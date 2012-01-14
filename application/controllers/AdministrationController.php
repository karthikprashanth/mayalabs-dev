<?php

class AdministrationController extends Zend_Controller_Action {

    public function init() {
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('index', 'json')
                ->initContext();
    }

    public static function transformAccount($id) {
        $currentStorage = Zend_Auth::getInstance()->getStorage();
        $currentData = $currentStorage->read();
        try {
            $currentData['restoreid'] = $currentData['id'];
            $currentData['id'] = $id;
            $currentStorage->write($currentData);
            $this->_redirect('dashboard/index');
        } catch (Exception $e) {
            echo $e;
        }
    }

    public static function getUserCredentials($id) {
        $credentials = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(), $id, "");
        $data = $credentials->getUserData();
        return $data;
    }

    private function getAdminLoginAdapter() {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('users')
                ->setIdentityColumn('username')
                ->setCredentialColumn('password');
        return $authAdapter;
    }

    public static function adminAuthLogin($username, $password) {
        try {
            $d = $username . $password;

            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($authAdapter);
            if ($result->isValid()) {
                $identity = $authAdapter->getResultRowObject();
                $authStorage = $auth->getStorage();

                // Add original id in the resultrowobject to use in restore
                $authStorage->write($identity);
                Zend_Registry::set('id', Zend_Auth::getInstance()->getStorage()->read()->id);
            }
            return $d;
        } catch (Exception $e) {
            echo $e;
        }
    }

    /**
     * Needs Changes as Model_ListUsers doesn't exist
     */
    public function indexAction() {
        $register = new Model_ListUsers();
        $register = $register->ListUsers();

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($register));
        $paginator->setItemCountPerPage(2)
                ->setCurrentPageNumber($this->_getParam('page', 1));

        if (!$this->_request->isXmlHttpRequest()) {
            $this->view->paginator = $paginator;
        } else {
            $users = array();
            foreach ($paginator as $user) {
                $users[] = array('username' => $user['username'], 'role' => $user['role']);
            }
        }

        $this->view->users = $users;
    }

    public function createaccAction() {
        $form = new Form_RegistrationForm();
        $form->submit->setLabel('Add');
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $username = $form->getValue('username');
                $role = $form->getValue('role');
                $plantId = $form->getValue('plantId');
                $register = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(),0,"");
				$users = $register->fetchAll();
                
				$exists = false;
				foreach($users as $user)
				{
					if($user['username'] == $username)
					{
						$exists = true;
					}
				}
				if($exists)
				{
					$this->view->message = "Username already exists";
					return;
				}
                /**
                 * Wont work, lacks add user
                 */
                $register->setUserName($username);
                $register->setRole($role);                
                $register->save();
//                $register = $register->createAccount($username, $role, $plantId);
                $this->_redirect('/userprofile/add?id=' . $register);
            } else {
                $form->populate($formData);
            }
        }
    }

    public function deleteaccAction() {
        if ($this->getRequest()->isPost()) {			
            $del = $this->getRequest()->getPost('del');
            if ($del == 'Delete') {
            	$role = Zend_Registry::get('role');
				$id = $this->getRequest()->getPost('id');
				if($role == 'ca'){					
					$umodel = new Model_DbTable_Userprofile(Zend_Db_Table::getDefaultAdapter(),$id);
                    $comGrp = $umodel->getList(array("plantId" => $umodel->getPlantId()));
					$belong = false;
					foreach($compGrp as $user){
						if((int)$id == (int)$user['id']){
							$belong = true;
						}
                    }
					if(!$belong){
						return;
					}
				}
                $user = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(),Zend_Auth::getInstance()->getStorage()->read()->id,"");
                $user->deleteUser();
            }
            $this->_redirect("/plant/admin");
        }
    }

    public function resetpasswordAction() {
        try {
            if ($this->getRequest()->isPost()) {

                $resetPass = $this->getRequest()->getPost('resetpass');
                if ($resetPass == 'Reset Password') {
                	$role = Zend_Registry::get('role');
					$userid = $this->getRequest()->getPost('id');
					if($role == 'ca')
					{
						$umodel = new Model_DbTable_Userprofile(Zend_Db_Table::getDefaultAdapter(),Zend_Auth::getInstance()->getStorage()->read()->id);
                        $comGrp = $umodel->getList(array("plantId" => $umodel->getPlantId()));

						$belong = false;
						foreach($compGrp as $user)
						{
							if((int)$userid == (int)$user['id'])
							{
								$belong = true;
							}
						}	
						if(!$belong)
						{
							return;
						}
					}
                    $myuser = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(),Zend_Auth::getInstance()->getStorage()->read()->id,"");
                    $status = $myuser->resetPassword($userid);
                    if ($status['rowsAffected'] == 1)
                        $this->view->message = 'Check mail, Password was reset';
                    else
                        $this->view->message = 'Resetting Password Failed';
					
					if($this->_getParam("source") == "adminlist")
						$this->_redirect("administration/list");
					else
						$this->_redirect("plant/admin");
                }
            }
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function transformAction() {
        try {
            if ($this->getRequest()->isPost()) {
                $adminId = Zend_Auth::getInstance()->getStorage()->read()->id;
                $transform = $this->getRequest()->getPost('transform');
                if ($transform == 'Transform') {
                    //getting the data of the user to transform
                    $transformId = $this->getRequest()->getPost('id');
                    $data = AdministrationController::getUserCredentials($transformId);
                    $authAdapter = $this->getAdminLoginAdapter();
                    $authAdapter->setIdentity($data['username'])
                            ->setCredential($data['password']);
                    $auth = Zend_Auth::getInstance();
                    $result = $auth->authenticate($authAdapter);
                    if ($result->isValid()) {
                        $identity = $authAdapter->getResultRowObject();
                        $authStorage = $auth->getStorage();
                        // Add original id in the resultrowobject to use in restore
                        $authStorage->write($identity);
                        Zend_Registry::set('id', Zend_Auth::getInstance()->getStorage()->read()->id);
                        $toWrite = new Zend_Session_Namespace('Zend_Auth');
                        $toWrite->adminId = $adminId;
                        $this->_redirect('dashboard/index');
                    }
                }
            }
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function restoreAction() {
        try {
            $toWrite = new Zend_Session_Namespace('Zend_Auth');
            $restoreId = $toWrite->adminId;
            unset($toWrite->adminId);
            $data = AdministrationController::getUserCredentials($restoreId);
            $authAdapter = $this->getAdminLoginAdapter();
            $authAdapter->setIdentity($data['username'])
                    ->setCredential($data['password']);
            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($authAdapter);
            if ($result->isValid()) {
                $identity = $authAdapter->getResultRowObject();
                $authStorage = $auth->getStorage();
                $identity->role = 'sa';
                $authStorage->write($identity);
                Zend_Registry::set('id', Zend_Auth::getInstance()->getStorage()->read()->id);
                $this->_redirect('dashboard/index');
            }
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function listAction() {        
        try {
            $this->view->headTitle('List Users', 'PREPEND');            
            $umodel = new Model_DbTable_Userprofile(Zend_Db_Table::getDefaultAdapter(),Zend_Auth::getInstance()->getStorage()->read()->id);
            $plantId = $umodel->getPlantId();
            $listObj = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(),Zend_Auth::getInstance()->getStorage()->read()->id,"");
            $users = $listObj->getList(array("plantId" => $plantId));
            $this->view->users = $users;
            
            $validcc = $listObj->getConferenceChairman();
            $this->view->validcc = $validcc;
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function setccAction() {
        $id = $this->_getParam('id', 0);
        $uModel = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(),$id,"");
        $setcc = $uModel->setConfChair();

        $this->_redirect('/administration/list');
    }

    public function unsetccAction() {
        $id = $this->_getParam('id', 0);
        $uModel = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(),Zend_Auth::getInstance()->getStorage()->read()->id,"");
        $uModel->unSetConfChair();
        
        $this->_redirect('/administration/list');
    }

    public function showmenuAction() {
        $this->_helper->getHelper('layout')->disableLayout();
        $role = 'us';
        $navTag = 'nav';
        if ($role == 'sa') {
            $navTag = 'adminnav';
        } else if ($role == 'ca') {
            $navTag = 'navca';
        }
        $navContainerConfig = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', $navTag);
        $navContainer = new Zend_Navigation($navContainerConfig);
        Zend_Registry::set('navcontainer', $navContainer);
        $acl = new Model_HiveAcl();
        $this->view->navigation($navContainer)->setAcl($acl)->setRole($role);
    }

    public function mailnotifyAction() {
    	
		
        $gtdatamodel = new Model_DbTable_Gtdata();
        $gtdata = $gtdatamodel->getUnmailedData();
        $this->view->gtdata = $gtdata;
    }

    public function sendmailAction() {
        $gtdatamodel = new Model_DbTable_Gtdata(Zend_Db_Table::getDefaultAdapter(),0);
        $gtdata = $gtdatamodel->getUnmailedData();
        $uModel = new Model_DbTable_Userprofile(Zend_Db_Table::getDefaultAdapter(),0);
        $users = $uModel->getList();
        $con['finding'] = 'findings';
        $con['upgrade'] = 'upgrades';
        $con['lte'] = 'lte';
        $mailbody = "<div style='width: 100%; '><div style='border-bottom: solid 1px #aaa; margin-bottom: 10px;'>";
        $mailbody = $mailbody . "<a href='http://www.hiveusers.com' style='text-decoration: none;'><span style='font-size: 34px; color: #2e4e68;'><b>hive</b></span>";
        $mailbody = $mailbody . "<span style='font-size: 26px; color: #83ac52; text-decoration:none;'><b>users.com</b></span></a><br/><br/>GT Data Notification</div>";
        $mailbody = $mailbody . "<div style='margin-bottom:10px;'><span style='color: #000;'><i>Hello</i>,<br/><br/>The following Findings/Upgrades/LTEs were added: <br/><br/></span>";
        foreach ($gtdata as $list) {
        	$list['data'] = strip_tags($list['data']);
            if(strlen($list['data'])>200){
                $list['data'] = substr($list['data'], 0, 200);
                $list['data'] = $list['data']."...<br/><br/>";
                $list['data'] = $list['data']."<a href='http://www.hiveusers.com/" . $con[$list['type']] . "/view?id=" . $list['id'] . "' style='text-decoration: none;'>Read More >></a>";
            }
            $list['data'] = $list['data']."<hr/>";

            $mailbody = $mailbody . "<a href = 'http://www.hiveusers.com/" . $con[$list['type']] . "/view?id=" . $list['id'] . "' style='text-decoration: none;'>" . $list['title'] . "</a> (" . ucfirst($list['type']) . ")";
            $mailbody = $mailbody . "<br/><i>".$list['data']."</i><br/><br/>";
        }
        $mailbody = $mailbody . "</div><div style='border-top: solid 1px #aaa; color:#aaa; padding: 5px;'><center>This is a generated mail, please do not Reply.</center></div></div>";
		$mcon = Zend_Registry::get('mailconfig');
		$config = array('ssl' => $mcon['ssl'], 'port' => $mcon['port'], 'auth' => $mcon['auth'], 'username' => $mcon['username'], 'password' => $mcon['password']);
		$tr = new Zend_Mail_Transport_Smtp($mcon['smtp'],$config);
		Zend_Mail::setDefaultTransport($tr);
        $mail = new Zend_Mail();
        $mail->setBodyHtml($mailbody);
        $mail->setFrom($mcon['fromadd'], $mcon['fromname']);
        foreach ($users as $user) {
            $mail->addTo($user['email'], $user['firstName']);
        }
        $mail->setSubject('GT Data Notification');
        $mail->send();
        $gtdatamodel->setMailed();
        $this->_redirect("/administration/mailnotify");
    }

	public function adminlistAction()
	{
		
		$this->_helper->getHelper('layout')->disableLayout();
		//$pid = $this->getRequest()->getPost('plantid');
		$pid = $this->_getParam('plantid',0);
		$this->view->plantid = $pid;
		$users = Model_DbTable_User::getList(array("plantId" => $pid));
		
		for($i=0;$i<count($users);$i++){
			$uprofile = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$users[$i]['id']);
			$users[$i]['fullname'] = $uprofile->getFullName();
		}
		if(count($users) == 0)
		{
			echo "<center>No users added</center>";
			$this->view->usercount = 0;
			return;
		}
		$this->view->users = $users;
		$this->view->usercount = Model_DbTable_User::getCount(array("plantId" => $pid));
		$this->view->confchair = Model_DbTable_User::getConferenceChairman();
	}
	
	public function dbinitAction()
	{
		//delete all users except admin
		
		/*$umodel = new Model_DbTable_User();
		
		$users = $umodel->fetchAll();
		
		foreach($users as $user)
		{
			if($user['id'] == 2)
				continue;
			//$umodel->deleteAccount($user['id']);
		}
		
		echo "All users except administrator have been deleted<br>";
		
		//delete all plants
		
		$pmodel = new Model_DbTable_Plant();
		$pmodel->delete();
		
		$data = array(
			'plantId' => 1,
			'corporateName' => 'LBTMS',
			'corporateLocation' => 'Mylapore',
			'corporateProvince' => 'Chennai',
			'corporateState' => 'Tamil Nadu',
			'corporateCountry' => 'India',
			'corporateZip' => '600004',
			'corporateTelephone' => '29381903',
			'corporateFax' => '1232131',
			'corporateWebsite' => 'www.hiveusers.com',
			'plantName' => 'Hive',
			'plantLocation' => 'Chennai',
			'plantState' => 'Tamil Nadu',
			'plantCountry' => 'India',
			'plantZip' => '600004',
			'plantTelephone' => '1231313',
			'GTStartMax' => '123',
			'GTStartMin' => '1232',
			'GTStartAvg' => '32',
			'GTTripMax' => '23',
			'GTTripMin' => '321',
			'GTTripAvg' => '232',
			'plantFax' => '123123',
			'plantWebsite' => 'www.hiveusers.com',
			'numOfGT' => '0',
			'GTMake' => 'Siemens',
			'GTBaseModel' => 'v93.4A',
			'plantAmbientTempMax' => '123',
			'plantAmbientTempMin' => '233',
			'plantAmbientTempAvg' => '232',
			'PLFMax' => '12',
			'PLFMin' => '23',
			'PLFAvg' => '23'
		);
		
		$pmodel->insert($data);
		
		//delete all gasturbines
		
		$gtmodel = new Model_DbTable_Gasturbine();
		$gtmodel->delete();
		
		//delete all gtdata
		
		$gtdatamodel = new Model_DbTable_Gtdata();
		$gtdatamodel->delete();
		
		//delete all bookmarks
		
		$bmmodel = new Model_DbTable_Bookmark();
		$bmmodel->delete();
		
		//delete all presentations
		
		$presmodel = new Model_DbTable_Presentation();
		$presmodel->delete();
		
		//delete all conference
		
		$confmodel = new Model_DbTable_Conference();
		$confmodel->delete();
		
		//delete schedule
		
		$schedule = new Model_DbTable_Schedule();
		$schedule->delete();
		
		//gallery
		
		$gal = new Model_DbTable_Gallery();
		$gal->delete();
		
		//confpres
		
		$cpres = new Model_DbTable_ConfPresentation();
		$cpres->delete();
		
		//schevent
		
		$schevent = new Model_DbTable_ScheduleEvent();
		$schevent->delete();*/
				
		
	}
	

}
