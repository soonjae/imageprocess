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
		$raw_format = array('arw','raf','orf','crw','cr2','dng','pef','mrw','x3f','nef');
		
		@set_time_limit(0);
	
		$oModuleModel = &getModel('module');
		$ipConfig = $oModuleModel->getModuleConfig('imageprocess');
		$oImageprocessModel = &getModel('imageprocess');

		$target_mid=explode(";",$ipConfig->target_mid);
		$store_mid=explode(";",$ipConfig->store_mid);
		$water_mid=explode(";",$ipConfig->water_mid);
		$logo_mid=explode(";",$ipConfig->logo_mid);
		if(!$ipConfig->ext) $ipConfig->ext="jpg";
		if(!$ipConfig->logo_ext) $ipConfig->logo_ext="jpg";
		$ext_type ="/\.(".implode("|",explode(";",$ipConfig->ext)).")$/i";
		$logo_ext_type ="/\.(".implode("|",explode(";",$ipConfig->logo_ext)).")$/i";

		$module_info=$oModuleModel->getModuleInfoByModuleSrl($args->module_srl);
		$file_mid= $module_info->module_srl;
		$out_file_size = false;
		
		$file=$args->uploaded_filename;
		$ext = strtolower(substr(strrchr($args->source_filename,'.'),1));

//Image Rotate

		if($file && $ipConfig->rotate_use == 'Y' && preg_match('/\.(jpg|jpeg|gif|png)$/i', $file) ){
			$exif = exif_read_data($file);
	        if($exif['Orientation'] == '6' || $exif['Orientation'] == '3' || $exif['Orientation'] == '8') 
			{
				if($ipConfig->magic_use == 'Y') $oImageprocessModel->MagicRotate($file, $ipConfig->magic_path);
                else $oImageprocessModel->GDrotate($file,$ext);
			}
		}

		//Image Rotate 끝

		//여기부터 magic_conversion
		$or_format = explode('|@|',$ipConfig->original_format);
		if(in_array('tiff',$or_format)) {
			$o_format = array_merge($or_format,array('tif')); //tiff와 tif를 동시에...
			$or_format = $o_format;
		}
		if(in_array('raw',$or_format)) $original_format = array_merge($or_format,$raw_format);
		else $original_format = $or_format;

		if($ipConfig->magic_use == 'Y' && $ipConfig->magic_conversion =='Y' && in_array($ext,$original_format)) 
		{
			// 화일을 미리 ./files/attache/images 폴더로 이동...
			$file = $oImageprocessModel->getConversionName($args,$ipConfig->target_format);
			
			//이동된 화일이 있으면...
			if($file) 
			{
				if($ipConfig->resize_use == 'Y') $tarsize = $ipConfig->resize_width;
				else $tarsize = 1024;

				$target_file = $oImageprocessModel->magicConvert($file,$ipConfig->magic_path,$ipConfig->target_format,$ext,$tarsize);
				if(file_exists($target_file)) 
				{
					$file = str_ireplace($_SERVER[DOCUMENT_ROOT],'./',$target_file);
				}
				$args->uploaded_filename=$file;
				$args->file_srl=$args->file_srl;
				$args->direct_download = 'Y';
				$output=executeQuery('imageprocess.renameFileName', $args);
			}
		} 
		
		//여기부터 리사이즈
		list($width, $height,$type)=getimagesize($file);
		if($ipConfig->resize_use == 'Y' && preg_match('/\.(jpg|jpeg|gif|png)$/i', $file) &&  (!$ipConfig->target_mid || in_array($file_mid,$target_mid))) 
		{	
			if(!$type || $type>3) return; //1:GIF, 2:JPG, 3:PNG
			$target_size = $ipConfig->resize_width;

			$quality = $ipConfig->resize_quality;
			
			if($ipConfig->target_width == 'N' && $width>$target_size) 
			{
				$new_width = $target_size;
				$new_height=round($height*$target_size/$width);

				if($ipConfig->original_store=='Y' && (!$ipConfig->store_mid || in_array($file_mid,$store_mid))) 
				{
					$ofile=$oImageprocessModel->getOfile($file,$ipConfig->store_path);
					if(!file_exists($ofile)) FileHandler::copyFile($file,$ofile); //@copy($file,$ofile);
				}
				if($ipConfig->magic_use == 'Y') $oImageprocessModel->magicResize($file, $new_width, $new_height, $ipConfig);
				else $oImageprocessModel->createImageFile($file, $new_width, $new_height, $ipConfig);
			} 
			elseif ($ipConfig->target_width == 'Y' && ($width>$target_size || $height>$target_size)) 
			{
				if($width>$height) 
				{
					$new_width = $target_size;
					$new_height=round($height*$target_size/$width);
				} 
				else 
				{
					$new_height = $target_size;
					$new_width = round($width*$target_size/$height);
				}
				$quality = $ipConfig->resize_quality;
				if($ipConfig->original_store=='Y' && (!$ipConfig->store_mid || in_array($file_mid,$store_mid))) 
				{
					$ofile=$oImageprocessModel->getOfile($file,$ipConfig->store_path);
					if(!file_exists($ofile)) FileHandler::copyFile($file,$ofile); //@copy($file,$ofile);
				}
				if($ipConfig->magic_use == 'Y') $oImageprocessModel->magicResize($file, $new_width, $new_height, $ipConfig);
				else $oImageprocessModel->createImageFile($file, $new_width, $new_height, $ipConfig);
			} 
		}

		//여기부터 워터마크
		if($ipConfig->watermark_use == 'Y' && preg_match($ext_type, $file) &&  (!$ipConfig->water_mid ||in_array($file_mid,$water_mid))) 
		{	
			list($width, $height,$type)=getimagesize($file);
			if(!$type || $type > 3) return; //1:GIF, 2:JPG, 3:PNG
			if($ipConfig->minimum_width > $height || $ipConfig->minimum_width > $width) return;
			if($ipConfig->original_store == 'Y' && (!$ipConfig->store_mid || in_array($file_mid,$store_mid))) 
			{
				$ofile=$oImageprocessModel->getOfile($file,$ipConfig->store_path);
				if(!file_exists($ofile)) FileHandler::copyFile($file,$ofile); //@copy($file,$ofile);
			}
			if($ipConfig->magic_use == 'Y') $oImageprocessModel->magicWatermark($file,$ipConfig);
			else $oImageprocessModel->alphaWatermark($file,$ipConfig);
		}

        //여기부터 텍스트로고
        $logo_mid=explode(";",$ipConfig->logo_mid);
        if(!$ipConfig->logo_ext) $ipConfig->logo_ext="jpg";
        $logo_ext_type ="/\.(".implode("|",explode(";",$ipConfig->logo_ext)).")$/i";

        if($ipConfig->textlogo_use == 'Y' && preg_match($logo_ext_type, $file) &&  (!$ipConfig->logo_mid || in_array($file_mid,$logo_mid)))
        {
            $logo = unserialize($ipConfig->each_logo);
            if(isset($logo[$file_mid])) $ipConfig->textlogo = $logo[$file_mid];
            $position = unserialize($ipConfig->each_position);
            if(isset($position[$file_mid])) $ipConfig->logo_position = $position[$file_mid];
            $fg = unserialize($ipConfig->each_fg);
            if(isset($fg[$file_mid])) $ipConfig->logo_fg = $fg[$file_mid];
            $bg = unserialize($ipConfig->each_bg);
            if(isset($bg[$file_mid])) $ipConfig->logo_bg = $bg[$file_mid];


            list($width, $height,$type)=getimagesize($file);
            if(!$type || $type > 3) return; //1:GIF, 2:JPG, 3:PNG
            if($ipConfig->logo_minimum_width && ($ipConfig->logo_minimum_width > $height || $ipConfig->logo_minimum_width > $width)) return;

            if($ipConfig->original_store == 'Y' && (!$ipConfig->store_mid || in_array($file_mid,$store_mid)))
            {
                $ofile=$oImageprocessModel->getOfile($file,$ipConfig->store_path);
                if(!file_exists($ofile)) FileHandler::copyFile($file,$ofile); //@copy($file,$ofile);
            }
            if($ipConfig->magic_use == 'Y') $oImageprocessModel->magicTextLogo($file,$ipConfig);
            else $oImageprocessModel->alphaTextLogo($file,$ipConfig);
        }


		//화일사이즈
		if(preg_match("/\.(jpg|jpeg|gif|png)$/i", $file) && ($ipConfig->watermark_use == 'Y' || $ipConfig->resize_use == 'Y')) 
		{
			$obj->file_srl = $args->file_srl;
			$obj->file_size = filesize($file);
			$output = executeQuery('imageprocess.updateFileSize', $obj);
		}
		return;
	} //functiontriggerImsert


	function triggerDeleteFile(&$args) 
	{
		$oImageprocessModel = &getModel('imageprocess');
		$file = $args->uploaded_filename;
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
		if($ipConfig->original_store != 'Y') return;
		$output = $oImageprocessModel->deleteOFiles($args->document_srl,$ipConfig->store_path);
		return $output;
	}

	function triggerDeleteComment(&$args) 
	{
		$oImageprocessModel = &getModel('imageprocess');
		$oModuleModel = &getModel('module');
		$ipConfig = $oModuleModel->getModuleConfig('imageprocess');
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
				if (filesize($ofile) > 1024 * 1024) {
				    while(!feof($fp)) echo fread($fp, 1024);
				    fclose($fp);
				} else {
				    fpassthru($fp); 
				}
			   //2014년 1월 3일 원본파일 다운로드 안되는 문제 소스삽입 끝

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
