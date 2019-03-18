<?php
/**
 * imageprocessView class
 * View class of the module imageprocess
 *
 * @author karma (soonj@naver.com)
 * @package /modules/imageprocess
 * @version 0.1
 */
class imageprocessView extends imageprocess
{
        /**
         * Initialization
         * @return void
         */
        function init()
        {
        }


	/**
         * @brief Add a form for imageprocess addition setup
         */
        function triggerDispImageprocessAdditionSetup(&$obj)
        {
                $current_module_srl = Context::get('module_srl');

                if(!$current_module_srl)
                {
                        // Get information of the current module
                        $current_module_info = Context::get('current_module_info');
                        $current_module_srl = $current_module_info->module_srl;
                        if(!$current_module_srl) return class_exists('BaseObject') ? new BaseObject() : new Object();
                }

		$oModuleModel = &getModel('module');
                $oImageprocessModel = getModel('imageprocess');

                Context::set('ipx_module_info', $oImageprocessModel->getImageprocessConfig($current_module_srl));
		Context::set('stampList', $oImageprocessModel->getStampList());
                Context::set('ipx_info', $oModuleModel->getModuleConfig('imageprocess'));

                // Set a template file
                $oTemplate = &TemplateHandler::getInstance();
                $tpl = $oTemplate->compile($this->module_path.'tpl', 'imageprocess_module_config');
                $obj .= $tpl;

                return class_exists('BaseObject') ? new BaseObject() : new Object();
        }
}
