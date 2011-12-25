<?php

class DashboardController extends Zend_Controller_Action
{

    public function init()
    {
        
    }

    public function indexAction()
    {
    		
        $this->view->headTitle('Dashboard','PREPEND');
        $username = Zend_Auth::getInstance()->getStorage()->read()->username;
        $role=Zend_Auth::getInstance()->getStorage()->read()->role;
        $this->view->username = $username;
        $this->view->role = $role;
        $this->view->lastlogin = Zend_Auth::getInstance()->getStorage()->read()->lastlogin;
    } 

    public function showmenuAction()
    {
        $this->_helper->getHelper('Layout')->disableLayout();
	    $role = Zend_Registry::get('role');
        $navTag = 'nav';
        if($role == 'sa'){
            $navTag = 'adminnav';
        }
        else if($role == 'ca') {
        	$navTag = 'navca';
        }
	    $navContainerConfig = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml',$navTag);               
		$navContainer=new Zend_Navigation($navContainerConfig);
		Zend_Registry::set('navcontainer',$navContainer);
		$acl = new Model_HiveAcl();
		$this->view->navigation($navContainer)->setAcl($acl)->setRole($role);
    }

}



