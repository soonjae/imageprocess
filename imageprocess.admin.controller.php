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
        	$ipConfig->target_mid = str_replace('|@|',';', trim(Context::get('target_mid')));
		$ipConfig->noresizegroup = str_replace('|@|',';',trim(Context::get('noresizegroup')));
		if($ipConfig->resize_use != 'Y' && $ipConfig->watermark_use != 'Y') $ipConfig->original_store = 'N';       
 
		$oModuleController->insertModuleConfig('imageprocess', $ipConfig);
		
		return new Object(0,"success_updated");
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
		$ipConfig->water_quality = Context::get('water_quality');
		if(!$ipConfig->water_quality || $ipConfig->water_quality > 100) $ipConfig->water_quality = 100;
		$ipConfig->water_position = Context::get('water_position');
		$ipConfig->water_mid = str_replace('|@|',';',trim(Context::get('water_mid')));
		$ipConfig->ext = str_replace('|@|',';',trim(Context::get('ext')));
		$ipConfig->nowatergroup = str_replace('|@|',';',trim(Context::get('nowatergroup')));
		if(!$ipConfig->ext) $ipConfig->ext = 'jpg';

		if($ipConfig->watermark_use != 'Y') $ipConfig->watermark = '';
		elseif($ipConfig->watermark_use == 'Y') 
		{
			if(!$ipConfig->watermark) $ipConfig->watermark = './modules/imageprocess/stamp/stamp.png';
			if(!file_exists($ipConfig->watermark)) return new Object(-1, 'none_image');
			if(!preg_match("/\.png$/i", $ipConfig->watermark)) return new Object(-1, 'notpng');
		}
		if($ipConfig->resize_use != 'Y' && $ipConfig->watermark_use != 'Y') $ipConfig->original_store = 'N';

		$mids = explode(';',$ipConfig->water_mid);
        if(count($mids))
        {
            $watermark=array();
            $xmargin = array();
            $ymargin = array();
            $water_position = array();
            foreach($mids as $mid)
            {
				$watermark[$mid] =  trim(Context::get('watermark_'.$mid));
                $xmargin[$mid] = Context::get('xmargin_'.$mid);
                $ymargin[$mid] = Context::get('ymargin_'.$mid);
                $water_position[$mid] = Context::get('water_position_'.$mid);
            }
			$ipConfig->each_watermark = $watermark;
            $ipConfig->each_xmargin = $xmargin;
            $ipConfig->each_ymargin = $ymargin;
            $ipConfig->each_position = $water_position;
        }
        else $ipConfig->each_watermark = NULL;

		$oModuleController->insertModuleConfig('imageprocess', $ipConfig);
        return new Object(0,"success_updated");
    }


    function procImageprocessAdminOfileSetup() 
	{
        $oModuleController = &getController('module');
        $oModuleModel = &getModel('module');

		$ofolder = Context::get('store_path');
		if($ofolder && !$this->checkfolder($ofolder)) return new Object(-1, 'checkfolder');

		$ipConfig = $oModuleModel->getModuleConfig('imageprocess');

		$ipConfig->store_path =$ofolder;
		$ipConfig->original_store= Context::get('original_store');
		$ipConfig->store_mid = str_replace('|@|',';',trim(Context::get('store_mid')));
		$ipConfig->down_group = str_replace('|@|',';',trim(Context::get('down_group')));

        $oModuleController->insertModuleConfig('imageprocess', $ipConfig);

        return new Object(0,"success_updated");
    }

    function procImageprocessAdminEtcSetup() 
	{
       	$oModuleController = &getController('module');
        $oModuleModel = &getModel('module');
       	$ipConfig = $oModuleModel->getModuleConfig('imageprocess');

        $ipConfig->change_kfile= Context::get('change_kfile');
       	$ipConfig->magic_use = Context::get('magic_use');
		$ipConfig->rotate_use = Context::get('rotate_use');
		$ipConfig->magic_path = str_replace('\\','/',Context::get('magic_path'));
		$ipConfig->magic_conversion = Context::get('magic_conversion');
		$ipConfig->magic_target = Context::get('magic_target');
		if(ini_get('safe_mode') && $ipConfig->magic_use == 'Y') return new Object(-1, 'ip_safe_mode');
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
			if(!stripos($ver,'imagemagick')) return new Object(-1, 'check_magic_path');
			$ipConfig->magic_conversion = Context::get('magic_conversion');
			$ipConfig->original_format = Context::get('original_format');
			$ipConfig->target_format = Context::get('target_format');
			if(!$ipConfig->target_format) $ipConfig->target_format = 'jpg';
		}

		$oModuleController->insertModuleConfig('imageprocess', $ipConfig);

		return new Object(0,"success_updated");
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

        if ($oModuleModel->getModuleExtend('imageprocess','controller','')) $oModuleController->deleteModuleExtend('imageprocess', 'ipx', 'controller','');

        $ipConfig->logo_minimum_width = Context::get('logo_minimum_width');
        $ipConfig->textlogo = Context::get('textlogo');
        $ipConfig->logo_quality = Context::get('logo_quality');
        $ipConfig->exfont = Context::get('logo_font_type');
        $ipConfig->logo_point = Context::get('logo_point')?Context::get('logo_point'):$this->logo_point;
        $ipConfig->logo_style = Context::get('logo_style')?Context::get('logo_style'):$this->font_style;
        if(!$ipConfig->logo_quality || $ipConfig->logo_quality > 100) $ipConfig->logo_quality = 100;
        $ipConfig->logo_position = Context::get('logo_position');
        if(!$ipConfig->logo_position) $ipConfig->logo_position = 'south';
        $ipConfig->logo_mid = str_replace('|@|',';',trim(Context::get('logo_mid')));
		$ipConfig->nologogroup = str_replace('|@|',';',trim(Context::get('nologogroup')));

        $ipConfig->logo_ext = str_replace('|@|',';',trim(Context::get('logo_ext')));
        $fg = Context::get('logo_fg');
        $bg = Context::get('logo_bg');
        $ipConfig->logo_fg = $this->checkColor( $fg );
        $ipConfig->logo_bg = $this->checkColor( $bg );

        if(!$ipConfig->logo_ext) $ipConfig->logo_ext = 'jpg';
        $mids = explode(';',$ipConfig->logo_mid);
        if(count($mids))
        {
            $logo=array();
            $_fg = array();
            $_bg = array();
            $position = array();
            foreach($mids as $mid)
            {
                $each_logo =  NULL;
                $each_logo = trim(Context::get('logo_'.$mid));
                if($each_logo) $logo[$mid] = $each_logo;
                $_fg_mid = Context::get('fg_'.$mid);
                if(!$_fg_mid) $_fg_mid = $ipConfig->logo_fg;
                $_bg_mid = Context::get('bg_'.$mid);
                if(!$_bg_mid) $_bg_mid = $ipConfig->logo_bg;

                $_fg[$mid] = $this->checkColor($_fg_mid);
                $_bg[$mid] = $this->checkColor($_bg_mid);
                $position[$mid] = Context::get('position_'.$mid);
            }
            $ipConfig->each_logo = serialize($logo);
            $ipConfig->each_fg = serialize($_fg);
            $ipConfig->each_bg = serialize($_bg);
            $ipConfig->each_position = serialize($position);
        }
        else $ipConfig->each_logo = NULL;

        $oModuleController->insertModuleConfig('imageprocess', $ipConfig);
        return new Object(0,"success_updated");
    }

	function checkColor($color)
    {
        if(preg_match('/^#[a-f0-9]{6}$/i', $color))
        {
            return $color;
        }
        else
        return '#' . $color;
    }
}
/* End of file imageprocess.admin.controller.php */
/* Location: ./modules/imageprocess/imageprocess.admin.controller.php */
