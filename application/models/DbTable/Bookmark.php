<?php
class Model_DbTable_Bookmark extends Zend_Db_Table_Abstract {
		
	/**
     * Overrides the $_name of the parent class to indicate the name of the table it maps to
     *
     * @var String
     */
	protected $_name = 'bookmarks';
	
	/**
	 * Unique ID of the Bookmark
	 * 
	 * @var Integer
	 */
	protected $id;
	
	/**
	 * Name of the Bookmark
	 * 
	 * @var String
	 */
	protected $name;
	
	/**
	 * Id of the user who created the bookmark
	 * 
	 * @var Integer
	 */
	protected $userId;
	
	/**
	 * Category of the bookmark
	 * 
	 * @var String
	 */
	protected $category;
	
	/**
	 * Category id of the bookmark
	 * 
	 * @var Integer
	 */
	protected $catId;
	
	/**
	 * Update time of the bookmark
	 * 
	 * @var Timestamp
	 */
	protected $updatedTime;
	
	/**
	 * Array containing all the details
	 * 
	 * @var Array
	 */
	protected $bookmarkData;
	
	/**
	 * Initializes values and fetches the respective Bookmark details using the Id as argument
	 * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
	 * @param Integer - Primary Key of the table
	 */
    public function __construct($config = array(), $id = 0 ) {
        parent::__construct($config);
        $data = array();
        if($id){
            $data = $this->fetchRow("bmId = " . $id);
			
            $this->bookmarkData = $data->toArray();
            $this->id = $this->bookmarkData['bmId'];
			$this->name = $this->bookmarkData['bmName'];
			$this->userId = $this->bookmarkData['userId'];
			$this->category = $this->bookmarkData['category'];
			$this->catId = $this->bookmarkData['catId'];
			$this->updatedTime = $this->bookmarkData['updatedtime'];
        }
    }
	
	/**
	 * Gets the Id of the bookmark
	 * 
	 * @return Integer
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Gets the name of the bookmark
	 * 
	 * @return String
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Sets the name of the bookmark
	 * 
	 * @param String
	 */
	public function setName($name) {
		$this->name = $name;
		$this->bookmarkData['bmName'] = $name;
	}
	
	/**
	 * Gets the Id of the user who created the bookmark
	 * 
	 * @return Integer
	 */
	public function getUserId() {
		return $this->userId;
	}
	
	/**
	 * Sets the Id of the user who created the bookmark
	 * 
	 * @param Integer
	 */
	public function setUserId($userId) {
		$this->userId = $userId;
		$this->bookmarkData['userId'] = $userId;
	}

	/**
	 * Gets the category of the bookmark
	 * 
	 * @return String
	 */
	public function getCategory(){
		return $this->category;
	}
	
	/**
	 * Sets the category of the bookmark
	 * 
	 * @param String
	 */
	public function setCategory($category) {
		$this->category = $category;
		$this->bookmarkData['category'] = $category;
	}
	
	/**
	 * Gets the category Id of the bookmark
	 * 
	 * @return Integer
	 */
	public function getCatId() {
		return $this->catId;
	}
	
	/**
	 * Sets the category Id of the bookmark
	 * 
	 * @param Integer
	 */
	public function setCatId($catId) {
		$this->catId = $catId;
		$this->bookmarkData['catId'] = $catId;
	}
	
	/**
	 * Gets the updated time of the bookmark
	 * 
	 * @return Timestamp
	 */
	public function getUpdatedTime() {
		return $this->updatedTime;
	}
	
	/**
	 * Sets the updated time of the bookmark
	 * 
	 * @param Timestamp
	 */
	public function setUpdatedTime($updatedTime) {
		$this->updatedTime = $updatedTime;
		$this->bookmarkData['updatedtime'] = $updatedTime;
	}
	
	/**
	 * Gets all details about the bookmark
	 * 
	 * @return Array
	 */
	public function getBookmarkData() {
		return $this->bookmarkData;
	}
	
	/**
	 * Sets the bookmark data with the given paramater
	 * 
	 * @param Array
	 */
	public function setBookmarkData($bookmarkData){
		$this->bookmarkData = $bookmarkData;
		$this->id = $this->bookmarkData['bmId'];
		$this->name = $this->bookmarkData['bmName'];
		$this->userId = $this->bookmarkData['userId'];
		$this->category = $this->bookmarkData['category'];
		$this->catId = $this->bookmarkData['catId'];
		$this->updatedTime = $this->bookmarkData['updatedtime'];
	}
	
	/**
	 * Saves the local values to the database
	 */
	public function save() {
		if($this->id) {
			$where['id = ?'] = $this->id;
			$this->update($this->bookmarkData,$where);
		}
		else {						
			$this->id = $this->insert($this->bookmarkData);
		}
	}
	
	/**
	 * Gets the list of bookmarks based on the options
	 * 
	 * @param Array
	 * @return Array
	 */
	public static function getList($options = array()) {
			
		$dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
		
		if($options['columns']['userId']) {
			$where = " WHERE userId = " . $options['columns']['userId'];
			if($options['columns']['category']) {
				$where .= " AND category = '" . $options['columns']['category'] . "'";
			}
			if($options['columns']['catId']) {
				$where .= " AND catId = " . $options['columns']['catId'];
			}
		}
		if($options['limit']) {
			$limit = " LIMIT 0," . $options['limit'];
		}
		$query = "SELECT * FROM bookmarks" . $where . $limit;		
		$stmt = $dbAdapter->query("SELECT * FROM bookmarks" . $where. " ORDER BY updatedtime DESC" . $limit);
        $list = $stmt->fetchAll();
        array($list);

        return $list;
	}
	
	/**
	 * Deletes the bookmark
	 */
	public function deleteBookmark() {
		$this->delete("bmId = ".$this->id);
	}
}