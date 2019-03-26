<?php
/**
 * @class  imageprocessAdminController
 * @author karma (http://www.wildgreen.co.kr)
 * @brief imageprocess 모듈의 admin controller 클래스
 **/

class imageprocessAdminController extends imageprocess 
{

	/**
	 * @brief 초기화
	 **/
	function init() 
	{
	}

	function procImageprocessAdminSetup() 
	{
		$oModuleController = &getController('module');
		$oModuleModel = &getModel('module');
		$ipConfig = $oModuleModel->getModuleConfig('imageprocess');

		$ipConfig->resize_use = Context::get('resize_use');
		$ipConfig->resize_width = Context::get('resize_width');
		$ipConfig->target_width = Context::get('target_width');
		$ipConfig->resize_quality = Context::get('resize_quality');
		if($ipConfig->resize_quality >100) $ipConfig->resize_quality = 100;
		elseif(!$ipConfig->resize_quality) $ipConfig->resize_quality = 80;
		$ipConfig->resize_ext = str_replace('|@|',';',trim(Context::get('resize_ext')));
		$ipConfig->noresizegroup = str_replace('|@|',';',trim(Context::get('noresizegroup')));
		if($ipConfig->resize_use != 'Y' && $ipConfig->watermark_use != 'Y') $ipConfig->original_store = 'N';       
 
		$oModuleController->insertModuleConfig('imageprocess', $ipConfig);
		if (class_exists('BaseObject')) return new BaseObject(0,"success_updated");
                else return new Object(0,"success_updated");
	}


    	function procImageprocessAdminWatermarkSetup() 
	{
		$oModuleController = &getController('module');
		$oModuleModel = &getModel('module');
		$ipConfig = $oModuleModel->getModuleConfig('imageprocess');

		$ipConfig->watermark_use = Context::get('watermark_use');
		$ipConfig->minimum_width = Context::get('minimum_width');
		$ipConfig->watermark = Context::get('watermark');
		$ipConfig->xmargin = Context::get('xmargin');
		if($ipConfig->xmargin == '') $ipConfig->xmargin = 10;
		$ipConfig->ymargin = Context::get('ymargin');
		if($ipConfig->ymargin == '') $ipConfig->ymargin = 10;
		$ipConfig->water_position = Context::get('water_position');
		$ipConfig->ext = str_replace('|@|',';',trim(Context::get('ext')));
		$ipConfig->nowatergroup = str_replace('|@|',';',trim(Context::get('nowatergroup')));
		if(!$ipConfig->ext) $ipConfig->ext = 'jpg';

		if($ipConfig->watermark_use != 'Y') $ipConfig->watermark = '';
		elseif($ipConfig->watermark_use == 'Y') 
		{
                        if(!$ipConfig->watermark) $ipConfig->watermark = './modules/imageprocess/stamp/stamp.png';
                        if(!file_exists($ipConfig->watermark))
                        {
                                if (class_exists('BaseObject')) return new BaseObject(-1, 'none_image');
                                else  return new Object(-1, 'none_image');
                        }
                        if(!preg_match("/\.png$/i", $ipConfig->watermark))
                        {
                                if (class_exists('BaseObject')) return new BaseObject(-1, 'notpng');
                                else  return new Object(-1, 'notpng');
                        }
		}

		$oModuleController->insertModuleConfig('imageprocess', $ipConfig);
        	if (class_exists('BaseObject')) return new BaseObject(0,"success_updated");
                else return new Object(0,"success_updated");
    	}


    	function procImageprocessAdminOfileSetup() 
	{
        	$oModuleController = &getController('module');
        	$oModuleModel = &getModel('module');

		$ofolder = Context::get('store_path');
		if($ofolder && !$this->checkfolder($ofolder))
                {
                        if (class_exists('BaseObject')) return new BaseObject(-1, 'checkfolder');
                        else return new Object(-1, 'checkfolder');
                }

		$ipConfig = $oModuleModel->getModuleConfig('imageprocess');

		$ipConfig->store_path =$ofolder;
		$ipConfig->original_store= Context::get('original_store');
		$ipConfig->down_group = str_replace('|@|',';',trim(Context::get('down_group')));

        	$oModuleController->insertModuleConfig('imageprocess', $ipConfig);

        	if (class_exists('BaseObject')) return new BaseObject(0,"success_updated");
        	else return new Object(0,"success_updated");
    	}

    	function procImageprocessAdminEtcSetup() 
	{
       		$oModuleController = &getController('module');
	        $oModuleModel = &getModel('module');
       		$ipConfig = $oModuleModel->getModuleConfig('imageprocess');

       		$ipConfig->magic_use = Context::get('magic_use');
		$ipConfig->rotate_use = Context::get('rotate_use');
		$ipConfig->magic_path = str_replace('\\','/',Context::get('magic_path'));
		$ipConfig->magic_conversion = Context::get('magic_conversion');
		$ipConfig->magic_target = Context::get('magic_target');
		if(ini_get('safe_mode') && $ipConfig->magic_use == 'Y')
                {
                        if (class_exists('BaseObject')) return new BaseObject(-1, 'ip_safe_mode');
                        else return new Object(-1, 'ip_safe_mode');
                }
		elseif($ipConfig->magic_use == 'I' )
		{
			$ipConfig->exif_del = Context::get('exif_del');
			$ipConfig->magic_conversion = Context::get('magic_conversion');
                        $ipConfig->original_format = Context::get('original_format');
                        $ipConfig->target_format = Context::get('target_format');
                        if(!$ipConfig->target_format) $ipConfig->target_format = 'jpg';

		}
		elseif($ipConfig->magic_use == 'Y' ) 
		{
			if($ipConfig->magic_path && !preg_match('/\/$/',$ipConfig->magic_path)) $ipConfig->magic_path .= '/';
			$command =$ipConfig->magic_path."identify -version";
			if (stripos(PHP_OS, 'WIN') === 0) 
			{ 
				$magic_path = str_replace('/','\\',$ipConfig->magic_path);
				$command = "\"".$magic_path."convert\" -version";
				$ver = shell_exec($command);
			} 
			else 	$ver = shell_exec($command);
			if(!stripos($ver,'imagemagick'))
                        {
                                if (class_exists('BaseObject')) return new BaseObject(-1,  'check_magic_path');
                                else return new Object(-1, 'check_magic_path');
                        }
			$ipConfig->magic_conversion = Context::get('magic_conversion');
			$ipConfig->original_format = Context::get('original_format');
			$ipConfig->target_format = Context::get('target_format');
			if(!$ipConfig->target_format) $ipConfig->target_format = 'jpg';
		}

		$oModuleController->insertModuleConfig('imageprocess', $ipConfig);

		if (class_exists('BaseObject')) return new BaseObject(0,"success_updated");
                else return new Object(0,"success_updated");
	}

	function checkfolder($dir) 
	{
		if(!is_dir($dir)) return false;
		// permission 체크
        if(is_writable($dir)) return true;
        else return false;
	}

	function procImageprocessAdminTextlogo() {
		$oModuleModel = &getModel('module');
	        $oModuleController = &getController('module');
        	$ipConfig = $oModuleModel->getModuleConfig('imageprocess');

	        $ipConfig->textlogo_use = Context::get('textlogo_use');

	        $ipConfig->logo_minimum_width = Context::get('logo_minimum_width');
        	$ipConfig->textlogo = Context::get('textlogo');
        	$ipConfig->exfont = Context::get('logo_font_type');
	        $ipConfig->logo_point = Context::get('logo_point')?Context::get('logo_point'):$this->logo_point;
        	$ipConfig->logo_style = Context::get('logo_style')?Context::get('logo_style'):$this->font_style;
        	$ipConfig->logo_position = Context::get('logo_position');
	        if(!$ipConfig->logo_position) $ipConfig->logo_position = 'south';
		$ipConfig->nologogroup = str_replace('|@|',';',trim(Context::get('nologogroup')));

	        $ipConfig->logo_ext = str_replace('|@|',';',trim(Context::get('logo_ext')));
        	$fg = Context::get('logo_fg');
	        $bg = Context::get('logo_bg');
        	$ipConfig->logo_fg = $this->checkColor( $fg );
	        $ipConfig->logo_bg = $this->checkColor( $bg );

        	if(!$ipConfig->logo_ext) $ipConfig->logo_ext = 'jpg';

        	$oModuleController->insertModuleConfig('imageprocess', $ipConfig);
	        if (class_exists('BaseObject')) return new BaseObject(0,"success_updated");
        	else return new Object(0,"success_updated");
    	}

	function checkColor($color)
    	{
        	if(preg_match('/^#[a-f0-9]{6}$/i', $color))
        	{
            		return $color;
        	}
        	else	return '#' . $color;
    	}
	
	function procImageprocessAdminInsertModuleConfig()
	{
		$oModuleController = &getController('module');
		$oModuleModel = &getModel('module');
                $ipConfig = $oModuleModel->getModuleConfig('imageprocess');
		$var = Context::getRequestVars();
		$target_module_srl = $var->target_module_srl;
		if($ipConfig->resize_use == 'Y')
		{	
			$target_mid = explode(';',$ipConfig->target_mid);	
			if($var->resize_use == 'Y')
			{
				if(!in_array($target_module_srl, $target_mid)) array_push($target_mid, $target_module_srl);
			} 
			else 
			{
				if(in_array($target_module_srl, $target_mid)) $target_mid = array_diff($target_mid, $target_module_srl);
			}
			$ipConfig->target_mid = implode(';',$target_mid);
		}
		if($ipConfig->watermark_use == 'Y')
                {
			$water_mid = explode(';',$ipConfig->water_mid);
			if($var->watermark_use == 'Y')
			{
				if(!in_array($target_module_srl, $water_mid)) array_push($water_mid, $target_module_srl);
				$ipConfig->each_watermark[$target_module_srl] = $var->watermark;
				$ipConfig->each_xmargin[$target_module_srl] = $var->xmargin;
				$ipConfig->each_ymargin[$target_module_srl] = $var->ymargin;
				$ipConfig->each_water_position[$target_module_srl] = $var->water_position;
			}
			else
			{
				if(in_array($target_module_srl, $water_mid)) $water_mid = array_diff($water_mid, $target_module_srl);
			}
			$ipConfig->water_mid = implode(';',$water_mid);
                }
		if($ipConfig->textlogo_use == 'Y')
                {
			$logo_mid = explode(';',$ipConfig->logo_mid);
                        if($var->textlogo_use == 'Y')
                        {
                                if(!in_array($target_module_srl, $logo_mid)) array_push($logo_mid, $target_module_srl);
				$each_text_position = unserialize($ipConfig->each_text_position);
				$each_text_position[$target_module_srl] = $var->position;
				$ipConfig->each_text_position = serialize($each_text_position);
				$each_logo = unserialize($ipConfig->each_logo);
				$each_logo[$target_module_srl] = $var->logo;
				$ipConfig->each_logo = serialize($each_logo);
				$each_fg = unserialize($ipConfig->each_fg);
				$each_fg[$target_module_srl] =  $this->checkColor($var->fg);
				$ipConfig->each_fg = serialize($each_fg);
				$each_bg = unserialize($ipConfig->each_bg);
				$each_bg[$target_module_srl] =  $this->checkColor($var->bg);
				$ipConfig->each_bg = serialize($each_bg);
                        }
                        else
                        {
                                if(in_array($target_module_srl, $logo_mid)) $logo_mid = array_diff($logo_mid, $target_module_srl);
                        }
                        $ipConfig->logo_mid = implode(';',$logo_mid);

                }
		if($ipConfig->original_store == 'Y')
                {
			$store_mid = explode(';',$ipConfig->store_mid);
			if( $var->ofile_use == 'Y')
			{
                        	if(!in_array($target_module_srl, $store_mid)) array_push($store_mid, $target_module_srl);
			}
			else
			{
				if(in_array($target_module_srl, $store_mid)) $store_mid = array_diff($store_mid, $target_module_srl);
			}
                        $ipConfig->store_mid = implode(';',$store_mid);
                }
		$oModuleController->insertModuleConfig('imageprocess', $ipConfig);

		$this->setError(-1);
                $this->setMessage('success_updated', 'info');

                $returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispBoardAdminContent');
                $this->setRedirectUrl($returnUrl);

	}
}
/* End of file imageprocess.admin.controller.php */
/* Location: ./modules/imageprocess/imageprocess.admin.controller.php */
