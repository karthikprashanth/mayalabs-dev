<?php

class GtsubsystemsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        
    }
	
	public function listAction(){
		try{
			$this->_helper->getHelper('Layout')->disableLayout();
			$sid = $this->_getParam("sid",0);
			if(!$sid){
				$this->view->list = Model_DbTable_Gtsubsystems::getList();
			}
			else {
				$this->view->list = Model_DbTable_Gtsubsystems::getList($sid);
			}
		}
		catch(Exception $e){
			echo $e;
		}
	}

}