<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    private $_acl = null;
    private $_auth = null;

    protected function _initAutoload() {
        $modelLoader = new Zend_Application_Module_Autoloader(array(
                    'namespace' => '',
                    'basePath' => APPLICATION_PATH));
        Zend_Session::start();
		Zend_Registry::set('gtgroupname','v93.4A');
        if (Zend_Auth::getInstance()->hasIdentity() && Zend_Auth::getInstance()->getStorage()->read()->role != '') {
            Zend_Registry::set('role', Zend_Auth::getInstance()->getStorage()->read()->role);
        } else {
            Zend_Registry::set('role', 'guest');
        }
		
		$mailconfig = $this->getOption('mailconfig');
		Zend_Registry::set('mailconfig',$mailconfig);
		
        $this->_acl = new Model_HiveAcl();
        Zend_Registry::set('acl', $this->_acl);
		
        $frontControl = Zend_Controller_Front::getInstance();
        $frontControl->registerPlugin(new Plugin_AccessCheck($this->_acl));

        if (Zend_Registry::get('role') != 'guest') {
            $frontControl->registerPlugin(new Plugin_HiveLayout());
            //$frontControl->registerPlugin(new Plugin_BookmarkLayout());
            // Bookmark layout is commented out because it is getting rendered even during xmlHttpRequest
            //$frontControl->registerPlugin(new Plugin_EditPermission());
            //$frontControl->registerPlugin(new Plugin_Breadcrumbs());
			$frontControl->registerPlugin(new Plugin_ForumSearchIndex());
        }
        return $modelLoader;
    }

    function _initViewHelpers() {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $layout->setLayout('layout');
        $frontControl = Zend_Controller_Front::getInstance();
        $view = $layout->getView();
        ZendX_JQuery::enableView($view);
        $view->doctype('HTML5');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8')
                ->appendName('Description', 'A Networking and Collaboration platform');
        $view->headTitle()->setSeparator(' - ');
        $view->headTitle('Hive');
        try {
            $role = Zend_Registry::get('role');
            $navTag = 'nav';
            if ($role == 'sa') {
                $navTag = 'adminnav';
            } else if ($role == 'ca') {
                $navTag = 'navca';
            }
            $navContainerConfig = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', $navTag);
            $navContainer = new Zend_Navigation($navContainerConfig);
            Zend_Registry::set('navcontainer', $navContainer);
            if ($role != 'sa') {
                $navContainer->findOneBy('codename', 'userplantview')->setParams(array('id' => 1));
            }
            $view->navigation($navContainer)->setAcl($this->_acl)->setRole($role);
			
        } catch (Exception $e) {
            echo $e;
        }
    }
}
