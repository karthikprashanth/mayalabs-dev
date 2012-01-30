<?php
class Model_DbTable_Conference extends Zend_Db_Table_Abstract {
		
	/**
     * Overrides the $_name of the parent class to indicate the name of the table it maps to
     *
     * @var String
     */
	protected $_name = 'conference';
	
	/**
	 * Unique ID of the conference (Primary Key)
	 * 
	 * @var Integer
	 */
	protected $conferenceId;
	
	/**
	 * The ID of the Plant which is going to host the conference
	 * 
	 * @var Integer
	 */		
	protected $host;
	
	/**
	 * A short description of the happenings of the conference
	 * 
	 * @var String
	 */
	protected $abstract;
	
	/**
	 * Year in which the conference is going to take place
	 * 
	 * @var Integer
	 */
	protected $year;
	
	/**
	 * Place where the conference is going to be held
	 * 
	 * @var String
	 */
	protected $place;
	
	/**
	 * Date and Time of last updation process
	 * 
	 * @var Timeupdate
	 */	
	protected $timeupdate;
	
	/**
	 * Full details about the conference
	 * 
	 * @var Array
	 */
	protected $conferenceData;
	
	/**
     * Initializes values and fetches the respective Conference details using conference ID as argument
     * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
     * @param Integer - Primary Key of the table
     */
	function __construct($config = array(),$conferenceId = 0)
    {
        parent::__construct($config);
		
        $this->conferenceData = array();

        if($conferenceId){
            $conferenceRow = $this->fetchRow("cId = " . $conferenceId);

            $this->conferenceData = $conferenceRow->toArray();
            $this->conferenceId = $this->conferenceData['cId'];
            $this->host = $this->conferenceData['host'];
            $this->abstract = $this->conferenceData['abstract'];
            $this->year = $this->conferenceData['year'];
            $this->place = $this->conferenceData['place'];
            $this->timeupdate = $this->conferenceData['timeupdate'];
				
        }
    }
	
	/**
	 * Gets the conference ID
	 * 
	 * @return Integer - Unique ID of the conference
	 */
	public function getConferenceId()
	{
		return $this->conferenceId;
	}
	
	/**
	 * Gets the ID of the plant which is going to host the conference
	 * 
	 * @return Integer - ID of the host plant
	 */
	public function getHost()
	{
		return $this->host;
	}
	
	/**
	 * Sets the ID of the plant which is going to host the conference
	 * 
	 * @param Integer - ID of the host plant
	 */
	public function setHost($host)
	{
		$this->host = $host;
		$this->conferenceData['host'] = $host;
	}
	
	/**
	 * Gets the Plant Name of the host
	 * 
	 * @return String - Name of the host plant
	 */
	public function getHostName(){
		$plant = new Model_DbTable_Plant(Zend_Db_Table_Abstract::getDefaultAdapter(),$this->host);
		return $plant->getPlantName();
	}
	
	/**
	 * Gets the abstract of the conference
	 * 
	 * @return String - A short description about the happenings of the concert
	 */
	public function getAbstract()
	{
		return $this->abstract;
	}
	
	/**
	 * Sets the abstract of the concference
	 * 
	 * @param String - Abstract of the conference
	 */
	public function setAbstract($abstract)
	{
		$this->abstract = $abstrac;
		$this->conferenceData['abstract'] = $abstract;
	}
	
	/**
	 * Gets the year of the conference
	 * 
	 * @return Integer - Year of the conference
	 */
	public function getYear()
	{
		return $this->year;
	}
	
	/**
	 * Sets the year of the conference
	 * 
	 * @param Integer - Year of the conference
	 */
	public function setYear($year)
	{
		$this->year = $year;
		$this->conferenceData['year'] = $year;
	}
	
	/**
	 * Gets the place of the conferece
	 * 
	 * @return String - Place where the conference is going to be held
	 */
	public function getPlace()
	{
		return $this->place;
	}
	
	/**
	 * Sets the place of the conferece
	 * 
	 * @param String - Place where the conference is going to be held
	 */
	public function setPlace($place)
	{
		$this->place = $place;
		$this->conferenceData['place'] = $place;
	}
	
	/**
	 * Gets the date and time of the last updation process
	 * 
	 * @return Timeupdate - Date and time of the last updation proces
	 */
	public function getTimeupdate()
	{
		return $this->timeupdate;
	}
	
	/**
	 * Sets the date and time of the last updation process
	 * 
	 * @param Timeupdate - Date and time of the last updation process
	 */
	public function setTimeupdate($timeupdate)
	{
		$this->timeupdate = $timeupdate;
		$this->conferenceData['timeupdate'] = $timeupdate;
	}
	
	/**
	 * Gets all the details about the conference
	 * 
	 * @return Array - Details of the conference
	 */
	public function getConferenceData()
	{
		return $this->conferenceData;
	}
	
	/**
	 * Sets all the details about the conference
	 * 
	 * @param Array - Details of the conference
	 */
	public function setConferenceData($conferenceData)
	{
        if($this->conferenceId == 0 || $conferenceData['cId'] == $this->conferenceId){
            $this->conferenceId = $conferenceData['cId'];
            $this->host = $conferenceData['host'];
            $this->abstract = $conferenceData['abstract'];
            $this->year = $conferenceData['year'];
            $this->place = $conferenceData['place'];
            $this->timeupdate = $conferenceData['timeupdate'];
            $this->conferenceData = $conferenceData;
		}
	}
	
	/**
	 * Gets details about all the existing conferences
	 * 
	 * @param Array - Options to filter the results
	 * @return Array - List of conferences
	 */
	public static function getList($options = array())
	{
        if(count($options)){
            if($options['orderby'] != ""){
                    $order = " ORDER BY " . $options['orderby'] . " DESC";
            }

            if($options['host'])
            {
                    $where = " WHERE host = " . $options['host'];
            }
			
			if($options['ul']){	
				$limit = " LIMIT " . $options['ll'] . " , " . $options['ul']; 
			}
			
        }
		$dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();		
        $stmt = $dbAdapter->query("SELECT * FROM conference " . $where . " " . $order . " " . $limit);
        $list = $stmt->fetchAll();
        array($list);
        return $list;
	}
	
	/**
	 * Gets the total number of conferences
	 * 
	 * @param Array - Options to filter the results
	 * @return Integer - The number of conferences
	 */
	public static function getCount($options = array())
	{
        if(count($options)){
                if($options['host']){
                        $where = " WHERE host = " . $options['host'];
                }
        }
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $dbAdapter->query("SELECT COUNT(*) as count FROM conference" . $where);
        $countRow = $stmt->fetchAll();
        return $countRow[0]["count"];
	}
	
	/**
	 * Updates the local data to the database
	 * 
	 * @return Integer - ID of the new conference for insert | Number of rows affected for update
	 */
	public function save()
	{
		if($this->conferenceId){
			$where = $this->getAdapter()->quoteInto('cId = ?', $this->conferenceId);
			$this->update($this->conferenceData,$where);
		}
		else{
			$this->conferenceId = $this->insert($this->conferenceData);
		}
	}
	
	/**
	 * Deletes the conference from the database
	 */
	public function deleteConference()
	{
    	$this->delete("cId = " . $this->conferenceId);
	}
		
	/*public function getConfDetail($cid){
			$cid = (int)$cid;
            $row = $this->fetchRow('cId = ' . $cid);
            if (!$row) {
                    throw new Exception("Could not find row $cid");
            }
            return $row->toArray();
	}
	
	public function getConfList()
	{
		$select = $this->select()
					   ->order('year DESC');
		$rSet = $this->fetchAll($select);
		return $rSet->toArray();
	}
	
	public function updateConf($cid, $content) {
        $where = $this->getAdapter()->quoteInto('cId = ?', $cid);
        $this->update($content, $where);
    }*/
	}