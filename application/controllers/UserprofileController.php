<?php

class UserprofileController extends Zend_Controller_Action {

    public function init() {
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('changepassword', 'json')
                	  ->initContext();
    }

    public function indexAction() {
    	try{
	        $uid = Zend_Auth::getInstance()->getStorage()->read()->id;
			$this->_redirect("/userprofile/view?id=".$uid);
		}
		catch(Exception $e){
			echo $e;
		}
    }

    public function addAction() {
        try {
        	$this->view->headTitle('Add User', 'PREPEND');
            $form = new Form_UserprofileForm();
            $form->submit->setLabel('Add');
            $this->view->form = $form;
            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    $id = $this->_getParam('id');
                    $userProfile = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter());
                    $content = $form->getValues();
                   	$users = Model_DbTable_Userprofile::getList();
					$exists = false;
					foreach($users as $user)
					{
						if($user['email'] == $content['email'])
						{
							$exists = true;
						}
					}
					if($exists)
					{
						$this->view->message = "Email already belongs to another user";
						return;
					}
					$plant = new Model_DbTable_Plant(Zend_Db_Table_Abstract::getDefaultAdapter(),$content['plantid']);
					$content['id'] = $id;
					$content['corporateName'] = $plant->getCorporateName();
					$content['plantName'] = $plant->getPlantName();
					$userProfile->setUserprofileData($content);
                    $userProfile->save();
                    $this->_redirect('/userprofile/view?id='.$id);
                } else {
                    $form->populate($formData);
                }
            }
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function editAction() {
        
        try {
            $form = new Form_UserprofileForm();
				
            $form->submit->setLabel('Save');
            if (Zend_Auth::getInstance()->getStorage()->read()->lastlogin == '') {
                $form->submit->setLabel('Save & Continue');
            }
            $this->view->form = $form;
			$user = new Model_DbTable_Userprofile(Zend_Db_Table::getDefaultAdapter(), Zend_Auth::getInstance()->getStorage()->read()->id);
			$this->view->headTitle($user->getFullName() . ' - Edit User', 'PREPEND');
            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    $content = $form->getValues();
					$content['id'] = Zend_Auth::getInstance()->getStorage()->read()->id;
                    $user->setUserprofileData($content);
                    $user->save();
                   	$this->_redirect('userprofile/view');
                }
                else {
                    $form->populate($formData);
                }
            } else {
				$pid = $user->getPlantId();
                $form->populate($user->getUserprofileData());
				$role = Zend_Registry::get("role");
				if($role == 'sa')
					$form->plantid->setValue($pid);
            }
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function viewAction() {
        try {
        	if($this->_getParam('id') != 0) {
        		$myUser = $this->_getParam('id');
        	}
        	else {
	            $role = Zend_Registry::get('role');
	            $myUser = Zend_Auth::getInstance()->getStorage()->read()->id;
	        }
            $userView = new Model_DbTable_Userprofile(Zend_Db_Table::getDefaultAdapter(),$myUser);
            $profileData = $userView->getUserprofileData();
			$pmodel = new Model_DbTable_Plant(Zend_Db_Table::getDefaultAdapter(), $profileData['plantId']);			
			$this->view->uPlantName = $pmodel->getPlantName();
			$this->view->uPlantId = $pmodel->getPlantId();
			
			$uModel = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(),$myUser, "");
			$iscc = $uModel->isConfChair();
    		$this->view->iscc = $iscc;
			if($this->view->iscc)
			{
				$this->view->iscc = " (Conference Chairman)";
			}
			else {
				$this->view->iscc = "";
			}
            $this->view->headTitle('Profile - ' .$profileData['firstName'] . ' ' . $profileData['lastName'], 'PREPEND');
            $this->view->profileData = $profileData;
			
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function changepasswordAction() {

        $this->view->headTitle('Change Password', 'PREPEND');
        try {
            $form = new Form_ChangePasswordForm();
            $form->submit->setLabel('Change Password');
            if (Zend_Auth::getInstance()->getStorage()->read()->lastlogin == '') {
                $form->submit->setLabel('Save & Continue');
            }
            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    $content = $form->getValues();
                    $userPass = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(),Zend_Auth::getInstance()->getStorage()->read()->id, "");
                    $oldPassword = $form->getValue('oldPassword');
                    $newPassword = $form->getValue('newPassword');
                    $reNewPassword = $form->getValue('reNewPassword');
                    if ($newPassword == $reNewPassword) {
                        $statusPass = $userPass->setPassword($newPassword);
                        if ($statusPass == 1) {
                            $this->view->message = 'Password has been changed';
							
							//Send Mail
							
							/*$umodel = new Model_DbTable_Userprofile(Zend_Db_Table::getDefaultAdapter(),$content['id']);
							$user = $umodel->getUserprofileData();
							$mailbody = "<div style='width: 100%; '><div style='border-bottom: solid 1px #aaa; margin-bottom: 10px;'>";
					        $mailbody = $mailbody . "<a href='http://www.hiveusers.com' style='text-decoration: none;'><span style='font-size: 34px; color: #2e4e68;'><b>hive</b></span>";
					        $mailbody = $mailbody . "<span style='font-size: 26px; color: #83ac52; text-decoration:none;'><b>users.com</b></span></a><br/><br/>Password Changed Successfully</div>";
					        $mailbody = $mailbody . "<div style='margin-bottom:10px;'><span style='color: #000;'><i>Hello " . $user['firstName'] . "</i>,<br/><br/>Your password has been changed successfully <br/><br/>Please click <a href = 'http://www.hiveusers.com/userprofile/view' style = 'text-decoration:none;'> <b>here<b> </a> to view your profile.</span>";	
							$mailbody = $mailbody . "</div><div style='border-top: solid 1px #aaa; color:#aaa; padding: 5px;'><center>This is a generated mail, please do not Reply.</center></div></div>";
							$mcon = Zend_Registry::get('mailconfig');
							$config = array('ssl' => $mcon['ssl'], 'port' => $mcon['port'], 'auth' => $mcon['auth'], 'username' => $mcon['username'], 'password' => $mcon['password']);
							$tr = new Zend_Mail_Transport_Smtp($mcon['smtp'],$config);
							Zend_Mail::setDefaultTransport($tr);
					        $mail = new Zend_Mail();
					        $mail->setBodyHtml($mailbody);
					        $mail->setFrom($mcon['fromadd'], $mcon['fromname']);
					        $mail->addTo($user['email'], $user['firstName']);
					        $mail->setSubject('Password Changed Successfully');
					        $mail->send();
							$this->_redirect("userprofile/index");*/
							
							//----//
                            if (Zend_Auth::getInstance()->getStorage()->read()->lastlogin == '')
                                $this->_redirect('userprofile/edit');
                        } else
                            $this->view->message = 'Wrong Password';
                    }
                    else {
                        $this->view->message = 'New password and Confirm Password do no match';
                    }
                } else {
                    $form->populate($formData);
                }
            }
            $this->view->form = $form;
        } catch (Exception $e) {
            echo $e;
        }
    }
}
