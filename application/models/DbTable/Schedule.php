<?php
class Model_DbTable_Schedule extends Zend_Db_Table_Abstract {
	
	/**
     * Overrides the $_name of the parent class to indicate the name of the table it maps to
     *
     * @var String
     */
	protected $_name = 'schedule';
	
	/**
	 * Unique ID of the schedule (Primary Key)
	 * 
	 * @var Integer
	 */
	protected $id;
	
	
	/**
	 * First day of the schedule
	 * 
	 * @var Date
	 */
	protected $firstDay;
	
	/**
	 * Last day of the schedule
	 * 
	 * @var Date
	 */
	protected $lastDay;
	
	/**
	 * Id of the conference to which this schedule belongs
	 * 
	 * @var Number
	 */
	protected $conferenceId;
	
	/**
	 * All data about the schedule
	 * 
	 * @var Array
	 */
	protected $scheduleData;
	
	/**
     * Initializes values and fetches the respective Conference details using conference ID as argument
     * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
     * @param Integer - Primary Key of the table
     */
	public function __construct($config = array(),$id){
		parent::__construct($config);
		
		$this->scheduleData = array();
		
		if($id){
			$scheduleRow = $this->fetchRow("id = ".$id);
			$this->scheduleData = $scheduleRow->toArray();
			$this->id = $this->scheduleData['id'];			
			$this->firstDay = $this->scheduleData['first_day'];
			$this->lastDay = $this->scheduleData['last_day'];
			$this->conferenceId = $this->scheduleData['cId'];
		}
		
	}
	
	/**
	 * Gets the Id of the Schedule
	 * 
	 * @return Integer
	 */
	public function getId(){
		return $this->id;
	}
	
	/**
	 * Gets the first day of the schedule
	 * 
	 * @return Date
	 */
	public function getFirstDay(){
		return $this->firstDay;
	}
	
	/**
	 * Sets the first day of the schedule
	 * 
	 * @param Date
	 */
	public function setFirstDay($firstDay){
		$this->firstDay = $firstDay;
		$this->scheduleData['firstDay'] = $firstDay;
	}
	
	/**
	 * Gets the last day of the schedule
	 * 
	 * @return Date
	 */
	public function getLastDay(){
		return $this->lastDay;
	}
	
	/**
	 * Sets the last day of the schedule
	 * 
	 * @param Date
	 */
	public function setLastDay($lastDay){
		$this->lastDay = $lastDay;
		$this->scheduleData['lastDay'] = $lastDay;
	}
	
	/**
	 * Gets the Id of the conference to which the schedule belongs
	 * 
	 * @return Integer
	 */
	public function getConferenceId(){
		return $this->conferenceId;
	}
	
	/**
	 * Sets the Id of the conference to which the schedule belongs
	 * 
	 * @param Integer
	 */
	public function setConferenceId($conferenceId){
		$this->conferenceId = $conferenceId;
		$this->scheduleData = $conferenceId;
	}
	
	/**
	 * Gets all the details about the schedule
	 * 
	 * @return Array
	 */
	public function getScheduleData()
	{
		return $this->scheduleData;
	}
	
	/**
	 * Sets all the details of the schedule
	 * 
	 * @param Array
	 */
	public function setScheduleData($scheduleData){
		if($this->id == 0 || $this->id == $scheduleData['id']){
			$this->scheduleData = $scheduleData;
			$this->firstDay = $scheduleData['first_day'];
			$this->lastDay = $scheduleData['last_day'];
			$this->conferenceId = $scheduleData['cId'];
		}
	}
	
	/**
	 * Gets the list of schedules
	 * 
	 * @param Array - Extra options to filter the list
	 */
	public static function getList($options = array()){
		if(count($options)){
            if($options['orderby'] != ""){
            	$order = " ORDER BY " . $options['orderby'] . " DESC";
            }
			
			if($options['ul']){	
				$limit = " LIMIT " . $options['ll'] . " , " . $options['ul']; 
			}
			
			if($options['columns']['cId']){
				$where = " WHERE cId = " . $options['columns']['cId'];
			}
        }
		$dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();		
        $stmt = $dbAdapter->query("SELECT * FROM schedule " . $where . " " . $order . " " . $limit);
        $list = $stmt->fetchAll();
        array($list);
        return $list;	
	}
	
	/**
	 * Gets the total number of schedules
	 * 
	 * @param Array - Options to filter the results
	 * @return Integer - The number of schedules
	 */
	public static function getCount($options = array())
	{
        if(count($options)){
                if($options['cId']){
                        $where = " WHERE cId = " . $options['cId'];
                }
        }
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $dbAdapter->query("SELECT COUNT(*) as count FROM schedule" . $where);
        $countRow = $stmt->fetchAll();
        return $countRow[0]["count"];
	}
	
	/**
	 * Saves the local content to the database
	 */
	public function save(){
		if($this->id){
			$where = $this->getAdapter()->quoteInto('id = ?', $this->id);
			$this->update($this->scheduleData,$where);
		}
		else{
			$this->id = $this->insert($this->scheduleData);
		}
	}
	
	public function deleteSchedule(){
		$this->delete("id = " . $this->id);
	}
}