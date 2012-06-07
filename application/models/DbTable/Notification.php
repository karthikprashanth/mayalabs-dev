<?php

class Model_DbTable_Notification extends Zend_Db_Table_Abstract {
	
	/**
     * Overrides the $_name of the parent class to indicate the name of the table it maps to
     *
     * @var String
     */ 
    protected $_name = 'notification';
	
	/**
	 * Unique ID of the notification
	 * 
	 * @var Integer
	 */
	protected $id;
	
	/**
	 * Category of the notification
	 * 
	 * @var String
	 */
	protected $category;
	
	/**
	 * Category ID of the notification
	 * 
	 * @var Integer
	 */
	protected $catId;
	
	/**
	 * Time update of the notification
	 * 
	 * @var Timestamp
	 */
	protected $timeupdate;
	
	/**
	 * User update of the notification
	 * 
	 * @var Integer
	 */
	protected $userId;
	
	/**
	 * Edited(1) or Added(0)
	 * 
	 * @var Integer
	 */
	protected $edited;
	
	/**
	 * Entire details about the notification
	 * 
	 * @var Array
	 */
	protected $notificationData;
	
	/**
	 * Initializes values and fetches the respective Notification details using the Id as argument
	 * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
	 * @param Integer - Primary Key of the table
	 */
    public function __construct($config = array(), $id = 0) {
        parent::__construct($config);
        $data = array();
        if($id){
            $data = $this->fetchRow("id = " . $id);
			
            $this->notificationData = $data->toArray();
			$this->id = $this->notificationData['id'];
			$this->category = $this->notificationData['category'];
			$this->catId = $this->notificationData['catid'];
			$this->timeupdate = $this->notificationData['timeupdate'];
			$this->userId = $this->notificationData['userupdate'];
			$this->edited = $this->notificationData['edit'];
        }
    }
	
	/**
	 * Gets the ID of the notification
	 * 
	 * @return Integer
	 */
	public function getId(){
		return $this->id;		
	}
	
	/**
	 * Gets the category of the notification
	 * 
	 * @return String
	 */
	public function getCategory(){
		return $this->category;
	}
	
	/**
	 * Gets the category ID of the notification
	 * 
	 * @return Integer
	 */
	public function getCatId(){
		return $this->catId;
	}
	
	/**
	 * Gets the timeupdate
	 * 
	 * @return Timeupdate
	 */
	public function getTimeupdate(){
		return $this->timeupdate;
	}
	
	/**
	 * Gets the userupdate
	 * 
	 * @return Integer
	 */
	public function getUserId(){
		return $this->userId;
	}
	
	/**
	 * Gets whether the notification is for an edit or an add
	 * 
	 * @return Integer
	 */
	public function getEdited(){
		return $this->edited;
	}
	
	/**
	 * Gets all details about the notification
	 * 
	 * @return Array
	 */
	public function getNotificationData(){
		return $this->notificationData;
	}
	
	/**
	 * Gets the list of all notifications based on the options
	 * 
	 * @param Array
	 * @return Array
	 */
	public static function getList($options = array()){
		if($options['limit']){
			$limit = " LIMIT " . $options['limit'];
		}
		$dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
		$stmt = $dbAdapter->query("SELECT * FROM notification ORDER BY timeupdate DESC".$limit);
		$list = $stmt->fetchAll();
		array($list);
		return $list;
	}    
}