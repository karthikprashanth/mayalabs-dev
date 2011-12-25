<?php

class Plugin_EditPermission extends Zend_Controller_Plugin_Abstract {

    protected $_view;

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        parent::preDispatch($request);
    }

    public function  dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        parent::dispatchLoopStartup($request);
        $role = Zend_Registry::get('role');
       	$valid = false;
       	if($role == 'sa')
		{
			$valid = true;
		}
        $controller = $request->getControllerName();
		$action = $request->getActionName();
        $id = $request->getParam('id');
		
        $up = new Model_DbTable_Userprofile();
		$umodel = new Model_DbTable_User();
        $up = $up->getUser(Zend_Auth::getInstance()->getStorage()->read()->id);
		$iscc = $umodel->is_confchair(Zend_Auth::getInstance()->getStorage()->read()->id);
		if($controller == 'schedule' && (in_array($action,array('add','edit','addeventlist','delete','delsch','delevent'))))
		{
			if($role != 'sa' && !$iscc)
			{
				$request->setControllerName('dashboard');
            	$request->setActionName('index');
			}
		}
		else if($controller == 'schedule'){
			$valid = true;
		}
		if($controller == 'conference' && (in_array($action,array('add','edit','delete','addpresentation','delpres','delphoto'))))
		{
			if($role != 'sa' && !$iscc)
			{
				$request->setControllerName('dashboard');
            	$request->setActionName('index');
			}
		}
		else if($controller == 'conference')
		{
			$valid = true;
		}
		if(!in_array($controller,array("gasturbine","plant","findings","upgrades","lte")))
		{
			return;
		}
		if($action != 'edit' && $action != 'delete')
		{
			return;
		}
        $pid = $up['plantId'];
        if ($controller == 'gasturbine') {
        	
            $gt = new Model_DbTable_Gasturbine();
            $gt = $gt->getGTP($pid);
            foreach ($gt as $temp) {
                if ((int)$temp['GTId'] == (int)$id) {
                    $valid = true;
                }
            }
        } else if ($controller == 'plant') {
            if((int)$pid == (int)$id){
                $valid = true;
                
            }
        } else if($controller == 'findings'){
            $gt = new Model_DbTable_Gasturbine();
            $gt = $gt->getGTP($pid);
            $fin = new Model_DbTable_Finding();
            $fin = $fin->getFinding($id);
            foreach ($gt as $temp){
                if((int)$temp['GTId'] == (int)$fin['gtid']){
                    $valid = true;
                }
            }
        } else if($controller == 'upgrades'){
            $gt = new Model_DbTable_Gasturbine();
            $gt = $gt->getGTP($pid);
            $fin = new Model_DbTable_Upgrade();
            $fin = $fin->getUpgrade($id);
            foreach ($gt as $temp){
                if((int)$temp['GTId'] == (int)$fin['gtid']){
                    $valid = true;
                }
            }
        } else if($controller == 'lte'){
            $gt = new Model_DbTable_Gasturbine();
            $gt = $gt->getGTP($pid);
            $fin = new Model_DbTable_LTE();
            $fin = $fin->getLTE($id);
            foreach ($gt as $temp){
                if((int)$temp['GTId'] == (int)$fin['gtid']){
                    $valid = true;
                }
            }
        }

        if(!$valid){
            $request->setControllerName('dashboard');
            $request->setActionName('index');
        }            
    }
}

?>
