<?php

class Plugin_Breadcrumbs extends Zend_Controller_Plugin_Abstract {

    protected $_view;

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        parent::preDispatch($request);
    }

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        parent::dispatchLoopStartup($request);

        $controller = $request->getControllerName();
        $action = $request->getActionName();

        $breadcrumb = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'breadcrumb');
        $breadcontainer = new Zend_Navigation($breadcrumb);
        if ($controller == 'conference' && $action != 'index' 
        && $action != 'view' && $action != 'add' 
        && $action != 'delete' && $action != 'delpres' && $action != 'delphoto') {       	
            if ($action == 'list' || $action == 'edit' || $action == 'gallery') {
                $cid = $request->getParam('id');
            } else if ($action == 'addpresentation') {
                $cid = $request->getParam('id');
            }
			
            $confModel = new Model_DbTable_Conference();
            $list = $confModel->getConfDetail($cid);
            $page = array(
                'label' => $list['year'] . ' (' . $list['place'] . ')',
                'controller' => 'conference',
                'action' => 'list',
                'codename' => 'conflistelement',
                'params' => array('id' => $list['cId'])
            );
            $breadcontainer->findOneBy('codename', 'confmain')->addPage($page);
			if($action == 'edit')
			{
				$page = array(
					'label' => 'Edit',
					'controller' => 'conference',
					'action' => 'edit',
					'params' => array('id' => $cid)
				);
				
				$breadcontainer->findOneBy('codename','conflistelement')->addPage($page);
			}
            if ($action == 'addpresentation') {
                $page = array(
                    'label' => 'Attachments',
                    'uri' => '/conference/list?id=' . $cid . '#confdata-frag-3',
                    'codename' => 'confpres'
                );
                $breadcontainer->findOneBy('codename', 'conflistelement')->addPage($page);

                $page = array(
                    'label' => 'Add',
                    'controller' => 'conference',
                    'action' => 'addpresentation'
                );

                $breadcontainer->findOneBy('codename', 'confpres')->addPage($page);
            }
			if($action == 'gallery')
			{
				$page = array(
					'label' => 'Gallery',
					'uri' => '/conference/list?id=' . $cid . '#confdata-frag-4'
				);
				
				$breadcontainer->findOneBy('codename', 'conflistelement')->addPage($page);
				
				$page = array(
					'label' => 'Add',
					'controller' => 'conference',
					'action' => 'gallery',
					'params' => array('id' => $cid)
				);
				
				$breadcontainer->findOneBy('label', 'Gallery')->addPage($page);
			}
			
        } 
		else if($controller == 'conference' && $action == 'add')
		{
			$page = array(
				'label' => 'Add',
				'controller' => 'conference',
				'action' => 'add'
			
			);
			$breadcontainer->findOneBy('codename', 'confmain')->addPage($page);
		}
        else if ($controller == 'schedule' && $action != 'view' && $action != 'delevent' && $action != 'delsch' && $action != 'edit') {
            $cid = $request->getParam('id');
            if ($action == 'add-event-list') {
                $n = $request->getParam('n');
            }
            $confmodel = new Model_DbTable_Conference();
            $conf = $confmodel->getConfDetail($cid);

            $page = array(
                'label' => 'Conferences',
                'controller' => 'Conference',
                'action' => 'index'
            );
            $breadcontainer->findOneBy('label', 'Home')->addPage($page);

            $page = array(
                'label' => $conf['year'] . ' (' . $conf['place'] . ')',
                'codename' => 'confitem',
                'controller' => 'conference',
                'action' => 'list',
                'params' => array('id' => $cid)
            );
            $breadcontainer->findOneBy('label', 'Conferences')->addPage($page);

            $page = array(
                'label' => 'Schedule',
                'uri' => '/conference/list?id=' . $cid . '#ui-tabs-1'
            );
            $breadcontainer->findOneBy('codename', 'confitem')->addPage($page);
			
            if(in_array($action,array('add','add-event-list')))
            {
	            $page = array(
	                'label' => 'Add',
	                'codename' => 'addsch',
	                'controller' => 'schedule',
	                'action' => 'add'
	            );
	            $breadcontainer->findOneBy('label', 'Schedule')->addPage($page);
	            if ($action == 'add-event-list') {
	                $page = array(
	                    'label' => 'Add',
	                    'codename' => 'addsch',
	                    'controller' => 'schedule',
	                    'action' => 'add-event-list',
	                    'params' => array('id' => $cid,'n' => $n)
	                );
	                $breadcontainer->findOneBy('label', 'Schedule')->addPage($page);
	            }
			}
			else if($action == 'edit')
			{
				$page = array(
	                'label' => 'Edit',
	                'controller' => 'schedule',
	                'action' => 'edit',
	                'params' => array('id' => $cid)
	            );
	            $breadcontainer->findOneBy('label', 'Schedule')->addPage($page);
			}
			else if($action == 'delete')
			{
				$page = array
				(
					'label' => 'Delete',
					'controller' => 'schedule',
					'action' => 'delete',
					'params' => array('id' => $cid)
				);
				$breadcontainer->findOneBy('label', 'Schedule')->addPage($page);
			}
        } else if ($controller == 'userprofile') {

            $page = array(
                'label' => 'Profile',
                'controller' => 'userprofile',
                'action' => 'view'
            );
            $breadcontainer->findOneBy('label', 'Home')->addPage($page);

            if ($action != 'view') {
                if ($action == 'index') {
                    $name = 'Edit';
                } else if ($action == 'add') {
                    $name = 'Add';
                } else if ($action == 'changepassword') {
                    $name = 'Change Password';
                }
                $page = array(
                    'label' => $name,
                    'controller' => 'userprofile',
                    'action' => $action
                );
                $breadcontainer->findOneBy('label', 'Profile')->addPage($page);
            }
        } else if ($controller == 'plant' && $action != 'admin') {
            if ($action == 'list') {
                $page = array(
                    'label' => 'Plants',
                    'controller' => 'plant',
                    'action' => 'list'
                );
                $breadcontainer->findOneBy('label', 'Home')->addPage($page);
            } else if ($action == 'add') {

                $page = array(
                    'label' => 'Plants',
                    'controller' => 'plant',
                    'action' => 'clist',
                    'codename' => 'plantlist'
                );
                $breadcontainer->findOneBy('label', 'Home')->addPage($page);

                $page = array(
                    'label' => 'Add',
                    'controller' => 'plant',
                    'action' => 'add'
                );
                $breadcontainer->findOneBy('codename', 'plantlist')->addPage($page);
            } else if ($action == 'clist') {
                $page = array(
                    'label' => 'Plants',
                    'controller' => 'plant',
                    'action' => 'clist',
                    'codename' => 'plantlist'
                );
                $breadcontainer->findOneBy('label', 'Home')->addPage($page);

           
            } else if ($action == 'view') {
                $pid = $request->getParam('id');
                $pmodel = new Model_DbTable_Plant();
                $plant = $pmodel->getPlant($pid);
                $uid = Zend_Auth::getInstance()->getStorage()->read()->id;
                $umodel = new Model_DbTable_Userprofile();
                $user = $umodel->getUser($uid);
                if ($pid == $user['plantId']) {
                    $page = array(
                        'label' => 'Profile',
                        'controller' => 'userprofile',
                        'action' => 'view'
                    );
                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);

                    $page = array(
                        'label' => $plant['plantName'],
                        'controller' => 'plant',
                        'action' => 'view',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('label', 'Profile')->addPage($page);
                } else {
                    $page = array(
                        'label' => 'Plants',
                        'controller' => 'plant',
                        'action' => 'clist',
                        'codename' => 'plantlist'
                    );

                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);

                    $page = array(
                        'label' => $plant['plantName'],
                        'controller' => 'plant',
                        'action' => 'view',
                        'params' => array('id' => $pid)
                    );

                    $breadcontainer->findOneBy('codename', 'plantlist')->addPage($page);
                }
            } else if ($action == 'edit') {
                $pid = $request->getParam('id');
                $pmodel = new Model_DbTable_Plant();
                $plant = $pmodel->getPlant($pid);
                $uid = Zend_Auth::getInstance()->getStorage()->read()->id;
                $umodel = new Model_DbTable_Userprofile();
                $user = $umodel->getUser($uid);
                if ($pid == $user['plantId']) {

                    $page = array(
                        'label' => 'Profile',
                        'controller' => 'userprofile',
                        'action' => 'view'
                    );
                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);

                    $page = array(
                        'label' => $plant['plantName'],
                        'controller' => 'plant',
                        'action' => 'view',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('label', 'Profile')->addPage($page);

                    $page = array(
                        'label' => 'Edit',
                        'controller' => 'plant',
                        'action' => 'edit',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);
                } else {
                    $page = array(
                        'label' => 'Plants',
                        'controller' => 'plant',
                        'action' => 'clist',
                        'codename' => 'plantlist'
                    );

                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);

                    $page = array(
                        'label' => $plant['plantName'],
                        'controller' => 'plant',
                        'action' => 'view',
                        'params' => array('id' => $pid)
                    );

                    $breadcontainer->findOneBy('codename', 'plantlist')->addPage($page);

                    $page = array(
                        'label' => 'Edit',
                        'controller' => 'plant',
                        'action' => 'edit',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);
                }
            }
        } else if ($controller == 'gasturbine') {

            if ($action == 'plantlist') {
                $pid = $request->getParam('id');
                $pmodel = new Model_DbTable_Plant();
                $plant = $pmodel->getPlant($pid);

                $uid = Zend_Auth::getInstance()->getStorage()->read()->id;
                $umodel = new Model_DbTable_Userprofile();
                $user = $umodel->getUser($uid);
                if ((int) $pid == (int) $user['plantId']) {

                    $page = array(
                        'label' => 'Profile',
                        'controller' => 'userprofile',
                        'action' => 'view'
                    );
                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);

                    $page = array(
                        'label' => $plant['plantName'],
                        'controller' => 'plant',
                        'action' => 'view',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('label', 'Profile')->addPage($page);

                    $page = array(
                        'label' => 'Gasturbines',
                        'controller' => 'gasturbine',
                        'action' => 'plantlist',
                        'params' => array('id' => $pid)
                    );

                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);
                } else {
                    $page = array(
                        'label' => 'Plants',
                        'controller' => 'plant',
                        'action' => 'clist',
                        'codename' => 'plantlist'
                    );

                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);

                    $page = array(
                        'label' => $plant['plantName'],
                        'controller' => 'plant',
                        'action' => 'view',
                        'params' => array('id' => $pid)
                    );

                    $breadcontainer->findOneBy('codename', 'plantlist')->addPage($page);

                    $page = array(
                        'label' => 'Gasturbines',
                        'controller' => 'gasturbine',
                        'action' => 'plantlist',
                        'params' => array('id' => $pid)
                    );

                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);
                }
            } else if ($action == 'edit') {
                $gtid = $request->getParam('id');
                $gtmodel = new Model_DbTable_Gasturbine();
                $gt = $gtmodel->getGT($gtid);

                $pid = $gt['plantId'];
                $pmodel = new Model_DbTable_Plant();
                $plant = $pmodel->getPlant($pid);

                $uid = Zend_Auth::getInstance()->getStorage()->read()->id;
                $umodel = new Model_DbTable_Userprofile();
                $user = $umodel->getUser($uid);

                if ((int) $user['plantId'] == (int) $gt['plantId']) {
                    $page = array(
                        'label' => 'Profile',
                        'controller' => 'userprofile',
                        'action' => 'view'
                    );
                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);

                    $page = array(
                        'label' => $plant['plantName'],
                        'controller' => 'plant',
                        'action' => 'view',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('label', 'Profile')->addPage($page);

                    $page = array(
                        'label' => 'Gasturbines',
                        'controller' => 'gasturbine',
                        'action' => 'plantlist',
                        'params' => array('id' => $pid)
                    );

                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);

                    $page = array(
                        'label' => $gt['GTName'],
                        'controller' => 'gasturbine',
                        'action' => 'view',
                        'params' => array('id' => $gtid)
                    );
                    $breadcontainer->findOneBy('label', 'Gasturbines')->addPage($page);

                    $page = array(
                        'label' => 'Edit',
                        'controller' => 'gasturbine',
                        'action' => 'edit',
                        'params' => array('id' => $gtid)
                    );
                    $breadcontainer->findOneBy('label', $gt['GTName'])->addPage($page);
                } else {
                    $page = array(
                        'label' => 'Plants',
                        'controller' => 'plant',
                        'action' => 'clist',
                        'codename' => 'plantlist'
                    );
                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);

                    $page = array(
                        'label' => $plant['plantName'],
                        'controller' => 'plant',
                        'action' => 'view',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('codename', 'plantlist')->addPage($page);

                    $page = array(
                        'label' => 'Gasturbines',
                        'controller' => 'gasturbine',
                        'action' => 'plantlist',
                        'params' => array('id' => $pid)
                    );

                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);

                    $page = array(
                        'label' => $gt['GTName'],
                        'controller' => 'gasturbine',
                        'action' => 'view',
                        'params' => array('id' => $gtid)
                    );
                    $breadcontainer->findOneBy('label', 'Gasturbines')->addPage($page);

                    $page = array(
                        'label' => 'Edit',
                        'controller' => 'gasturbine',
                        'action' => 'edit',
                        'params' => array('id' => $gtid)
                    );
                    $breadcontainer->findOneBy('label', $gt['GTName'])->addPage($page);
                }
            } else if ($action == 'list') {
                $page = array(
                    'label' => 'Gasturbines',
                    'controller' => 'gasturbine',
                    'action' => 'list'
                );
                $breadcontainer->findOneBy('label', 'Home')->addPage($page);
            } else if ($action == 'add') {
                $role = Zend_Auth::getInstance()->getStorage()->read()->role;

                if ($role == 'sa') {
                    $page = array(
                        'label' => 'Gasturbines',
                        'controller' => 'gasturbine',
                        'action' => 'list'
                    );
                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);

                    $page = array(
                        'label' => 'Add',
                        'controller' => 'gasturbine',
                        'action' => 'add'
                    );
                    $breadcontainer->findOneBy('label', 'Gasturbines')->addPage($page);
                } else {
                    $uid = Zend_Auth::getInstance()->getStorage()->read()->id;
                    $umodel = new Model_DbTable_Userprofile();
                    $user = $umodel->getUser($uid);

                    $pid = $user['plantId'];
                    $pmodel = new Model_DbTable_Plant();
                    $plant = $pmodel->getPlant($pid);

                    $page = array(
                        'label' => 'Profile',
                        'controller' => 'userprofile',
                        'action' => 'view'
                    );
                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);

                    $page = array(
                        'label' => $plant['plantName'],
                        'controller' => 'plant',
                        'action' => 'view',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('label', 'Profile')->addPage($page);

                    $page = array(
                        'label' => 'Gasturbines',
                        'controller' => 'gasturbine',
                        'action' => 'plantlist',
                        'params' => array('id' => $pid)
                    );

                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);

                    $page = array(
                        'label' => 'Add',
                        'controller' => 'gasturbine',
                        'action' => 'add'
                    );
                    $breadcontainer->findOneBy('label', 'Gasturbines')->addPage($page);
                }
            } else if ($action == 'view') {
                $gtid = $request->getParam('id');

                $uid = Zend_Auth::getInstance()->getStorage()->read()->id;
                $umodel = new Model_DbTable_Userprofile();
                $user = $umodel->getUser($uid);

                $pmodel = new Model_DbTable_Plant();
                $plant = $pmodel->getPlant($user['plantId']);
                $pid = $user['plantId'];

                $gtmodel = new Model_DbTable_Gasturbine();
                $gt = $gtmodel->getGT($gtid);
                $gtidlist = $gtmodel->listPlantGtArray($pid);
                $bool = false;
                foreach ($gtidlist as $gturbine) {
                    if ((int) $gturbine['GTId'] == (int) $gtid) {
                        $bool = true;
                    }
                }
                if ($bool) {

                    $page = array(
                        'label' => 'Profile',
                        'controller' => 'userprofile',
                        'action' => 'view'
                    );
                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);

                    $page = array(
                        'label' => $plant['plantName'],
                        'controller' => 'plant',
                        'action' => 'view',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('label', 'Profile')->addPage($page);

                    $page = array(
                        'label' => 'Gasturbines',
                        'controller' => 'gasturbine',
                        'action' => 'plantlist',
                        'codename' => 'gtlist',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);

                    $page = array(
                        'label' => $gt['GTName'],
                        'controller' => 'gasturbine',
                        'action' => 'view',
                        'params' => array('id' => $gtid)
                    );
                    $breadcontainer->findOneBy('codename', 'gtlist')->addPage($page);
                } else {
                    $gtid = $request->getParam('id');
                    $gtmodel = new Model_DbTable_Gasturbine();
                    $gt = $gtmodel->getGT($gtid);

                    $pid = $gt['plantId'];
                    $pmodel = new Model_DbTable_Plant();
                    $plant = $pmodel->getPlant($pid);

                    $page = array(
                        'label' => 'Plants',
                        'controller' => 'plant',
                        'action' => 'clist',
                        'codename' => 'plantlist'
                    );
                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);

                    $page = array(
                        'label' => $plant['plantName'],
                        'controller' => 'plant',
                        'action' => 'view',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('codename', 'plantlist')->addPage($page);

                    $page = array(
                        'label' => 'Gasturbines',
                        'controller' => 'gasturbine',
                        'action' => 'plantlist',
                        'codename' => 'gtlist',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);

                    $page = array(
                        'label' => $gt['GTName'],
                        'controller' => 'gasturbine',
                        'action' => 'view',
                        'params' => array('id' => $gtid)
                    );
                    $breadcontainer->findOneBy('codename', 'gtlist')->addPage($page);
                }
            }
        } else if ($controller == 'findings' || $controller == 'upgrades' || $controller == 'lte'  || ($controller == 'presentation' && $action != 'view' && $action != 'delete') && $action != 'list') {
            if ($action == 'list' || $action == 'delete') {
                return;
            }
            if ($controller == 'findings') {
                $lno = 2;
                $name = 'Findings';
            } else if ($controller == 'upgrades') {
                $lno = 3;
                $name = 'Upgrades';
            } else if ($controller == 'lte') {
                $lno = 4;
                $name = 'LTE';
            } else if ($controller == 'presentation') {
                $lno = 5;
                $name = 'Attachments';
            }
            if ($action == 'add') {
                $gtid = $request->getPost('gtid');
                $gtmodel = new Model_DbTable_Gasturbine();
                $gt = $gtmodel->getGT($gtid);
                $pid = $gt['plantId'];

                $pmodel = new Model_DbTable_Plant();
                $plant = $pmodel->getPlant($pid);

                $uid = Zend_Auth::getInstance()->getStorage()->read()->id;
                $umodel = new Model_DbTable_Userprofile();
                $user = $umodel->getUser($uid);

                if ((int) $user['plantId'] == (int) $pid) {
                    $page = array(
                        'label' => 'Profile',
                        'controller' => 'userprofile',
                        'action' => 'view'
                    );
                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);

                    $page = array(
                        'label' => $plant['plantName'],
                        'controller' => 'plant',
                        'action' => 'view',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('label', 'Profile')->addPage($page);

                    $page = array(
                        'label' => 'Gasturbines',
                        'controller' => 'gasturbine',
                        'action' => 'plantlist',
                        'codename' => 'gtlist',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);

                    $page = array(
                        'label' => $gt['GTName'],
                        'controller' => 'gasturbine',
                        'action' => 'view',
                        'params' => array('id' => $gtid)
                    );
                    $breadcontainer->findOneBy('codename', 'gtlist')->addPage($page);

                    $page = array(
                        'label' => $name,
                        'uri' => '/gasturbine/view?id=' . $gtid . '#ui-tabs-' . $lno
                    );
                    $breadcontainer->findOneBy('label', $gt['GTName'])->addPage($page);

                    $page = array(
                        'label' => 'Add',
                        'controller' => $controller,
                        'action' => 'add',
                        
                    );
                    $breadcontainer->findOneBy('label', $name)->addPage($page);
                } else {
                    $page = array(
                        'label' => 'Plants',
                        'controller' => 'plant',
                        'action' => 'clist',
                        'codename' => 'plantlist'
                    );
                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);

                    $page = array(
                        'label' => $plant['plantName'],
                        'controller' => 'plant',
                        'action' => 'view',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('codename', 'plantlist')->addPage($page);

                    $page = array(
                        'label' => 'Gasturbines',
                        'controller' => 'gasturbine',
                        'action' => 'plantlist',
                        'codename' => 'gtlist',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);

                    $page = array(
                        'label' => $gt['GTName'],
                        'controller' => 'gasturbine',
                        'action' => 'view',
                        'params' => array('id' => $gtid)
                    );
                    $breadcontainer->findOneBy('codename', 'gtlist')->addPage($page);

                    $page = array(
                        'label' => $name,
                        'uri' => '/gasturbine/view?id=' . $gtid . '#ui-tabs-' . $lno
                    );
                    $breadcontainer->findOneBy('label', $gt['GTName'])->addPage($page);

                    $page = array(
                        'label' => 'Add',
                        'controller' => $controller,
                        'action' => 'add'
                    );
                    $breadcontainer->findOneBy('label', $name)->addPage($page);
                }
            } else if ($action == 'view' || $action == 'edit') {

                $id = $request->getParam('id');

                $gtdatamodel = new Model_DbTable_Gtdata();
                $gtdata = $gtdatamodel->getData($id);
                $gtid = $gtdata['gtid'];
                $gtmodel = new Model_DbTable_Gasturbine();
                $gt = $gtmodel->getGT($gtid);
                $pid = $gt['plantId'];

                $pmodel = new Model_DbTable_Plant();
                $plant = $pmodel->getPlant($pid);

                if ($gtdatamodel->isGTBelong($id)) {
                    $page = array(
                        'label' => 'Profile',
                        'controller' => 'userprofile',
                        'action' => 'view'
                    );
                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);

                    $page = array(
                        'label' => $plant['plantName'],
                        'controller' => 'plant',
                        'action' => 'view',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('label', 'Profile')->addPage($page);

                    $page = array(
                        'label' => 'Gasturbines',
                        'controller' => 'gasturbine',
                        'action' => 'plantlist',
                        'codename' => 'gtlist',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);

                    $page = array(
                        'label' => $gt['GTName'],
                        'controller' => 'gasturbine',
                        'action' => 'view',
                        'params' => array('id' => $gtid)
                    );
                    $breadcontainer->findOneBy('codename', 'gtlist')->addPage($page);

                    $page = array(
                        'label' => $name,
                        'uri' => '/gasturbine/view?id=' . $gtdata['gtid'] . '#ui-tabs-' . $lno
                    );
                    $breadcontainer->findOneBy('label', $gt['GTName'])->addPage($page);

                    $page = array(
                        'label' => $gtdata['title'],
                        'controller' => $controller,
                        'action' => 'view',
                        'params' => array('id' => $id)
                    );
                    $breadcontainer->findOneBy('label', $name)->addPage($page);

                    if ($action == 'edit') {
                        $page = array(
                            'label' => 'Edit',
                            'controller' => $controller,
                            'action' => 'edit',
                            'params' => array('id' => $id)
                        );
                        $breadcontainer->findOneBy('label', $gtdata['title'])->addPage($page);
                    }
                } else {
                    $page = array(
                        'label' => 'Plants',
                        'controller' => 'plant',
                        'action' => 'clist',
                        'codename' => 'plantlist'
                    );
                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);

                    $page = array(
                        'label' => $plant['plantName'],
                        'controller' => 'plant',
                        'action' => 'view',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('codename', 'plantlist')->addPage($page);

                    $page = array(
                        'label' => 'Gasturbines',
                        'controller' => 'gasturbine',
                        'action' => 'plantlist',
                        'codename' => 'gtlist',
                        'params' => array('id' => $pid)
                    );
                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);

                    $page = array(
                        'label' => $gt['GTName'],
                        'controller' => 'gasturbine',
                        'action' => 'view',
                        'params' => array('id' => $gtid)
                    );
                    $breadcontainer->findOneBy('codename', 'gtlist')->addPage($page);

                    $page = array(
                        'label' => $name,
                        'uri' => '/gasturbine/view?id=' . $gtdata['gtid'] . '#ui-tabs-' . $lno
                    );
                    $breadcontainer->findOneBy('label', $gt['GTName'])->addPage($page);

                    $page = array(
                        'label' => $gtdata['title'],
                        'controller' => $controller,
                        'action' => 'view',
                        'params' => array('id' => $id)
                    );
                    $breadcontainer->findOneBy('label', $name)->addPage($page);

                    if ($action == 'edit') {
                        $page = array(
                            'label' => 'Edit',
                            'controller' => $controller,
                            'action' => 'edit',
                            'params' => array('id' => $id)
                        );
                        $breadcontainer->findOneBy('label', $gtdata['title'])->addPage($page);
                    }
                }
            }
        } else if ($controller == 'advertisement' && $action != 'list' && $action != 'randomad' && $action != 'add') {
            if ($action == 'view') {
                $id = $request->getParam('id');
            } else if ($action == 'edit') {
                $id = $request->getPost('advertId');
            }
            $adModel = new Model_DbTable_Advertisement();
            $ad = $adModel->getAdvertisement($id);
            $page = array(
                'label' => $ad['title'],
                'controller' => 'advertisement',
                'action' => 'view',
                'params' => array('id' => $id)
            );
            $breadcontainer->findOneBy('label', 'Advertisements')->addPage($page);
            if ($action == 'edit') {
                $page = array(
                    'label' => 'Edit',
                    'controller' => 'advertisement',
                    'action' => 'edit'
                );

                $breadcontainer->findOneBy('label', $ad['title'])->addPage($page);
            }
        }
		else if($controller == 'notification')
		{
			if($action == 'view')
			{
				$page = array(
					'label' => 'News Feed',
					'controller' => 'notification',
					'action' => 'view'
				);
				
				$breadcontainer->findOneBy('label','Home')->addPage($page);
			}
		}
		else if($controller == 'search')
		{
			$page = array(
				'label' => 'Search',
				'controller' => 'search',
				'action' => 'index'
			);
			
			$breadcontainer->findOneBy('label','Home')->addPage($page);
			
			if($action == 'searchmatrix')
			{
				$page = array(
					'label' => 'Hive Matrix',
					'controller' => 'search',
					'action' => 'searchmatrix'
				);
				
				$breadcontainer->findOneBy('label','Search')->addPage($page);
			}
			else if($action == 'searchindex')
			{
				$page = array(
					'label' => 'Indexing',
					'controller' => 'search',
					'action' => 'searchindex'
				);
				
				$breadcontainer->findOneBy('label','Search')->addPage($page);
			}
		}
		else if($controller = 'reports')
		{
			if($action == 'configure')
			{
				$id = $request->getParam('id');
				$type = $request->getParam('type');
				$gtdatamodel = new Model_DbTable_Gtdata();
				$gtmodel = new Model_DbTable_Gasturbine();
				$pmodel = new Model_DbTable_Plant();
				$umodel = new Model_DbTable_Userprofile();
				
				if($type == "gtdataview")
				{
					
					$gtdata = $gtdatamodel->getData($id);
					$gt = $gtmodel->getGT($gtdata['gtid']);
					$plant = $pmodel->getPlant($gt['plantId']);
					$pid = $plant['plantId'];
					$gtid = $gt['GTId'];
					$gdtype = $gtdata['type'];
					
					if ($gdtype == 'finding') {
		                $lno = 2;
		                $name = 'Findings';
		            } else if ($gdtype == 'upgrade') {
		                $lno = 3;
		                $name = 'Upgrades';
		            } else if ($gdtype == 'lte') {
		                $lno = 4;
		                $name = 'LTE';
		            }
					
					if($gtdatamodel->isGTBelong($id))
					{
						$page = array(
	                        'label' => 'Profile',
	                        'controller' => 'userprofile',
	                        'action' => 'view'
	                    );
	                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);
						
						$page = array(
	                        'label' => $plant['plantName'],
	                        'controller' => 'plant',
	                        'action' => 'view',
	                        'params' => array('id' => $pid)
	                    );
	                    $breadcontainer->findOneBy('label', 'Profile')->addPage($page);
						
						$page = array(
	                        'label' => 'Gasturbines',
	                        'controller' => 'gasturbine',
	                        'action' => 'plantlist',
	                        'codename' => 'gtlist',
	                        'params' => array('id' => $pid)
	                    );
	                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);
						
						$page = array(
	                        'label' => $gt['GTName'],
	                        'controller' => 'gasturbine',
	                        'action' => 'view',
	                        'params' => array('id' => $gtid)
	                    );
	                    $breadcontainer->findOneBy('codename', 'gtlist')->addPage($page);
						
						$page = array(
	                        'label' => $name,
	                        'uri' => '/gasturbine/view?id=' . $gtid . '#ui-tabs-' . $lno
	                    );
	                    $breadcontainer->findOneBy('label', $gt['GTName'])->addPage($page);
						
						$page = array(
	                        'label' => $gtdata['title'],
	                        'controller' => $controller,
	                        'action' => 'view',
	                        'params' => array('id' => $id)
	                    );
	                    $breadcontainer->findOneBy('label', $name)->addPage($page);
						
						$page = array(
							'label' => 'Configure Report',
							'controller' => 'reports',
							'action' => 'configure'
						);
						
						$breadcontainer->findOneBy('label', $gtdata['title'])->addPage($page);
					}
					else 
					{
						$page = array(
	                        'label' => 'Plants',
	                        'controller' => 'plant',
	                        'action' => 'clist',
	                        'codename' => 'plantlist'
	                    );
	                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);
	
	                    $page = array(
	                        'label' => $plant['plantName'],
	                        'controller' => 'plant',
	                        'action' => 'view',
	                        'params' => array('id' => $pid)
	                    );
	                    $breadcontainer->findOneBy('codename', 'plantlist')->addPage($page);
	
	                    $page = array(
	                        'label' => 'Gasturbines',
	                        'controller' => 'gasturbine',
	                        'action' => 'plantlist',
	                        'codename' => 'gtlist',
	                        'params' => array('id' => $pid)
	                    );
	                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);
	
	                    $page = array(
	                        'label' => $gt['GTName'],
	                        'controller' => 'gasturbine',
	                        'action' => 'view',
	                        'params' => array('id' => $gtid)
	                    );
	                    $breadcontainer->findOneBy('codename', 'gtlist')->addPage($page);
	
	                    $page = array(
	                        'label' => $name,
	                        'uri' => '/gasturbine/view?id=' . $gtid . '#ui-tabs-' . $lno
	                    );
	                    $breadcontainer->findOneBy('label', $gt['GTName'])->addPage($page);
	
	                    $page = array(
	                        'label' => $gtdata['title'],
	                        'controller' => $controller,
	                        'action' => 'view',
	                        'params' => array('id' => $id)
	                    );
	                    $breadcontainer->findOneBy('label', $name)->addPage($page);
						
						$page = array(
							'label' => 'Configure Report',
							'controller' => 'reports',
							'action' => 'configure'
						);
						
						$breadcontainer->findOneBy('label', $gtdata['title'])->addPage($page);
					}
				}
				else if(in_array($type,array("finding","upgrade","lte")))
				{
					$gt = $gtmodel->getGT($id);
					$plant = $pmodel->getPlant($gt['plantId']);
					$pid = $plant['plantId'];
					$gtid = $gt['GTId'];
					$user = $umodel->getUser(Zend_Auth::getInstance()->getStorage()->read()->id);
					$uplantid = $user['plantId'];
					if((int)$uplantid == (int)$gt['plantId'])
					{
						$belong = true;
					}					
					else
					{
						$belong = false;
					}
					
					if ($type == 'finding') {
		                $lno = 2;
		                $name = 'Findings';
		            } else if ($type == 'upgrade') {
		                $lno = 3;
		                $name = 'Upgrades';
		            } else if ($type == 'lte') {
		                $lno = 4;
		                $name = 'LTE';
		            }
					if($belong)
					{
						$page = array(
	                        'label' => 'Profile',
	                        'controller' => 'userprofile',
	                        'action' => 'view'
	                    );
	                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);
						
						$page = array(
	                        'label' => $plant['plantName'],
	                        'controller' => 'plant',
	                        'action' => 'view',
	                        'params' => array('id' => $pid)
	                    );
	                    $breadcontainer->findOneBy('label', 'Profile')->addPage($page);
						
						$page = array(
	                        'label' => 'Gasturbines',
	                        'controller' => 'gasturbine',
	                        'action' => 'plantlist',
	                        'codename' => 'gtlist',
	                        'params' => array('id' => $pid)
	                    );
	                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);
						
						$page = array(
	                        'label' => $gt['GTName'],
	                        'controller' => 'gasturbine',
	                        'action' => 'view',
	                        'params' => array('id' => $gtid)
	                    );
	                    $breadcontainer->findOneBy('codename', 'gtlist')->addPage($page);
						
						$page = array(
	                        'label' => $name,
	                        'uri' => '/gasturbine/view?id=' . $gtid . '#ui-tabs-' . $lno
	                    );
	                    $breadcontainer->findOneBy('label', $gt['GTName'])->addPage($page);

						$page = array(
							'label' => 'Configure Report',
							'controller' => 'reports',
							'action' => 'configure'
						);
						
						$breadcontainer->findOneBy('label', $name)->addPage($page);
					}
					else 
					{
						$page = array(
	                        'label' => 'Plants',
	                        'controller' => 'plant',
	                        'action' => 'clist',
	                        'codename' => 'plantlist'
	                    );
	                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);
	
	                    $page = array(
	                        'label' => $plant['plantName'],
	                        'controller' => 'plant',
	                        'action' => 'view',
	                        'params' => array('id' => $pid)
	                    );
	                    $breadcontainer->findOneBy('codename', 'plantlist')->addPage($page);
	
	                    $page = array(
	                        'label' => 'Gasturbines',
	                        'controller' => 'gasturbine',
	                        'action' => 'plantlist',
	                        'codename' => 'gtlist',
	                        'params' => array('id' => $pid)
	                    );
	                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);
	
	                    $page = array(
	                        'label' => $gt['GTName'],
	                        'controller' => 'gasturbine',
	                        'action' => 'view',
	                        'params' => array('id' => $gtid)
	                    );
	                    $breadcontainer->findOneBy('codename', 'gtlist')->addPage($page);
	
	                    $page = array(
	                        'label' => $name,
	                        'uri' => '/gasturbine/view?id=' . $gtid . '#ui-tabs-' . $lno
	                    );
	                    $breadcontainer->findOneBy('label', $gt['GTName'])->addPage($page);

						$page = array(
							'label' => 'Configure Report',
							'controller' => 'reports',
							'action' => 'configure'
						);
						
						$breadcontainer->findOneBy('label', $name)->addPage($page);
					}
				}
				else if($type == "gtreport")
				{
					$gt = $gtmodel->getGT($id);
					$plant = $pmodel->getPlant($gt['plantId']);
					$pid = $plant['plantId'];
					$gtid = $gt['GTId'];
					$user = $umodel->getUser(Zend_Auth::getInstance()->getStorage()->read()->id);
					$uplantid = $user['plantId'];
					if((int)$uplantid == (int)$gt['plantId'])
					{
						$belong = true;
					}					
					else
					{
						$belong = false;
					}
					
					if ($type == 'finding') {
		                $lno = 2;
		                $name = 'Findings';
		            } else if ($type == 'upgrade') {
		                $lno = 3;
		                $name = 'Upgrades';
		            } else if ($type == 'lte') {
		                $lno = 4;
		                $name = 'LTE';
		            }
					if($belong)
					{
						$page = array(
	                        'label' => 'Profile',
	                        'controller' => 'userprofile',
	                        'action' => 'view'
	                    );
	                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);
						
						$page = array(
	                        'label' => $plant['plantName'],
	                        'controller' => 'plant',
	                        'action' => 'view',
	                        'params' => array('id' => $pid)
	                    );
	                    $breadcontainer->findOneBy('label', 'Profile')->addPage($page);
						
						$page = array(
	                        'label' => 'Gasturbines',
	                        'controller' => 'gasturbine',
	                        'action' => 'plantlist',
	                        'codename' => 'gtlist',
	                        'params' => array('id' => $pid)
	                    );
	                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);
						
						$page = array(
	                        'label' => $gt['GTName'],
	                        'controller' => 'gasturbine',
	                        'action' => 'view',
	                        'params' => array('id' => $gtid)
	                    );
	                    $breadcontainer->findOneBy('codename', 'gtlist')->addPage($page);

						$page = array(
							'label' => 'Configure Report',
							'controller' => 'reports',
							'action' => 'configure'
						);
						
						$breadcontainer->findOneBy('label', $gt['GTName'])->addPage($page);
					}
					else 
					{
						$page = array(
	                        'label' => 'Plants',
	                        'controller' => 'plant',
	                        'action' => 'clist',
	                        'codename' => 'plantlist'
	                    );
	                    $breadcontainer->findOneBy('label', 'Home')->addPage($page);
	
	                    $page = array(
	                        'label' => $plant['plantName'],
	                        'controller' => 'plant',
	                        'action' => 'view',
	                        'params' => array('id' => $pid)
	                    );
	                    $breadcontainer->findOneBy('codename', 'plantlist')->addPage($page);
	
	                    $page = array(
	                        'label' => 'Gasturbines',
	                        'controller' => 'gasturbine',
	                        'action' => 'plantlist',
	                        'codename' => 'gtlist',
	                        'params' => array('id' => $pid)
	                    );
	                    $breadcontainer->findOneBy('label', $plant['plantName'])->addPage($page);
	
	                    $page = array(
	                        'label' => $gt['GTName'],
	                        'controller' => 'gasturbine',
	                        'action' => 'view',
	                        'params' => array('id' => $gtid)
	                    );
	                    $breadcontainer->findOneBy('codename', 'gtlist')->addPage($page);

						$page = array(
							'label' => 'Configure Report',
							'controller' => 'reports',
							'action' => 'configure'
						);
						
						$breadcontainer->findOneBy('label', $gt['GTName'])->addPage($page);
					}
				}
				
			}
		} 
        Zend_Registry::set('breadcontainer', $breadcontainer);
    }

}

?>