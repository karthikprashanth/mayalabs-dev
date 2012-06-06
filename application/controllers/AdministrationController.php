<?php

class AdministrationController extends Zend_Controller_Action {

    public function init() {
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('index', 'json')
                	  ->initContext();
    }
	
	public function indexAction() {
        $this->_redirect('administration/users');
    }
	
	public function createaccountAction() {
    	try{
	        $form = new Form_RegistrationForm();
	        $form->submit->setLabel('Add');
	        $this->view->form = $form;
			$this->view->headTitle('Create User Account','PREPEND');
	        if ($this->getRequest()->isPost()) {
	            $formData = $this->getRequest()->getPost();
	            if ($form->isValid($formData)) {
	                $username = $form->getValue('username');
	                $role = $form->getValue('role');
	                $plantId = $form->getValue('plantId');
	                $register = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(),0,"");
					$users = $register::getList();
	                
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
                    $password = "reason";
                    //Setting password as 'reason' for easy debugging. 
                    //Will be changed when mail module is done 
	                $register->setUserName($username);
                     
	                
	                $register->setRole($role);                
	                $register->save();
                    $register->setPassword($password);
                    $register->save();
                    
                    /* Adding forum user */
                    $forumData = array(
                        'user_id' => $register->getUserId(),
                        'user_type' => 0,
                        'group_id' => 2,
                        'username' => $register->getUserName(),
                        'user_password' => md5($password)
                    );
                    global $phpbb_root_path, $phpEx, $user, $db, $config, $cache, $template;
                    define('IN_PHPBB', true);
                    define('PHPBB_INSTALLED', true);
                    $phpbb_root_path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
                    $phpbb_root_path = substr($phpbb_root_path, 0, strlen($phpbb_root_path) - 24);
                    $phpbb_root_path = $phpbb_root_path . "public" . DIRECTORY_SEPARATOR . "forums" . DIRECTORY_SEPARATOR;
                    $phpEx = 'php';
                    include($phpbb_root_path . 'common.php');
                    include($phpbb_root_path . 'includes' . DIRECTORY_SEPARATOR . 'functions_user.php');
                    user_add($forumData);
                    /* Done Adding forum user */ 
	                $this->_redirect('/userprofile/add?id=' . $register->getUserId());
  	            } else {
  	                $form->populate($formData);
  	            }
            }
	     }
		 catch(Exception $e)
		 {
		 	echo $e;
		 }
    }

    public function deleteaccountAction() {
        if ($this->getRequest()->isPost()) {			
            $del = $this->getRequest()->getPost('del');
            if ($del == 'Delete') {
            	$role = Zend_Registry::get('role');
				$id = $this->getRequest()->getPost('id');
                $user = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(),$id,"");
                $user->deleteUser();
                
                /* Delete user from forum */
                global $phpbb_root_path, $phpEx, $user, $db, $config, $cache, $template;
                define('IN_PHPBB', true);
                define('PHPBB_INSTALLED', true);
                $phpEx = 'php';
                $phpbb_root_path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
                $phpbb_root_path = substr($phpbb_root_path, 0, strlen($phpbb_root_path) - 24);
                $phpbb_root_path = $phpbb_root_path . "public" . DIRECTORY_SEPARATOR . "forums" . DIRECTORY_SEPARATOR;
                include($phpbb_root_path . 'common.php');
                include($phpbb_root_path . 'includes' . DIRECTORY_SEPARATOR . 'functions_user.php');
                user_delete("remove", $id);
                /* Forum deleting done */
            }
            $this->_redirect("/administration/list");
        }
    }
	
	public function resetpasswordAction() {
        try {
            if ($this->getRequest()->isPost()) {
                $resetPass = $this->getRequest()->getPost('resetpass');
                if ($resetPass == 'Reset Password') {
                	$role = Zend_Registry::get('role');
					$userid = $this->getRequest()->getPost('id');
                    $user = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(),$userid,"");
					$password=Model_Functions::generateRandom(8);					
                    $user->setPassword($password);
                    $user->save();                    
                    
                    $forumUser = new Model_DbTable_Forum_Users(Zend_Db_Table_Abstract::getDefaultAdapter(),$userid);
                    $forumUser->setPassword($password);
                    $forumUser->save();
                    
                    $this->_redirect("administration/list");
                    
                }
            }
        } catch (Exception $e) {
            echo $e;
        }
    }
	
	public function listAction()
	{
		$this->view->headTitle('User Administration','PREPEND');
		if($this->getRequest()->isPost()){
			$this->_helper->getHelper('layout')->disableLayout();
			$pid = $this->getRequest()->getPost('plantid');
		}
		else{
			$role=Zend_Registry::get('role');
			if($role=='ca')
			{
				$userId = Zend_Auth::getInstance()->getStorage()->read()->id;
				$userProfile = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$userId);
				$pid=$userProfile->getPlantId();
			}
			else{
				$this->_redirect('administration/users');
			}
		}
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
	
	public function usersAction() {
        try {
        	$this->view->headTitle('User Administration','PREPEND');
          	$plantList = Model_DbTable_Plant::getList(array('orderby' => 'plantName'));
			
            $plants = new Zend_Paginator(new Zend_Paginator_Adapter_Array($plantList));
            $plants->setItemCountPerPage(5)
                    ->setCurrentPageNumber($this->_getParam('page', 1));

            $this->view->plants = $plants;
        } catch (Exception $exc) {
            echo $exc;
        }
    }

    public function setconferencechairmanAction() {
    	try{
	        $id = $this->getRequest()->getPost('id');
	        $user = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(),$id,"");
	        $setcc = $user->setConfChair();
			$user->save();
	        $this->_redirect('/administration/users');
		}
		catch(Exception $e){
			echo $e;
		}
    }

    public function unsetconferencechairmanAction() {
    	try{
	        $id = $this->getRequest()->getPost('id');
	        $user = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(),$id,"");
	        $user->unSetConfChair();
	        $user->save();
	        $this->_redirect('/administration/users');
		}
		catch(Exception $e){
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
        $user = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(), $id, "");
        $data = $user->getUserData();
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

   
    /*public function mailnotifyAction() {
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
    }*/
}