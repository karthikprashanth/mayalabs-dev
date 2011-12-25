<?php

class MyprofileController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        try {
            if (!$this->_request->isXmlHttpRequest()) {
                $this->view->role = Zend_Registry::get('role');
                $this->_helper->viewRenderer->setResponseSegment('sidebar1');
            }						
            $up = new Model_DbTable_Userprofile();
            $up = $up->getUser(Zend_Auth::getInstance()->getStorage()->read()->id);
            $name = $up['firstName'] . " " . $up['lastName'];
			
			$role = Zend_Registry::get("role");
			
			if($role != 'sa')
			{
				$pid = $up['plantId'];
				$gtmodel = new Model_DbTable_Gasturbine();
				$gt = $gtmodel->getGTP($pid);
				
				$plantmodel = new Model_DbTable_Plant();
				$plant = $plantmodel->getPlant($pid);
				$plantname = $plant['plantName'];
			}
			$uid = Zend_Auth::getInstance()->getStorage()->read()->id;
			$uModel = new Model_DbTable_User();
			$iscc = $uModel->is_confchair($uid);
    		$this->view->iscc = $iscc;
            $this->view->name = $name;
			$this->view->pid = $pid;
			$this->view->pname = $plantname;
			$this->view->gt = $gt;
        } catch (Exception $e) {
            echo $e;
		}
      
    }

}

