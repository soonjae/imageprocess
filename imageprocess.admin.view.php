<?php
/**
 * @class  imageprocessAdminView
 * @author karma (http://www.wildgreen.co.kr)
 * @brief  imageprocess 모듈의 admin view 클래스
 **/

class imageprocessAdminView extends imageprocess 
{
	/**
	 * @brief 초기화
	 **/
	function init() 
	{
		$this->setTemplatePath($this->module_path.'tpl');
		$oModuleModel = &getModel('module');

		$this->imageprocess_info=$oModuleModel->getModuleConfig('imageprocess');
		$oMemberModel = &getModel('member');
		$this->group_list = $oMemberModel->getGroups();

	}

	/**
	* @brief 리사이즈 관리 페이지 보여줌
	**/
	function dispImageprocessAdminIndex() 
	{

		$oModuleModel = &getModel('module');
		$oImageprocessModel = &getModel('imageprocess');

		$imageprocess_info=$this->imageprocess_info;
		$target_mid=explode(";",$imageprocess_info->target_mid);
		$imageprocess_info->target_mid=$target_mid;
		$imageprocess_info->noresizegroup=explode(";",$imageprocess_info->noresizegroup);
		Context::set("imageprocess_info",$imageprocess_info);

		// mid 목록을 가져옴
		$oModuleModel = &getModel('module');
		$oModuleAdminModel = &getAdminModel('module');

		$mid_list = $oImageprocessModel->getMidList($args);

		// module_category와 module의 조합
		if(!$site_module_info->site_srl) 
		{
			// 모듈 카테고리 목록을 구함
			$module_categories = $oModuleModel->getModuleCategories();

			if($mid_list) 
			{
				foreach($mid_list as $module_srl => $module) 
				{
					$module_categories[$module->module_category_srl]->list[$module_srl] = $module;
				}
			}
		} 
		else 
		{
			$module_categories[0]->list = $mid_list;
		}

		Context::set('mid_list',$module_categories);
		Context::set('group_list', $this->group_list);

		// 템플릿 파일 지정
		$this->setTemplateFile('adminindex');
	}

    /**
     * @brief 워터마크 관리 페이지 보여줌
     **/
    function dispImageprocessAdminWatermark() 
    {

        $oModuleModel = &getModel('module');
        $imageprocess_info=$this->imageprocess_info;

        $water_mid=explode(";",$imageprocess_info->water_mid);
        $imageprocess_info->water_mid=$water_mid;
		$imageprocess_info->ext = explode(";",$imageprocess_info->ext);
		$imageprocess_info->nowatergroup=explode(";",$imageprocess_info->nowatergroup);
		Context::set("each_watermark",$imageprocess_info->each_watermark);
		Context::set("xmargin",$imageprocess_info->xmargin);
		Context::set("ymargin",$imageprocess_info->ymargin);
		Context::set("each_position",$imageprocess_info->each_position);
        Context::set("imageprocess_info",$imageprocess_info);

        // mid 목록을 가져옴
		$oImageprocessModel = &getModel('imageprocess');
		$mid_list = $oImageprocessModel->getMidList($args);

        // module_category와 module의 조합
        if(!$site_module_info->site_srl) 
		{
            // 모듈 카테고리 목록을 구함
            $module_categories = $oModuleModel->getModuleCategories();

            if($mid_list) 
			{
                foreach($mid_list as $module_srl => $module) 
				{
                    $module_categories[$module->module_category_srl]->list[$module_srl] = $module;
                }
            }
        } 
		else 
		{
            $module_categories[0]->list = $mid_list;
        }
	
	$stampList = $this->getStampList();	
	Context::set('stampList',$stampList);
	Context::set('mid_list',$module_categories);
	Context::set('group_list', $this->group_list);

	// 템플릿 파일 지정
	$this->setTemplateFile('watermark_setup');
    }
	
    /**
    * @brief 워터마크 관리 페이지 보여줌
    **/
    function dispImageprocessAdminOfile() 
	{
        $oModuleModel = &getModel('module');
		$imageprocess_info=$this->imageprocess_info;

        $store_mid=explode(";",$imageprocess_info->store_mid);
        $imageprocess_info->store_mid=$store_mid;
        $imageprocess_info->down_group=explode(";",$imageprocess_info->down_group);
		Context::set("imageprocess_info",$imageprocess_info);

		//$oMemberModel = &getModel('member');
        //$group_list = $oMemberModel->getGroups();
        Context::set('group_list', $this->group_list);

        // mid 목록을 가져옴
		$oImageprocessModel = &getModel('imageprocess');
		$mid_list = $oImageprocessModel->getMidList($args);

        // module_category와 module의 조합
        if(!$site_module_info->site_srl) 
		{
            // 모듈 카테고리 목록을 구함
            $module_categories = $oModuleModel->getModuleCategories();

            if($mid_list) 
			{
                foreach($mid_list as $module_srl => $module) 
				{
                    $module_categories[$module->module_category_srl]->list[$module_srl] = $module;
                }
            }
        } 
		else 
		{
            $module_categories[0]->list = $mid_list;
        }

        Context::set('mid_list',$module_categories);

        // 템플릿 파일 지정
        $this->setTemplateFile('ofile_setup');
    }

    /**
    * @brief 워터마크 관리 페이지 보여줌
    **/
    function dispImageprocessAdminEtc() 
	{
    	$EXIF = 1;
        if (!extension_loaded('exif')) $EXIF=0;
		COntext::set('EXIF',$EXIF);

		$oModuleModel = &getModel('module');
        $imageprocess_info=$oModuleModel->getModuleConfig('imageprocess');

		Context::set('image_types',$this->image_types);
		Context::set('imageprocess_info',$imageprocess_info);
		Context::set('magic_path',$this->checkMagicPath());

        // 템플릿 파일 지정
        $this->setTemplateFile('etc_setup');
    }

	/**
	* 이미지매직 프로그램 위치 확인
	**/
	function checkMagicPath() 
	{
		if (stripos(PHP_OS, 'WIN') === 0) 
		{
			$exe = array('identify.exe','convert.exe','composite.exe');
			$serverPath = explode(';',$_SERVER["PATH"]);
		}
		else 
		{
			$exe = array('identify','convert','composite');
			$serverPath = explode(':',$_SERVER["PATH"]);
		}
		foreach($serverPath as $key) 
		{
			if(@file_exists($key.'/'.$exe[0]) && @file_exists($key.'/'.$exe[1]) && @file_exists($key.'/'.$exe[2])) return $key.'/';
		}
	}

	/**
	* 워터마크 화일의 목록
	**/
	function getStampList()
    {
        $txt = fileHandler::readDir('./modules/imageprocess/stamp');
        $arr=array();
        foreach ($txt as $key)
        {
			if(strtolower(substr(strrchr($key,'.'),1)) != 'png') continue; //png 화일이 아니면 패쓰...
            $dir = './modules/imageprocess/stamp/'.$key;
            $arr[$key] = $dir;
        }
        ksort($arr);
        return $arr;
    }

	function dispImageprocessAdminTextlogo() 
	{
		$oModuleModel = &getModel('module');
		$imageprocess_info=$this->imageprocess_info;
	
		$logo_mid=explode(";",$imageprocess_info->logo_mid);
		$imageprocess_info->logo_mid=$logo_mid;
		$imageprocess_info->logo_ext = explode(";",$imageprocess_info->logo_ext);
		$imageprocess_info->nologogroup=explode(";",$imageprocess_info->nologogroup);
		Context::set("imageprocess_info",$imageprocess_info);
		Context::set("imageprocess_module_info",$this->imageprocess_module_info);
		Context::set('external',$this->getOutFont());
		Context::set('logo',unserialize($imageprocess_info->each_logo));
		Context::set('fg',unserialize($imageprocess_info->each_fg));
		Context::set('bg',unserialize($imageprocess_info->each_bg));
		Context::set('position',unserialize($imageprocess_info->each_position));
		$oImageprocessModel = &getModel('imageprocess');
		$mid_list = $oImageprocessModel->getMidList($args);

		if(!$site_module_info->site_srl) 
		{
				$module_categories = $oModuleModel->getModuleCategories();

				if($mid_list) 
				{
					foreach($mid_list as $module_srl => $module) 
				{
					$module_categories[$module->module_category_srl]->list[$module_srl] = $module;
				}
			}
		} 
		else 
		{
			$module_categories[0]->list = $mid_list;
		}
		Context::set('mid_list',$module_categories);
		Context::set('group_list', $this->group_list);

        $this->setTemplatePath($this->module_path.'tpl');
        $this->setTemplateFile('textlogo');
    }

	function getOutFont()
    {
        require_once(_XE_PATH_.'modules/imageprocess/ttfinfo.class.php');
        $txt = fileHandler::readDir('./modules/imageprocess/font');
        $arr=array();
        foreach ($txt as $key)
        {
            $fontinfo = getFontInfo('modules/imageprocess/font/'.$key);
            $fname = $fontinfo[18] ? detectUTF8($fontinfo[18],true):detectUTF8($fontinfo[4],true);
            if(!$fname) continue;
            $dir = _XE_PATH_.'modules/imageprocess/font/'.$key;
            $this->makeTextPng($dir);
            $arr[$fname] = $dir;
        }
        ksort($arr);
        return $arr;
    }

    function makeTextPng($font)
    {
        $info = pathinfo($font);
        $fn =  basename($font,'.'.$info['extension']);

        $path = './files/cache/imageprocess';
        if(!is_dir($path)) FileHandler::makeDir($path);
        $file = sprintf('%s/%s.png',$path,$fn);
        if(file_exists($file)) return;
        $fontsize = 15;
        $text = "한Aa國";
        $width = 65;
        $oImageprocessModel = &getModel('imageprocess');
        $tbox = $oImageprocessModel->calculateTextBox($text,$font,$fontsize,0);
        $height = 25;

        $image = imagecreatetruecolor($width,$height);

        $bgcolor = imagecolorallocate($image, 240, 240, 240);
        $fontcolor = imagecolorallocate($image, 0, 0, 0);

         imagefilledrectangle($image, 0, 0, $width, $height, $bgcolor);

        $x = ($width - $tbox["width"])/2 ;
        $y = $fontsize + ($height -$tbox["height"])/2+1;
        
        imagefttext($image, $fontsize, 0, $x, $y, $fontcolor, $font, $text);
        imagepng($image,$file);

        imagedestroy($image);
    }


}
/* End of file imageprocess.admin.view.php */
/* Location: ./modules/imageprocess/imageprocess.admin.view.php */
