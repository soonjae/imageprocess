<?php
/**
 * @class  imageprocess class
 * @author karma (http://www.wildgreen.co.kr)
 * @brief  imageprocess 모듈의 클래스
 */

class imageprocess extends ModuleObject 
{
	/**
	 * @brief 설치시 추가 작업이 필요할시 구현
	 **/ 
	function moduleInstall() 
	{
		return new Object();
	}
	
	/**
	 * @brief 설치가 이상이 없는지 체크하는 method
	 **/
	function checkUpdate() 
	{
		$oModuleModel = &getModel('module');

		//설정파일
		$ipConfig = $oModuleModel->getModuleConfig('imageprocess');
		$module_info = $oModuleModel->getModuleInfoXml('imageprocess');
		if(!$ipConfig->version || ($ipConfig->version != $module_info->version)) return true;
		if(!$oModuleModel->getTrigger('file.insertFile', 'imageprocess', 'controller', 'triggerInsertFile', 'after')) return true;
        if(!$oModuleModel->getTrigger('file.downloadFile', 'imageprocess', 'controller', 'triggerDownloadFile', 'before')) return true;
        if(!$oModuleModel->getTrigger('file.deleteFile', 'imageprocess', 'controller', 'triggerDeleteFile', 'after')) return true;
        if(!$oModuleModel->getTrigger('comment.deleteComment', 'imageprocess', 'controller', 'triggerDeleteComment', 'before')) return true;
        if(!$oModuleModel->getTrigger('document.deleteDocument', 'imageprocess', 'controller', 'triggerDeleteDocument', 'before')) return true;
		if(!$oModuleModel->getTrigger('document.moveDocumentModule','imageprocess', 'controller', 'triggerMoveDocument', 'before')) return true;
		if (!extension_loaded('exif') && $ipConfig->rotate_use == 'Y') return true; 
		return false;
	}

	/**
	 * @brief 업데이트 실행
	 **/
	function moduleUpdate() 
	{
		$oModuleController = &getController('module');
		$oModuleModel = &getModel('module');
		$oldconfig = $oModuleModel->getModuleConfig('imageprocess');

		$module_info = $oModuleModel->getModuleInfoXml('imageprocess');

		if($oldconfig && $oldconfig->version < '0.8.3' ) 
		{
			$ipConfig = $oldconfig;
			if($ipConfig->store_mid) $ipConfig->store_mid = implode(";",$oModuleModel->getModuleSrlByMid(explode(";",$ipConfig->store_mid)));
			if($ipConfig->water_mid) $ipConfig->water_mid = implode(";",$oModuleModel->getModuleSrlByMid(explode(";",$ipConfig->water_mid)));
			if($ipConfig->target_mid) $ipConfig->target_mid = implode(";",$oModuleModel->getModuleSrlByMid(explode(";",$ipConfig->target_mid)));
			unset($ipConfig->kfile_mid);
		} 
		elseif($oldconfig && $oldconfig->version < '0.8.6' ) 
		{
			$ipConfig = $oldconfig;
			$ipConfig->ext ='jpg;gif;png';
		} 
		elseif($oldconfig && $oldconfig->version < '0.9.0' ) 
		{
            $ipConfig = $oldconfig;
            $ipConfig->water_quality = '100';
        }
		elseif($oldconfig) 
		{
			$ipConfig = $oldconfig;
		}
		else 
		{
			$ipConfig->resize_use = 'N';
			$ipConfig->resize_width = '760';
			$ipConfig->original_store ='N';
			$ipConfig->resize_quality = '80';
			$ipConfig->minimum_width = '300';
			$ipConfig->watermark_use = 'N';
			$ipConfig->magic_use = 'N';
			$ipConfig->watermark = '';
			$ipConfig->water_quality = '100';
			$ipConfig->water_position ='RB';
			$ipConfig->store_mid = '';
			$ipConfig->water_mid = '';
			$ipConfig->target_mid = '';
			$ipConfig->store_path = '';
			$ipConfig->down_group = '';
			$ipConfig->xmargin = 10;
			$ipConfig->ymargin = 10; 
			$ipConfig->ext = 'jpg;png';
			$ipConfig->logo_ext = 'jpg;png';
		}

		if (!extension_loaded('exif') && $oldconfig->rotate_use == 'Y')
             $ipConfig->rotate_use = 'N';

		$ipConfig->version = $module_info->version;
		$oModuleController->insertModuleConfig('imageprocess', $ipConfig);
		
		if(!$oModuleModel->getTrigger('file.insertFile', 'imageprocess', 'controller', 'triggerInsertFile', 'after'))
		$oModuleController->insertTrigger('file.insertFile', 'imageprocess', 'controller', 'triggerInsertFile', 'after');

        if(!$oModuleModel->getTrigger('file.downloadFile', 'imageprocess', 'controller', 'triggerDownloadFile', 'before'))
        $oModuleController->insertTrigger('file.downloadFile', 'imageprocess', 'controller', 'triggerDownloadFile', 'before');

        if(!$oModuleModel->getTrigger('file.deleteFile', 'imageprocess', 'controller', 'triggerDeleteFile', 'after'))
        $oModuleController->insertTrigger('file.deleteFile', 'imageprocess', 'controller', 'triggerDeleteFile', 'after');

		if(!$oModuleModel->getTrigger('comment.deleteComment', 'imageprocess', 'controller', 'triggerDeleteComment', 'before'))
        $oModuleController->insertTrigger('comment.deleteComment', 'imageprocess', 'controller', 'triggerDeleteComment', 'before');
		
		if(!$oModuleModel->getTrigger('document.deleteDocument', 'imageprocess', 'controller', 'triggerDeleteDocument', 'before'))
        $oModuleController->insertTrigger('document.deleteDocument', 'imageprocess', 'controller', 'triggerDeleteDocument', 'before');
		
		if(!$oModuleModel->getTrigger('document.moveDocumentModule','imageprocess', 'controller', 'triggerMoveDocument', 'before')) $oModuleController->insertTrigger('document.moveDocumentModule','imageprocess', 'controller', 'triggerMoveDocument', 'before');

		return new Object(0, 'success_updated');
	}

	function moduleUninstall()
    {
       	$oModuleController->deleteTrigger('file.insertFile', 'imageprocess', 'controller', 'triggerInsertFile', 'after');
        $oModuleController->deleteTrigger('file.downloadFile', 'imageprocess', 'controller', 'triggerDownloadFile', 'before');
        $oModuleController->deleteTrigger('file.deleteFile', 'imageprocess', 'controller', 'triggerDeleteFile', 'after');
        $oModuleController->deleteTrigger('comment.deleteComment', 'imageprocess', 'controller', 'triggerDeleteComment', 'before');
        $oModuleController->deleteTrigger('document.deleteDocument', 'imageprocess', 'controller', 'triggerDeleteDocument', 'before');
		$oModuleController->deleteTrigger('document.moveDocumentModule','imageprocess', 'controller', 'triggerMoveDocument', 'before');

        return new Object(0, 'success_deleted'); 
    }


	/**
	 * @brief 캐시 파일 재생성
	 **/
	function recompileCache() 
	{
	}
}
/* End of file imageprocess.class.php */
/* Location: ./modules/imageprocess/imageprocess.class.php */
