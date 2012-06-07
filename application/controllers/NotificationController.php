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
    	$notifications = Model_DbTable_Notification::getList(array('limit' => $ul));
    	
		$this->view->notifications = $notifications;

    }
}



