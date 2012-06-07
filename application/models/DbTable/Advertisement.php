<?php

class Model_DbTable_Advertisement extends Zend_Db_Table_Abstract {
	
	/**
     * Overrides the $_name of the parent class to indicate the name of the table it maps to
     *
     * @var String
     */
	protected $_name = 'advertisements';
	
	/**
	 * Unique ID of the advertisement
	 * 
	 * @var Integer
	 */
	protected $id;
	
	/**
	 * Title of the advertisement
	 * 
	 * @var String
	 */
	protected $title;
	
	/**
	 * Model of Gasturbine for which the advertisement is added
	 * 
	 * @var String
	 */
	protected $gtModel;
	
	/**
	 * Description of the advertisement
	 * 
	 * @var String
	 */
	protected $description;
	
	/**
	 * Image of the advertisement
	 * 
	 * @var Blob
	 */
	protected $image;
	
	/**
	 * Time of last update
	 * 
	 * @var Timestamp
	 */
	protected $timeupdate;
	
	/**
	 * Entire details about the advertisement
	 * 
	 * @var Array
	 */
	protected $advertData;
	
	/**
     * Initializes values and fetches the respective Advertisement details using ID as argument
     * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
     * @param Integer - Primary Key of the table
     */
	function __construct($config = array(),$advertId = 0)
    {
        parent::__construct($config);
		
        $this->advertData = array();

        if($advertId){
            $advertRow = $this->fetchRow("advertId = " . $advertId);

            $this->advertData = $advertRow->toArray();
			$this->id = $this->advertData['advertId'];
			$this->title = $this->advertData['title'];
			$this->gtModel = $this->advertData['GTModel'];
			$this->description = $this->advertData['description'];
			$this->image = $this->advertData['advertImage'];
			$this->timeupdate = $this->advertData['timeupdate'];
        }
    }
	
	/**
	 * Gets the adverisement Id
	 * 
	 * @return Integer
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Gets the advertisement title
	 * 
	 * @return String
	 */
	public function getTitle(){
		return $this->title;
	}
	
	/**
	 * Sets the advertisement title
	 * 
	 * @param String
	 */
	public function setTitle($title){
		$this->title = $title;
		$this->advertData['title'] = $title;
	}
	
	/**
	 * Gets the GT Model
	 * 
	 * @return String
	 */
	public function getGTModel(){
		return $this->gtModel;
	}
	
	/**
	 * Sets the GT Model
	 * 
	 * @param String
	 */
	public function setGTModel($gtModel){
		$this->gtModel = $gtModel;
		$this->advertData['GTModel'] = $gtModel;
	}
	
	/**
	 * Gets the description
	 * 
	 * @return String
	 */
	public function getDescription(){
		return $this->description;
	}
	
	/**
	 * Sets the description
	 * 
	 * @param String
	 */
	public function setDescription($description){
		$this->description = $description;
		$this->advertData = $description;
	}
	
	/**
	 * Gets the advertisement image
	 * 
	 * @return Blob
	 */
	public function getImage(){
		return $this->image;
	}
	
	/**
	 * Sets the advertisement image
	 * 
	 * @param Blob
	 */
	public function setImage($image){
		$this->image = $image;
		$this->advertData['image'] = $image;
	}
	
	/**
	 * Gets the timeupdate
	 * 
	 * @return Timestamp
	 */
	public function getTimeupdate(){
		return $this->timeupdate;
	}
	
	/**
	 * Sets the timeupdate
	 * 
	 * @param Timestamp
	 */
	public function setTimeupdate($timeupdate){
		$this->timeupdate = $timeupdate;
		$this->advertData['timeupdate'] = $timeupdate;
	}

	/**
	 * Gets the advertisement data
	 * 
	 * @return Array
	 */
	public function getAdvertData(){
		return $this->advertData;
	}
	
	/**
	 * Sets the advertisement data
	 * 
	 * @param Array
	 */
	public function setAdvertData($advertData){
		if($advertData['advertId'] == "" || $advertData['advertId'] == $this->id) {
			$this->advertData = $advertData;
			$this->id = $advertData['advertId'];
			$this->title = $this->advertData['title'];
			$this->gtModel = $this->advertData['GTModel'];
			$this->description = $this->advertData['description'];
			$this->image = $this->advertData['advertImage'];
			$this->timeupdate = $this->advertData['timeupdate'];
		}
	}
	
	/**
	 * Gets the list of all advertisements
	 * 
	 * @return Array
	 */
	public static function getList(){
		$dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();		
        $stmt = $dbAdapter->query("SELECT * FROM advertisements");
        $list = $stmt->fetchAll();
        array($list);
        return $list;
	}
	
	/**
	 * Generates a random advertisement
	 * 
	 * @return Array
	 */
	public static function getRandomAd(){
		$dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
		
		$stmt = $dbAdapter->query("SELECT * FROM advertisements ORDER BY RAND() LIMIT 1");
		$row = $stmt->fetchAll();
		array($row);
		return $row[0];
	}
	
	/**
	 * Saves the local values to the database
	 */
	public function save(){
		if($this->id) {
			$where['advertId = ?'] = $this->id;
			$this->update($this->advertData,$where);
		}
		else {
			$this->insert($this->advertData);
			$db = Zend_Db_Table_Abstract::getDefaultAdapter();
			$this->id = $db->lastInsertId();
			$this->advertData['advertId'] = $this->id;
		}
	}
	
	/**
	 * Deletes the advertisement
	 */
	public function deleteAdvertisement(){
		$this->delete("advertId = " . $this->id);
	}

}