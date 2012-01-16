<?php

class AuthenticationController extends Zend_Controller_Action {

    public function init() {
        Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session());
        /* Initialize action controller here */
    }
	
	public function indexAction() {
        
    }
	
	public function loginAction() {
        try {
            
            $this->_helper->getHelper('Layout')->disableLayout();
            $this->view->headTitle('Login', 'PREPEND');
            if (Zend_Auth::getInstance()->hasIdentity()) {
                Zend_Registry::set('id', Zend_Auth::getInstance()->getStorage()->read()->id);
                $lastlogin = Zend_Auth::getInstance()->getStorage()->read()->lastlogin;
                if ($lastlogin == null) {
                    $this->_redirect('userprofile/changepassword');
                } else {
                    $t_url = 'dashboard/index';
                    $this->_redirect($t_url);
                }
            }

            $request = $this->getRequest();
            if (Zend_Registry::isRegistered('t')) {
                $t_url = Zend_Registry::get('t');
            } else {
                $turl = $this->_getParam('t', "");
                if ($turl != "") {
                    $t_url = $turl;
                } else {
                    $t_url = 'dashboard/index';
                }
            }

            $form = new Form_LoginForm(array('t' => $t_url));

            if ($request->isPost()) {
                if ($form->isValid($this->_request->getPost())) {
                    $authAdapter = $this->getAuthAdapter();
                    $username = $form->getValue('username');

                    $user = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(),0,$username);
                    $id = $user->getUserId();
					
                    $plain_password = $form->getValue('password');
                    $password = $user->encryptPassword($form->getValue('password'));

                    $authAdapter->setIdentity($username)
                                ->setCredential($password);
                    $auth = Zend_Auth::getInstance();
                    $result = $auth->authenticate($authAdapter);
                    
                    try {
                        if ($result->isValid()) {
                            $identity = $authAdapter->getResultRowObject();
                            $authStorage = $auth->getStorage();
                            $authStorage->write($identity);

                            //Generate secure ID for third party applications
//                            $ext = $this->getRequest()->getPost("ext");
//
//                            if ($ext == "yes") {
//                                $sid = Model_Functions::generateRandom(16)
//                                        . md5($username . $password . time()) .
//                                        Model_Functions::generateRandom(16);
//
//                                $umodel->setSecureId($sid);
//                                echo $sid;
//                            }
                            
                            // create object for registration for timestamp method
                            Zend_Registry::set('id', Zend_Auth::getInstance()->getStorage()->read()->id);
                            
                            $myUser = Zend_Auth::getInstance()->getStorage()->read()->id;
                            $role = Zend_Auth::getInstance()->getStorage()->read()->role;
                            //to check if he is a first time user,so as to decide
                            //if dashboard or profiles
                            $lastlogin = $authStorage->read()->lastlogin;
                            
                            //forum login
//                            global $phpbb_root_path, $phpEx, $user, $db, $config, $cache, $template;
//                            define('IN_PHPBB', true);
//                            $phpbb_root_path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
//                            $phpbb_root_path = substr($phpbb_root_path, 0, strlen($phpbb_root_path) - 24);
//                            $phpbb_root_path = $phpbb_root_path . "public" . DIRECTORY_SEPARATOR . "forums" . DIRECTORY_SEPARATOR;
//                            $phpEx = "php";
//                            include($phpbb_root_path . 'common.' . $phpEx);
//                            // Start session management
//                            $user->session_begin();
//                            $auth->acl($user->data);
//                            $auth->login($username, $plain_password, true, 1);
//                            $user->setup();

                            //-----//
                            if ($lastlogin == null) {
                                $toWrite = new Zend_Session_Namespace('Zend_Auth');
                                $user->setLastLogin();
                                echo "Entered 1st block, $t_url";
                                $this->_redirect('userprofile/changepassword');
                            } else {
                                $user->setLastLogin();
                                echo "Entered 1st block, $t_url";
                                $this->_redirect($form->getValue('t_url'));
                            }
                        } else {
                            $this->view->errorMessage = "Invalid Username or Password";
                        }
                    } catch (Exception $e) {
                        echo $e;
                    }
                } else {
                    $this->view->errorMessage = "Username or Password Empty";
                }
            }
            $this->view->form = $form;
        } catch (Exception $e) {
            echo $e;
        }
    }

    

    public function logoutAction() {
        //logout from forum
        try {
            $uid = Zend_Auth::getInstance()->getStorage()->read()->id;
            /*global $phpbb_root_path, $phpEx, $user, $db, $config, $cache, $template;
            define('IN_PHPBB', true);
            $phpbb_root_path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
            $phpbb_root_path = substr($phpbb_root_path, 0, strlen($phpbb_root_path) - 24);
            $phpbb_root_path = $phpbb_root_path . "public" . DIRECTORY_SEPARATOR . "forums" . DIRECTORY_SEPARATOR;
            $phpEx = "php";
            include($phpbb_root_path . 'common.' . $phpEx);
            // Start session management
            $user->session_kill();
            $user->session_begin();
            //Unset the third pary secure id
            $ext = $this->getRequest()->getPost("ext");
            $sid = $this->getRequest()->getPost("sid");*/

            /*if ($ext == "yes" && $sid != "") {
                $umodel = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(), $uid, "");
                $umodel->unSetSecureId($sid);
            }*/ 
            //logout from hive
            Zend_Auth::getInstance()->clearIdentity();
            $this->_redirect('');
        } catch (Exception $e) {
            echo $e;
        }
    }    
	
	public function getAuthAdapter() {
        try {
            $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
            $authAdapter->setTableName('users')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password');
            return $authAdapter;
        } catch (Exception $e) {
            echo $e;
        }
    }

    public static function isFirstTime() {
        try {
            $toRead = new Zend_Session_Namespace('Zend_Auth');
            $restoreId = $toRead->ftl;
            if ($restoreId == 'true') {
                return true;
            }
            return false;
        } catch (Exception $e) {
            echo $e;
        }
    }

    public static function clearFirstTime() {
        try {
            $toRead = new Zend_Session_Namespace('Zend_Auth');
            unset($toRead->ftl);
        } catch (Exception $e) {
            echo $e;
        }
    }

    /*public function extlogoutAction() {
        try{
        $this->_helper->getHelper('Layout')->disableLayout();
        $sid = $this->getRequest()->getPost('sid');
        $uname = $this->getRequest()->getPost('uname');
        echo $sid . $uname;
        if ($sid != "" && $uname != "") {
            $umodel = new Model_DbTable_User(Zend_Db_Table::getDefaultAdapter(),0,$uname);
            $umodel->unSetSecureId($sid);
        }
        }catch(Exception $e){
            echo $e;
        }
    }*/
}