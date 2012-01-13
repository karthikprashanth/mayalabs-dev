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
            $up = new Model_DbTable_Userprofile(Zend_Db_Table::getDefaultAdapter(), Zend_Auth::getInstance()->getStorage()->read()->id);
            $name = $up->getFullName();
			
			$role = Zend_Registry::get("role");
			
			if($role != 'sa')
			{
                $pid = $up->getPlantId();
				$gtmodel = new Model_DbTable_Gasturbine(Zend_Db_Table::getDefaultAdapter());
                $gt = $gtmodel->getList(array("plantId" => $pid));
				
				$plantmodel = new Model_DbTable_Plant(Zend_Db_Table::getDefaultAdapter(), $pid);
				$plantname = $plantmodel->getPlantName();
			}
			$uid = Zend_Auth::getInstance()->getStorage()->read()->id;
			$uModel = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(), Zend_Auth::getInstance()->getStorage()->read()->id, "");
			$iscc = $uModel->isConfChair();
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

