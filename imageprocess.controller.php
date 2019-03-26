<?php
/**
 * @class  imageprocessController
 * @author karma (http://www.wildgreen.co.kr)
 * @brief  imageprocess 모듈의 controller 클래스
 **/

class imageprocessController extends imageprocess {

	/**
	 * @brief 초기화
	 **/
	function init() 
	{
//		$GLOBALS['IMAGEPROCESSING'] = 0;
	}

	function triggerInsertFile(&$args) 
	{
		if(Context::get('act') == 'procDocumentManageCheckedDocument') return;

		if($GLOBALS['IMAGEPROCESSING'] == 'true') 
		{
			return;
		}
		@set_time_limit(0);

		$oModuleModel = &getModel('module');
		$ipConfig = $oModuleModel->getModuleConfig('imageprocess');
		$oImageprocessModel = &getModel('imageprocess');
		$ipConfig->fileargs = $args;
                $file=$args->uploaded_filename;
		$module_info=$oModuleModel->getModuleInfoByModuleSrl($args->module_srl);
                $file_mid= $module_info->module_srl;

		$ipConfig->target_mid=explode(";",$ipConfig->target_mid);
		$ipConfig->store_mid=explode(";",$ipConfig->store_mid);
		$ipConfig->water_mid=explode(";",$ipConfig->water_mid);
		$ipConfig->logo_mid=explode(";",$ipConfig->logo_mid);
		$logo = unserialize($ipConfig->each_logo);
		if(isset($logo[$file_mid])) $ipConfig->textlogo = $logo[$file_mid];
		$position = unserialize($ipConfig->each_text_position);
		if(isset($position[$file_mid])) $ipConfig->logo_position = $position[$file_mid];
		$fg = unserialize($ipConfig->each_fg);
		if(isset($fg[$file_mid])) $ipConfig->logo_fg = $fg[$file_mid];
		$bg = unserialize($ipConfig->each_bg);
		if(isset($bg[$file_mid])) $ipConfig->logo_bg = $bg[$file_mid];
		if($ipConfig->each_watermark[$file_mid]) $ipConfig->watermark = $ipConfig->each_watermark[$file_mid];
		if($ipConfig->each_xmargin[$file_mid]) $ipConfig->xmargin = $ipConfig->each_xmargin[$file_mid];
		if($ipConfig->each_ymargin[$file_mid]) $ipConfig->ymargin = $ipConfig->each_ymargin[$file_mid];
		if($ipConfig->each_water_position[$file_mid]) $ipConfig->water_position = $ipConfig->each_water_position[$file_mid];
		if(!$ipConfig->ext) $ipConfig->ext="jpg";
		if(!$ipConfig->resize_ext) $ipConfig->resize_ext="jpg";
		if(!$ipConfig->logo_ext) $ipConfig->logo_ext="jpg";
		$ipConfig->ext_type ="/\.(".implode("|",explode(";",$ipConfig->ext)).")$/i";
		$ipConfig->logo_ext_type ="/\.(".implode("|",explode(";",$ipConfig->logo_ext)).")$/i";
		$ipConfig->resize_type ="/\.(".implode("|",explode(";",$ipConfig->resize_ext)).")$/i";

		$ipConfig->fileargs = $args;
		$file=$args->uploaded_filename;

		if($ipConfig->magic_use == 'Y') $this->MagicProcess($file, $ipConfig);
		elseif($ipConfig->magic_use == 'I') $this->ImagickProcess($file,$ipConfig);
		else $this->GDProcess($file,$ipConfig);	
		
		if(preg_match("/\.(jpg|jpeg|gif|png)$/i", $file) && ($ipConfig->watermark_use == 'Y' || $ipConfig->resize_use == 'Y' || $ipConfig->textlogo_use == 'Y')) 
		{
			$this->updatefileszie($file,$args->file_srl);
		}
		return;
	}
	
	
	function MagicProcess($file, $ipConfig)
	{
		if($ipConfig->magic_use != 'Y') return;
		$oModuleModel = &getModel('module');
              	$module_info=$oModuleModel->getModuleInfoByModuleSrl($ipConfig->fileargs->module_srl);
                $file_mid= $module_info->module_srl;
		$oImageprocessModel = &getModel('imageprocess');
		list($original_width, $original_height, $orginal_type) = getimagesize($file);

		if($file && $ipConfig->rotate_use == 'Y' && preg_match('/\.(jpg|jpeg|gif|png)$/i', $file) )
                {
                        $exif = @exif_read_data($file);
                        if($exif['Orientation'] == '6' || $exif['Orientation'] == '3' || $exif['Orientation'] == '8')
                        {
				$oImageprocessModel->magicRotate($file, $ipConfig->magic_path);
                        }
                }

		if($ipConfig->original_store=='Y' && in_array($file_mid,$ipConfig->store_mid))
                {
                        $ofile=$oImageprocessModel->getOfile($file,$ipConfig->store_path);
                        if(!file_exists($ofile)) FileHandler::copyFile($file,$ofile);
                }
	
		if($ipConfig->resize_use == 'Y' && preg_match($ipConfig->resize_type, $file) &&  in_array($file_mid,$ipConfig->target_mid) && ($original_width > $ipConfig->resize_width || $original_height > $ipConfig->resize_width  ))
                {
                        if($oImageprocessModel->checkGroup($ipConfig->noresizegroup))
                        {
                                $newSize = $this->getNewsize($file, $ipConfig);
                                if($newSize) $oImageprocessModel->magicResize($file, $newSize->width, $newSize->height, $ipConfig);
                        }
                }
                //여기부터 워터마크
		if($ipConfig->watermark_use == 'Y' && preg_match($ipConfig->ext_type, $file) &&  in_array($file_mid,$ipConfig->water_mid) && $original_width > $ipConfig->minimum_width && $original_height > $ipConfig->minimum_width )
                {
                        if($oImageprocessModel->checkGroup($ipConfig->nowatergroup))
                        {
                                $oImageprocessModel->magicWatermark($file,$ipConfig);
                        }
                }
                //여기부터 텍스트로고
		if($ipConfig->textlogo_use == 'Y' && preg_match($ipConfig->logo_ext_type, $file) &&  in_array($file_mid,$ipConfig->logo_mid) && $original_width > $ipConfig->logo_minimum_width && $original_height > $ipConfig->logo_minimum_width )
                {
                        if($oImageprocessModel->checkGroup($ipConfig->nologogroup))
                        {
                                $oImageprocessModel->magicTextLogo($file,$ipConfig);
                        }
                }
	} //function MagicProcess

	function ImagickProcess($file, $ipConfig)
	{
		if($ipConfig->magic_use != 'I') return;
                $oModuleModel = &getModel('module');
                $module_info=$oModuleModel->getModuleInfoByModuleSrl($ipConfig->fileargs->module_srl);
                $file_mid= $module_info->module_srl;
		$oImageprocessModel = &getModel('imageprocess');
		$oImageprocessModel->imagickdo($file, $ipConfig);
        } //function ImagickProcess


	function getNewsize($file, $ipConfig)
	{
		list($width, $height,$type)=getimagesize($file);
		if(!$type || $type>3) return; //1:GIF, 2:JPG, 3:PNG
		$target_size = $ipConfig->resize_width;
		if($height <= $ipConfig->minimum_width || $width <= $ipConfig->minimum_width) return false;
		if($height <= $target_size && $width <= $target_size) return false;
		$obj = new stdClass;
		if($ipConfig->target_width == 'N' && $width>$target_size)
		{
			$obj->width = $target_size;
			$obj->height = round($height*$target_size/$width);
		}
		elseif ($ipConfig->target_width == 'Y' && ($width>$target_size || $height>$target_size))
		{
			if($width>$height)
			{
				$obj->width = $target_size;
				$obj->height = round($height*$target_size/$width);
			}
			else
			{
				$obj->height = $target_size;
				$obj->width = round($width*$target_size/$height);
			}
		}
		return $obj;
	}

	function GDProcess($file, $ipConfig)
	{
		if($ipConfig->magic_use == 'I' || $ipConfig->magic_use == 'Y') return;
                $oModuleModel = &getModel('module');
                $module_info=$oModuleModel->getModuleInfoByModuleSrl($ipConfig->fileargs->module_srl);
                $file_mid= $module_info->module_srl;
		$oImageprocessModel = &getModel('imageprocess');
		list($original_width, $original_height, $orginal_type) = getimagesize($file);

//		$oImageprocessModel->GDdo($file,$ipConfig);
		
		if($file && $ipConfig->rotate_use == 'Y' && preg_match('/\.(jpg|jpeg|gif|png)$/i', $file) )
                {
                        $exif = @exif_read_data($file);
                        if($exif['Orientation'] == '6' || $exif['Orientation'] == '3' || $exif['Orientation'] == '8')
                        {
                                $oImageprocessModel->GDrotate($file);
                        }
                }
                if($ipConfig->original_store == 'Y' && in_array($file_mid,$ipConfig->store_mid))
		{
                        $ofile=$oImageprocessModel->getOfile($file,$ipConfig->store_path);
                        if(!file_exists($ofile)) FileHandler::copyFile($file,$ofile); //@copy($file,$ofile);
                }
		//여기부터 리사이즈
                if($ipConfig->resize_use == 'Y' && preg_match($ipConfig->resize_type, $file) &&  in_array($file_mid,$ipConfig->target_mid) && ($original_width > $ipConfig->resize_width || $original_height > $ipConfig->resize_width  ))
                {
                        if($oImageprocessModel->checkGroup($ipConfig->noresizegroup))
                        {
                                $newSize = $this->getNewsize($file, $ipConfig);
                                if($newSize) $oImageprocessModel->GDResize($file, $newSize->width, $newSize->height, $ipConfig);
                        }
                }
                //여기부터 워터마크
                if($ipConfig->watermark_use == 'Y' && preg_match($ipConfig->ext_type, $file) &&  in_array($file_mid,$ipConfig->water_mid) && $original_width > $ipConfig->minimum_width && $original_height > $ipConfig->minimum_width )
                {
                        if($oImageprocessModel->checkGroup($ipConfig->nowatergroup))
                        {
                                $oImageprocessModel->GDWatermark($file,$ipConfig);
                        }
                }
                //여기부터 텍스트로고
                if($ipConfig->textlogo_use == 'Y' && preg_match($ipConfig->logo_ext_type, $file) &&  in_array($file_mid,$ipConfig->logo_mid) && $original_width > $ipConfig->logo_minimum_width && $original_height > $ipConfig->logo_minimum_width )
                {
                        if($oImageprocessModel->checkGroup($ipConfig->nologogroup))
                        {
                                $oImageprocessModel->GDTextLogo($file,$ipConfig);
                        }
                }
        } //function GDProcess

	function updatefileszie($file,$file_srl)
	{
		$args = new stdclass;
		$args->file_srl = $file_srl;
		$args->file_size = filesize($file);
		$output = executeQuery('imageprocess.updateFileSize', $args);
	}
	

	function triggerDeleteFile(&$args) 
	{
		$oImageprocessModel = &getModel('imageprocess');
		$file = $args->uploaded_filename;
		$obj = new stdClass;
		$obj->file_srl = $args->file_srl;
		$oImageprocessModel->deleteEXIF($obj);

		$oModuleModel = &getModel('module');
		$ipConfig = $oModuleModel->getModuleConfig('imageprocess');

		$ext = strrchr($file,'.');
		$fn = dirname($file).'/'.basename($file,$ext);
		if(file_exists($fn)) FileHandler::removeFile($fn);
		$ofile = $oImageprocessModel->checkOfile($file,$ipConfig->store_path);
		if(file_exists($ofile)) 
		{
			FileHandler::removeFile($ofile); //unlink($ofile);
			$path = $oImageprocessModel->getFolder($ofile);
			FileHandler::removeBlankDir($path);
		}
		return;
	}

	/**
	* 화일 이동시...
	**/
	function triggerMoveDocument(&$args)
	{
		if(!$args->document_srls) return;
	        $GLOBALS['IMAGEPROCESSING']= 'true';
		$oImageprocessModel = &getModel('imageprocess');
		$oModuleModel = &getModel('module');
		$ipConfig = $oModuleModel->getModuleConfig('imageprocess');
		$oDocumentModel = &getModel('document');
		$document_srl_list = explode(',',$args->document_srls);

		for($i=count($document_srl_list)-1;$i>=0;$i--) {
			$document_srl = $document_srl_list[$i];
			$oDocument = $oDocumentModel->getDocument($document_srl);
			if(!$oDocument->isExists()) continue;
			unset($obj);
			$obj = $oDocument->getObjectVars();
			if($module_srl != $obj->module_srl && $oDocument->hasUploadedFiles()) {
				$files = $oDocument->getUploadedFiles();
				if(is_array($files))
				{
					foreach($files as $key => $val)
					{
						$_file = array();
						$_file = $val->uploaded_filename;
						$ofile = $oImageprocessModel->checkOfile($val->uploaded_filename,$ipConfig->store_path);
						if(!file_exists($ofile)) continue;
						FileHandler::moveFile($ofile,$_file);
					}
				}
			}
		}
		return $args;
	}

	/**
	* 문서 삭제
	**/
	function triggerDeleteDocument(&$args) 
	{
		$oImageprocessModel = &getModel('imageprocess');
		$oModuleModel = &getModel('module');
		$ipConfig = $oModuleModel->getModuleConfig('imageprocess');

		$obj = new stdClass;
                $obj->target_srl = $args->document_srl;
                $oImageprocessModel->deleteEXIF($obj);

		if($ipConfig->original_store != 'Y') return;
		$output = $oImageprocessModel->deleteOFiles($args->document_srl,$ipConfig->store_path);
		return $output;
	}

	function triggerDeleteComment(&$args) 
	{
		$oImageprocessModel = &getModel('imageprocess');
		$oModuleModel = &getModel('module');
		$ipConfig = $oModuleModel->getModuleConfig('imageprocess');

		$obj = new stdClass;
                $obj->target_srl = $args->comment_srl;
                $oImageprocessModel->deleteEXIF($obj);

		if($ipConfig->original_store != 'Y') return;
		$output = $oImageprocessModel->deleteOFiles($args->comment_srl,$ipConfig->store_path);
		return $output;
	}

	function triggerDownloadFile(&$args) 
	{

		$oImageprocessModel = &getModel('imageprocess');
		$oModuleModel = &getModel('module');
		$ipConfig = $oModuleModel->getModuleConfig('imageprocess');
		$down_group=explode(';',$ipConfig->down_group);
		$file_obj=$args;
		$file_size = @filesize($args->uploaded_filename); //becuase of filesize bug
		if($file_size) $file_obj->file_size = $file_size;
		$ofile = $oImageprocessModel->checkOfile($args->uploaded_filename,$ipConfig->store_path);
		if(file_exists($ofile)) 
		{
			$obj->member_srl = $args->member_srl;
			$obj->down_group = $down_group;
			if($oImageprocessModel->getGrantDown($obj)) 
			{
				$file_obj->uploaded_filename = $ofile;
				$file_obj->file_size = filesize($ofile);

				//2014년 1월 3일 원본파일 다운로드 안되는 문제  소스 삽입 시작
				$filename = $file_obj->source_filename;
	            		if(!file_exists($ofile)) return $this->stop('msg_file_not_found');
    	        		$fp = fopen($ofile, 'rb');
        	    		if(!$fp) return $this->stop('msg_file_not_found');
            			header("Cache-Control: "); 
	            		header("Pragma: "); 
    	        		header("Content-Type: application/octet-stream"); 
        	    		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            			header("Content-Length: " .(string)($file_obj->file_size)); 
	            		header('Content-Disposition: attachment; filename="'.$filename.'"'); 
    	        		header("Content-Transfer-Encoding: binary\n");
			   	// if file size is lager than 10MB, use fread function (#18675748)
				if (filesize($ofile) > 1024 * 1024) 
				{
				    	while(!feof($fp)) echo fread($fp, 1024);
				    	fclose($fp);
				} 
				else 
				{
				    fpassthru($fp); 
				}//2014년 1월 3일 원본파일 다운로드 안되는 문제 소스삽입 끝
			}
		}
		elseif($oImageprocessModel->checkConvertedFile($file_obj))
		{
			$file_obj->uploaded_filename = $oImageprocessModel->checkConvertedFile($file_obj);
			$file_obj->file_size = filesize($oImageprocessModel->checkConvertedFile($file_obj));
		}

		return $file_obj;
	}

} 
/* End of file imageprocess.controller.php */
/* Location: ./modules/imageprocess/imageprocess.controller.php */
