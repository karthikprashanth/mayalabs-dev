<?php

class Model_DbTable_Userprofile extends Zend_Db_Table_Abstract {

    /**
     * Overrides the $_name of the parent class to indicate the name of the table it maps to
     *
     * @var String
     */
    protected $_name = 'userprofile';
    /**
     * Unique Id of the User (Primary Key)
     *
     * @var Integer
     */
    protected $userId;
    /** ID of the plant to which the user belongs
     *  
     * @var Integer 
     */
    protected $plantId;
    /**
     * First Name of the User
     *
     * @var String
     */
    protected $firstName;
    /**
     * Full Name of the User
     * 
     * @var String
     */
    protected $fullName;
    /**
     * Name of the Plant to which the User belongs to
     *
     * @var String
     */
    protected $plantName;
    /**
     * Name of the Corporate to which the User belongs to
     * 
     * @var String
     */
    protected $corporateName;
    /**
     * Email address of the user
     *
     * @var String
     */
    protected $email;
    /**
     * Contains all the profile details about the User
     * 
     * @var Array
     */
    protected $userprofileData;

    /**
     * Initializes values and fetches the respective User Profile details using the userId as argument
     * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
     * @param Integer - Primary Key of the table
     */
    function __construct($config = array(), $userId = 0) {
        parent::__construct($config);

        $userData = array();
		
        if ($userId) {
        	
            $userprofileRow = $this->fetchRow("id = " . $userId);
			
            $this->userprofileData = $userprofileRow->toArray();
			
            $this->userId = $userprofileData['id'];
            $this->plantId = $userprofileData['plantId'];
            $this->firstName = $userprofileData['firstName'];
            $this->fullName = $userprofileData['firstName'] . " " . $userprofileData['lastName'];
            $this->plantName = $userprofileData['plantName'];
            $this->corporateName = $userprofileData['corporateName'];
            $this->email = $userprofileData['email'];
        }
        
    }

    /**
     * Gets the Unique Id of the user (Primary Key)
     *
     * @return Integer - The ID of the User
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * Gets the ID of the Plant to which the User belongs
     *
     * @return Integer - The ID of the plant to which the user belongs
     */
    public function getPlantId() {
        return $this->plantId;
    }

    /**
     * Sets the ID of the Plant to which the User belongs
     */
    public function setPlantId($plantid) {
        $where = $this->getAdapter()->quoteInto('id = ?', $this->userId);
        $this->update(array('plantId' => $plantid), $where);
    }

    /**
     * Gets the First Name of the User
     *
     * @return String - Full name of the user
     */
    public function getFirstName() {
        return $this->firstName;
    }
	
	/**
	 * Sets the First Name of the User
	 * 
	 * @param String - First Name of the User
	 */
	public function setFirstName($firstName)
	{
		$this->userprofileData['firstName'] = $firstName;
		$this->firstName = $firstName;
	}

    /**
     * Gets the Full Name of the User
     *
     * @return String - Full name of the user
     */
    public function getFullName() {
        return $this->fullName;
    }
	
    /** Sets the Full Name of the User
     *
     * @param String - Full name of the user
     */

    public function setFullName($fullName)
    {
            $this->fullName = $fullName;
    }
    /**
     * Gets the Name of the Plant to which the User belongs
     *
     * @return String
     */
    public function getPlantName() {
        return $this->plantName;
    }
	
    /**
     * Sets the Plant Name to which the user belongs
     *
     * @param String - Name of the Plant
     */
    public function setPlantName($plantName)
    {
            $this->userprofileData['plantName'] = $plantName;
            $this->plantName = $plantName;
    }
	
    /**
     * Gets the Corporate Name of the Plant to which the User belongs
     *
     * @return String
     */
    public function getCorporateName() {
        return $this->corporateName;
    }
	
    /**
     * Sets the Corporate Name of the plant to which the user belongs
     *
     * @param String - Corporate Name of the plant
     */
    public function setCorporateName($corporateName)
    {
            $this->userprofileData['corporateName'] = $corporateName;
            $this->corporateName = $corporateName;
    }
    /**
     * Gets the email address of the User
     * @return String
     */
    public function getEmailId() {
        return $this->email;
    }
	
    /** Sets the email ID of the User
     *
     * @param String - Email Id to be Set
     */
    public function setEmailId($email)
    {
            $this->userprofileData['email'] = $email;
            $this->email = $email;
    }
	
    /**
     * Gets all the user's profile details
     *
     * @return Array - Contains user's profile details
     */
    public function getUserprofileData() {
        return $this->userprofileData;
    }
	
    /**
     * Sets all the user's profile details
     *
     * @param Array - Contains user's profile details
     */
    public function setUserprofileData($userprofileData)
    {
        if($userprofileData['id'] == 0 | $userprofileData['id'] == $this->userId){
                $this->plantId = $userprofileData['plantId'];
                $this->plantName = $userprofileData['plantName'];
                $this->corporateName = $userprofileData['corporateName'];
                $this->firstName = $userprofileData['firstName'];
                $this->fullName = $userprofileData['firstName'] . " " . $userprofileData['lastName'];
                $this->email = $userprofileData['email'];
                $this->userprofileData = $userprofileData;
        }
    }
	
    /**
     * Gets the details of all Users
     *
     * @param Array - Options to filter the results
     * @return Array - All users' details
     */
    public static function getList($options = array())
    {
        if (count($options)){
        	$where = " WHERE ";
			foreach($options as $key => $value){
				$where .= $key . " = '" . $value . "' AND ";
			}
			$where = substr($where,0,strlen($where)-4);
        }

        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $dbAdapter->query("SELECT * FROM userprofile" . $where);
        $list = $stmt->fetchAll();
        array($list);
        return $list;
    }

    /**
     * Gets the Total number of users
     *
     * @param Array - Options to filter the results
     * @return Integer - The count of total number of users
     */
    public static function getCount($options = array())
    {
        if (count($options)){
        	$where = " WHERE ";
			foreach($options as $key => $value){
				$where .= $key . " = '" . $value . "' AND ";
			}
			$where = substr($where,0,strlen($where)-4);
        }

        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $dbAdapter->query("SELECT COUNT(*) AS count FROM userprofile " . $where);
        $countRow = $stmt->fetchAll();
        return $countRow[0]["count"];
    }
	
    /**
     * Updates details about user profile
     */
    public function save()
    {
        if($userprofileData['id']){
            $forumUserModel = new Model_DbTable_Forum_Users(Zend_Db_Table_Abstract::getDefaultAdapter(),$this->userId);
            $forumUserModel->setUserData($this->userprofileData);
            $forumUserModel->save();
            
            $where = $this->getAdapter()->quoteInto('id = ?', $this->userId);    
            $this->update($this->userprofileData, $where);
        }
        else {
            $forumUserModel = new Model_DbTable_Forum_Users(Zend_Db_Table_Abstract::getDefaultAdapter(),$this->userId);
            $forumUserModel->setUserData($this->userprofileData);
            $forumUserModel->save();
			
            $this->userId = $this->insert($this->userData);
			
        }
        
    }
	
    /**
     * Deletes the user's profile
     */
    public function deleteUserprofile()
    {
    	$this->delete('id = ' . $this->userId);
    }
    
	
    /*public function updateUser($id, $content) {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->update($content, $where);
        $forumUserModel = new Model_DbTable_Forum_Users();
        $forumwhere['user_id = ?'] = $id;
        $forumcontent = array('user_fullname' => $content['firstName'] . " " . $content['lastName']);
        $forumUserModel->update($forumcontent, $forumwhere);
    }

     public function updatePlantId($id, $plantid) {
      $where = $this->getAdapter()->quoteInto('id = ?', $id);
      $this->update(array('plantId' => $plantid), $where);
      } Changed to setPlantId */

    /* public function getUserList($pid) {
      $pid = (int) $pid;
      $row = $this->fetchAll('plantId = ' . $pid);
      if (!$row) {
      throw new Exception("Could not find row $id");
      }
      return $row->toArray();
      }

      public function getUser($id) {
      $id = (int) $id;
      $row = $this->fetchRow('id = ' . $id);
      if (!$row) {
      throw new Exception("Could not find row $id");
      }
      return $row->toArray();
      } */
     
     /*public function add($content) {
        
        $content = array_merge($content, array('id' => $this->userId,'corporateName' => $this->corporateName, 'plantName' => $this->plantName));
        $this->insert($content);

        $forumUserModel = new Model_DbTable_Forum_Users(Zend_Db_Table_Abstract::getDefaultAdapter(),$this->userId);
        $forumUserModel->setEmail($this->email);
        $forumUserModel->setUserPlantName($this->plantName);

        $forumwhere['user_id = ?'] = $this->userId;
        $forumcontent = array('user_plantname' => $this->plantName, 'user_fullname' => $this->fullName);
        $forumUserModel->update($forumcontent, $forumwhere);
        $where['id = ?'] = $this->userId;
        $uModel = new Model_DbTable_User();
        $user = $uModel->getUser($this->userId);
        $pwd = $user['password'];
        $data = array('password' => md5($pwd . "{" . $this->userId . "}"));
        $uModel->update($data, $where);

        $mailbody = "<div style='width: 100%; '><div style='margin-bottom: 10px; border-bottom: solid 1px #000;'>";
        $mailbody = $mailbody . "<a href='http://www.hiveusers.com' style='text-decoration: none;'><span style='font-size: 34px; color: #2e4e68;'><b>hive</b></span>";
        $mailbody = $mailbody . "<span style='font-size: 26px; color: #83ac52; text-decoration:none;'><b>users.com</b></span></a><br/><br/>";
        $mailbody = $mailbody . "Welcome to Hive</div>";

        $mailbody = $mailbody . "<p style='color: #000;'>With the changing business environment and a growing need to share information, connectivity has become one of the most integral aspect of a company's success.</p>
                                 <p style='color: #000;'><a href='http://hiveusers.com/' style='text-decoration:none; color: #2e4e68;'><b>Hive</b></a><span style='color: #000;'> is a customized web based networking solution that empowers individuals, groups and corporates to connect and share information with one another. It has been built
                                 keeping in mind the needs and requirements of the power sector. Featuring a powerful yet userfriendly interface, it boasts a dynamic range of features.</span></p>";

        $mailbody = $mailbody . "<ul style='color:#2e4e68;'><li>Customised Search Matrix</li><li>Article and Presentation Inventory</li><li>User Group Meeting Coverage</li><li>Discussion Forums</li><li>Quarterly Newsletter</li><li>Email Notification</li></ul>";

        $mailbody = $mailbody . "<p>To see more of what hive can do for you, login using the following credentials: <br/><br/>";
        $mailbody = $mailbody . "<b>Username : </b>" . $user['username'] . "<br/>";
        $mailbody = $mailbody . "<b>Password : </b>" . $pwd . "<br/></p><br/>Regards,<br/>Hive Team<br/>";

        $mailbody = $mailbody . "<div style='border-top: solid 1px #000; color:#aaa; padding: 5px;'><center>This is a generated mail, please do not Reply.</center></div></div>";

        $mcon = Zend_Registry::get('mailconfig');
        $config = array('ssl' => $mcon['ssl'], 'port' => $mcon['port'], 'auth' => $mcon['auth'], 'username' => $mcon['username'], 'password' => $mcon['password']);
        $tr = new Zend_Mail_Transport_Smtp($mcon['smtp'], $config);
        Zend_Mail::setDefaultTransport($tr);
        $mail = new Zend_Mail();
        $mail->setBodyHtml($mailbody);
        $mail->setFrom($mcon['fromadd'], $mcon['fromname']);
        $mail->addTo($content['email'], $content['firstName']);
        $mail->setSubject('Account Information');
        $mail->send();
        
    }*/
}