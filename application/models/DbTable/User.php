<?php

class Model_DbTable_User extends Zend_Db_Table_Abstract {

    /**
     * Overrides the $_name of the parent class to indicate the name of the table it maps to
     *
     * @var String
     */
    protected $_name = 'users';
    /**
     * Unique ID of the User (Primary Key)
     * 
     * @var Integer
     */
    protected $userId;
    /**
     * Unique Login ID of the User
     *
     * @var String
     */
    protected $userName;
    /**
     * User's Role sa/ca/ed/us
     * 
     * @var String
     */
    protected $role;
    /**
     * Secure ID of the User for Third Party Applications
     *
     * @var String
     */
    protected $secureId;
    /**
     * Last Login Date and Time
     * 
     * @var Timeupdate
     */
    protected $lastLogin;
    /**
     * Conference Chairman
     *
     * @var Boolean
     */
    protected $isConfChair;
    /**
     * Contains all details about the User
     * 
     * @var Array
     */
    protected $userData;

    /**
     * Initializes values and fetches the respective User details using the userId as argument
     *
     * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
     * @param Integer - Primary Key of the table
     */
    function __construct($config = array(), $userId = 0,$username = "")
    {
        parent::__construct($config);

        $userData = array();

        if ($userId || $username != "") {
        	if($userId)
            	$userRow = $this->fetchRow("id = " . $userId);
			else if($username != "")
				$userRow = $this->fetchRow("username = '" . $username . "'");
			if(!count($userRow)){
				$this->userId = 0;
				return;
			}
            $this->userData = $userRow->toArray();
            $this->userId = $userRow['id'];
            $this->userName = $userRow['username'];
            $this->role = $userRow['role'];
            $this->secureId = $userRow['sid'];
            $this->lastLogin = $userRow['lastlogin'];
            $this->isConfChair = $userRow['conf_chair'];
        }
    }

    /**
     * Gets the User Id
     *
     * @return Integer - The unique User Id (Primary Key)
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Gets the User Name
     *
     * @return String - The unique login name of the User
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Sets the Username
     *
     * @param String - Name of the user to be set
     */
    public function setUserName($username)
    {
        $this->userData['username'] = $username;
		$this->userName = $username;
    }


    /**
     * Gets the Role of the user - sa/ca/ed/us
     *
     * @return String - The ACL Role of the user
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Sets the Role of the user
     *
     * @param - The ACL Role of the user
     */
    public function setRole($role)
    {
        $this->userData['role'] = $role;
		$this->role = $role;
    }

    /**
     * Gets the Secure ID for third party applications
     *
     * @return String - The secure login ID
     */
    public function getSecureId()
    {
        return $this->secureId;
    }

    /**
     * Sets the Secure Id for third party applications
     *
     * @param String - Secure ID
     */
    public function setSecureId($sid)
    {
        $esid = $this->secureId;
        if ($esid != "") {
            $sid = $esid . $sid . ",";
        } else {
            $sid = $sid . ",";
        }
        $where = $this->getAdapter()->quoteInto('id = ?', $this->userId);
        $this->update(array('sid' => $sid), array($where));
        $this->secureId = $sid;
		$this->userData['sid'] = $sid;
    }

    /**
     * Unsets the Secure ID of the User
     *
     * @param String - The secure ID which has to be unset
     */
    public function unSetSecureId($sid)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $this->userId);
        $uRow = $this->fetchRow($this->userId);
        $existingSId = explode(",", $this->secureId);
        for ($i = 0; $i < count($existingSId); $i++) {
            if ($existingSId[$i] == $sid)
                unset($existingSId[$i]);
        }
        $existingSId = implode(",", $existingSId);
        $rowaffected = $this->update(array('sid' => $existingSId), array($where));
		$this->secureId = $existingSId;
    }

    /**
     * Gets the last Date and Time the User logged in
     *
     * @return Timeupdate - Last Login time and date
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Sets the current date and time each time an user logs in
     */
    public function setLastLogin()
    {
        $time = date('Y-m-d H:i:s');
        $where = $this->getAdapter()->quoteInto('id = ?', $this->userId);
        $this->update(array('lastlogin' => $time), array($where));
		$this->lastLogin = $time;
		$this->userData['lastlogin'] = $time;
    }

    /**
     * Returns true if the user is the Conference Chairman
     *
     * @return Boolean - Whether the user is a Conference Chairman or not
     */
    public function isConfChair()
    {
        return $this->isConfChair;
    }

    /**
     * Sets the value of the conf_chair column to 1
     */
    public function setConfChair()
    {
		$this->isConfChair = 1;
		$this->userData['conf_chair'] = 1;
		
    }

    /**
     * Sets the value of the conf_chair column to 0
     */
    public function unSetConfChair()
    {
		$this->isConfChair = 0;
		$this->userData['conf_chair'] = 0;
    }

    /**
     * Gets all details about the User
     *
     * @return Array - All details of the user
     */
    public function getUserData()
    {
        return $this->userData;
    }

    /**
     *
     * @param Array - The user's details to be set
     */
    public function setUserData($userdata)
    {
        if($this->userId == 0 || $userdata['id'] == $this->userData['id']){
            $this->userId = $userdata['id'];
            $this->userName = $userdata['username'];
            $this->role = $userdata['role'];
            $this->lastLogin = $userdata['lastlogin'];
            $this->isConfChair = $userdata['conf_chair'];
            $this->userData = $userdata;
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
        if (count($options))
            $where = "WHERE id IN(SELECT id FROM userprofile WHERE plantId = " . $options['plantId'] . ")";
		
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $dbAdapter->query("SELECT * FROM users " . $where);
        $list = $stmt->fetchAll();        
        array($list);
        return $list;
    }

    /**
     * Gets the Total number of users
     *
     * @param Array - Options to filter results
     * @return Integer - The count of total number of users
     */
    public static function getCount($options = array())
    {
        if (count($options))
            $where = "WHERE id IN(SELECT id FROM userprofile WHERE plantId = " . $options['plantId'] . ")";
		
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $dbAdapter->query("SELECT COUNT(*) AS count FROM users " . $where);
        $countRow = $stmt->fetchAll();
		return $countRow[0]["count"];
    }

    /**
     * Gets the Conference Chairman's Id, if exists
     *
     * @return Integer
     */
    public static function getConferenceChairman(){
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $dbAdapter->query("SELECT * FROM users WHERE conf_chair = 1");
        $countRow = $stmt->fetchAll();
        if (count($countRow) != 0) {
            return $countRow[0]['id'];
        } else {
            return 0;
        }
    }
	
	/**
     * Checks if the $password is the user's actual password
     *
     * @param String - The password which has to be checked
     * @return Boolean - True if $password is the user's actual password
     */
    public function isPassword($password)
    {
        return !strcmp($this->userData['password'],$this->encryptPassword($password));
    }
    
    /**
     * Sets the User's Password and also the Forum Account's password
     *
     * @param String - The password which has to be set
	 * @return Integer - The number of rows affected indicating the success of the updation process
     */
    public function setPassword($password)
    {
		$this->userData['password'] = $this->encryptPassword($password);
    }

    /**
     * MD5's the password and does the necessary salting
     *
     * @param String - The Password which has to be encrypted
	 * @param Integer - Whether the encryption is for forum (plain MD5) or for general purpose(MD5 + Salt)
     * @return String - The encrypted password
     */
    public function encryptPassword($password,$forForum = 0)
    {
        if(!$forForum)
            return md5($password . '{' . $this->userId . '}');
        else
            return md5($password);
    }

    /**
     * Updates details about users
     */
    public function save()
    {   
        if($this->userId){            
            $where = $this->getAdapter()->quoteInto('id = ?', $this->userId);
            $this->update($this->userData, $where);
        }
        else {
            /*
             * Adding password as 'password' so that debugging will be easy
             * Has to be changed when mailing module is done
             */            
            $this->userData['conf_chair'] = 0;
            $this->userId = $this->insert($this->userData);
        }
        
    }

    /**
     * Deletes the User's Account and also his account from the forum
     */
    public function deleteUser()
    {
        $this->delete('id = ' . $this->userId);
     	   
        $userProfileModel = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$this->userId);
        $userProfileModel->deleteUserprofile();
		
    }
}