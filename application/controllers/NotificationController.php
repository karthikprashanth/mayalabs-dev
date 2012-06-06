<?php

class NotificationController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function viewAction()
    {
    	
    	if(!$this->_request->isXmlHttpRequest()) {
        	$this->_helper->viewRenderer->setResponseSegment('notifications');
        }
		if($this->_getParam('mode',"") == "paginate")
		{
			$this->_helper->getHelper('layout')->disableLayout();
		}
		
		$ul = $this->_getParam('ul',9);
		$this->view->ul = $ul;
    	$notification = new Model_DbTable_Notification();
    	$notiList = $notification->getNotifications($ul);
		
    	$this->view->notiList = $notiList;
    	$uProfile = new Model_DbTable_Userprofile();
		
    	$this->view->uProfile = $uProfile;
    	$pDet = new Model_DbTable_Plant();
		
    	$this->view->pdet = $pDet;
    	$gDet = new Model_DbTable_Gasturbine();
		
    	$this->view->gdet = $gDet;
        $fDet = new Model_DbTable_Finding();
		
        $this->view->fdet = $fDet;
        $uDet = new Model_DbTable_Upgrade();
		
        $this->view->udet = $uDet;
        $lDet = new Model_DbTable_LTE();
		
        $this->view->ldet = $lDet;
        $confDet = new Model_DbTable_Conference();
		
        $this->view->confDet = $confDet;
        $forumData = new Model_DbTable_Forum_Data();
		
        $this->view->fData = $forumData;
        $forumTopics = new Model_DbTable_Forum_Topics();
		
        $this->view->fTopics = $forumTopics;
        $forumPosts = new Model_DbTable_Forum_Posts();
        $this->view->fPosts = $forumPosts;

    }


}



