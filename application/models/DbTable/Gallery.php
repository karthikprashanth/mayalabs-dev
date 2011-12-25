<?php
class Model_DbTable_Gallery extends Zend_Db_Table_Abstract {
		
	/**
     * Overrides the $_name of the parent class to indicate the name of the table it maps to
     *
     * @var String
     */
	protected $_name = 'confgallery';
	
	/**
	 * Unique ID of the photo (Primary Key)
	 * 
	 * @var Integer
	 */
	protected $photoId;
	
	/**
	 * A brief description about the photo
	 * 
	 * @var String
	 */
	protected $tag;
	
	/**
	 * The binary data of the photo
	 * 
	 * @var Long Blob
	 */
	protected $binaryData;
	
	/**
	 * ID of the conference to which the photo belongs
	 * 
	 * @var Integer
	 */
	protected $conferenceId;
	
	/**
	 * All details of the photo
	 * 
	 * @var Array
	 */
	protected $photoData;
	
	/**
     * Initializes values and fetches the respective Photo details using the photoId as argument
     * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
     * @param Integer - Primary Key of the table
     */
    function __construct($config = array(),$photoId = 0)
    {
        parent::__construct($config);

        $photoData = array();

        if($photoId){
            $photoRow = $this->fetchRow("photoId = " . $photoId);

            $this->photoData = $photoRow->toArray();
			
            $this->photoId = $photoRow['photoId'];
            $this->tag = $photoRow['tag'];
            $this->binaryData = $photoRow['data'];
			$this->conferenceId = $photoRow['cId'];
        }
    }
	
	/**
	 * Gets the Unique ID of the photo
	 * 
	 * @return Integer - The unique ID of the photo
	 */
	public function getPhotoId()
	{
		return $this->photoId;
	}
	
	/**
	 * Gets the tag of the photo
	 * 
	 * @return String - A breif description about the photo
	 */
	public function getTag()
	{
		return $this->tag;
	}
	
	/**
	 * Sets the tag of the photo
	 * 
	 * @param String - A breif description about the photo
	 */
	public function setTag($tag)
	{
		$this->tag = $tag;
		$this->photoData['tag'] = $tag;
	}
	
	/**
	 * Gets the binary data of the photo
	 * 
	 * @return Long Blob - The binary data of the photo
	 */
	public function getBinaryData()
	{
		return $this->binaryData;
	}
	
	/**
	 * Sets the binary data of the photo
	 * 
	 * @param Long Blob - The binary data of the photo
	 */
	public function setBinaryData($binaryData)
	{
		$this->binaryData = $binaryData;
		$this->photoData['data'] = $binaryData;
	}
	
	/**
	 * Gets the ID of the conference to which the photo belongs
	 * 
	 * @return Integer - Conference ID of the photo
	 */
	public function getConferenceId()
	{
		return $this->conferenceId;
	}
	
	/**
	 * Sets the ID of the conference to which the photo belongs
	 * 
	 * @param Integer - Conference ID of the photo
	 */
	public function setConferenceId($conferenceId)
	{
		$this->conferenceId = $conferenceId;
		$this->photoData['cId'] = $conferenceId;
	}
	
	/**
	 * Gets all the details about the photo
	 * 
	 * @return Array - Details of the photo
	 */
	public function getPhotoData()
	{
		return $this->photoData;
	}
	
	/**
	 * Sets the details about the photo
	 * 
	 * @param Array - Details of the photo
	 */
	public function setPhotoData($photoData)
	{
		if($this->photoId == 0 | $this->photodata['photoId'] == $this->photoId){
			$this->photoId = $photoData['photoId'];
			$this->tag = $photoData['tag'];
			$this->binaryData = $photoData['data'];
			$this->conferenceId = $photoData['cId'];
			$this->photoData = $photoData;
		}
	}
	
	/**
	 * Gets details about all photos
	 * 
	 * @param Array - Options to filter the results
	 * @return Array - Details about all photos
	 */
	public static function getList($options = array())
	{
		if(count($options)){
			if($options['cId']){
				$where = " WHERE cId = " . $options['cId'];
			}
		}
		$stmt = $dbAdapter->query("SELECT * FROM confgallery " . $where);
        $list = $stmt->fetchAll();
        array($list);
        return $list;
	}
	
	/**
	 * Gets the total number of photos
	 * 
	 * @param Array - Options to filter the results
	 * @return Integer - The total number of photos
	 */
	public static function getCount($options = array())
	{
		if(count($options)){
			if($options['cId']){
				$where = " WHERE cId = " . $options['cId'];
			}
		}
		$stmt = $dbAdapter->query("SELECT COUNT(*) AS count FROM confgallery " . $where);
        $countRow = $stmt->fetchAll();
        return $countRow[0]["count"];
	}
	
	/**
	 * Saves the local data of the photo to the database
	 * 
	 * @return Integer - ID of the new photo added for insert | Number of rows affected for update
	 */
	public function save()
	{
        if($this->photoId)
        {
            $where = $this->getAdapter()->quoteInto('photoId = ?', $this->photoId);
            $this->update($this->photoData, $where);
        }
        else{
            $this->photoId = $this->insert($this->photoData);
            $this->photoId;
        }
	}
	
	/**
	 * Deletes the photo from the database
	 */
	public function deletePhoto()
	{
    	$this->delete("photoId = " . $this->photoId);
	}
	
	/*public function getGallery($id){
		$row = $this->fetchAll('cId = ' . $id);
		return $row->toArray();
	}
	
	public function getPhoto($id)
	{
		$row = $this->fetchRow("photoId = " . (int)$id);
		return $row->toArray();
	}*/
	
}