<?php
class Model_DbTable_ScheduleEvent extends Zend_Db_Table_Abstract {
		
	/**
     * Overrides the $_name of the parent class to indicate the name of the table it maps to
     *
     * @var String
     */
	protected $_name = "schedule_event";
	
	/**
	 * Unique ID of the schedule event (Primary Key)
	 * 
	 * @var Integer
	 */
	protected $id;
	
	/**
	 * Id of the schedule to which the event belongs
	 * 
	 * @var Integer
	 */
	protected $scheduleId;
	
	/**
	 * The number which indicates the order in which the events will be displayed
	 * 
	 * @var Integer
	 */
	protected $eventNo;
	
	/**
	 * Date of the event
	 * 
	 * @var Date
	 */
	protected $eventDate;
	
	/**
	 * Event timings
	 * 
	 * @var String
	 */
	protected $timings;
	
	/**
	 * Event description
	 * 
	 * @var String
	 */
	protected $description;
	
	/**
	 * All details about the schedule event
	 * 
	 * @var array
	 */
	protected $eventData;
	
	/**
     * Initializes values and fetches the respective Conference details using conference ID as argument
     * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
     * @param Integer - Primary Key of the table
     */
    function __construct($config = array(),$id=0){
		parent::__construct($config);
		$this->eventData = array();
		
		if($id){
			$eventRow = $this->fetchRow("id = " . $id);
			
			$this->eventData = $eventRow->toArray();
			$this->id = $this->eventData['id'];
			$this->scheduleId = $this->eventData['scheduleId'];
			$this->eventNo = $this->eventData['event_no'];
			$this->eventDate = $this->eventData['event_date'];
			$this->timings = $this->eventData['timings'];
			$this->description = $this->eventData['description'];
			
		}
	}
	
	/**
	 * Gets the unique ID of the event
	 * 
	 * @return Integer
	 */
	public function getId(){
		return $this->id;
	}
	
	/**
	 * Gets the Id of schedule to which the event belongs
	 * 
	 * @return Integer
	 */
	public function getScheduleId(){
		return $this->scheduleId;
	}
	
	/**
	 * Sets the Id of the schedule to which the event belongs
	 * 
	 * @param Integer
	 */
	public function setScheduleId($scheduleId){
		$this->scheduleId = $scheduleId;
		$this->eventData['scheduleId'] = $scheduleId;
	}
	
	/**
	 * Gets the event number of the event
	 * 
	 * @return Integer
	 */
	public function getEventNo(){
		return $this->eventNo;
	}
	
	/**
	 * Sets the event number of the event
	 * 
	 * @param Integer
	 */
	public function setEventNo($eventNo){
		$this->eventNo = $eventNo;
		$this->eventData['event_no'] = $eventNo;
	}
	
	/**
	 * Gets the event date
	 * 
	 * @return Date
	 */
	public function getEventDate(){
		return $this->eventDate;
	}
	
	/**
	 * Sets the event date
	 * 
	 * @param Date
	 */
	public function setEventDate($date){
		$this->eventDate = $date;
		$this->eventData['event_date'] = $date;
	}
	
	/**
	 * Gets the event timings
	 * 
	 * @return String
	 */
	public function getTimings(){
		return $this->timings;
	}
	
	/**
	 * Sets the event timings
	 * 
	 * @param String
	 */
	public function setTimings($timings){
		$this->timings = $timings;
	}
	
	/**
	 * Gets the description of the event
	 * 
	 * @return String
	 */
	public function getDescription(){
		return $this->description;
	}
	
	/**
	 * Sets the event description
	 * 
	 * @param String
	 */
	public function setDescription($description){
		$this->description = $description;
		$this->eventData['description'] = $description;
	}
	
	/**
	 * Gets the entire details about the event
	 * 
	 * @return Array
	 */
	public function getEventData(){
		return $this->eventData;
	}
	
	/**
	 * Sets the details of the schedule event
	 * 
	 * @param Array
	 */
	public function setEventData($eventData){
		if($this->id == 0 || $this->id == $eventData['id']){
			$this->eventData = $eventData;
			$this->scheduleId = $eventData['scheduleId'];
			$this->eventNo = $eventData['event_no'];
			$this->eventDate = $eventData['event_date'];
			$this->timings = $eventData['timings'];
			$this->description = $eventData['description'];
		}
	}
	
	/**
	 * Gets the list of schedule events
	 * 
	 * @param Array - Extra options to filter the list
	 */
	public static function getList($options = array()){
		if(count($options)){
            if($options['orderby'] != ""){
            	$order = " ORDER BY " . $options['orderby'];
            }
			
			if($options['ul']){	
				$limit = " LIMIT " . $options['ll'] . " , " . $options['ul']; 
			}
			
			if($options['columns']['scheduleId']){
				$where = " WHERE scheduleId = " . $options['columns']['scheduleId'];
			}
			
			if($options['event_ids']){
				$where = " WHERE id IN(" . $options['event_ids'] . ")";
			}
			
        }
		$dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();		
        $stmt = $dbAdapter->query("SELECT * FROM schedule_event " . $where . " " . $order . " " . $limit);
        $list = $stmt->fetchAll();
        array($list);
        return $list;	
	}
	
	/**
	 * Gets the total number of schedule events
	 * 
	 * @param Array - Options to filter the results
	 * @return Integer - The number of schedule events
	 */
	public static function getCount($options = array())
	{
        if(count($options)){
        	if($options['cId']){
            	$where = " WHERE scheduleId = " . $options['scheduleId'];
            }
        }
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $dbAdapter->query("SELECT COUNT(*) as count FROM schedule_event" . $where);
        $countRow = $stmt->fetchAll();
        return $countRow[0]["count"];
	}
	
	/**
	 * Saves the local data to the database
	 */
	public function save(){
		if($this->id){
			$where = $this->getAdapter()->quoteInto('id = ?', $this->id);
			$this->update($this->eventData,$where);
		}
		else{
			$this->id = $this->insert($this->eventData);
		}
	}
	
	/**
	 * Deletes the schedule event
	 */
	public function deleteEvent(){
		$this->delete("id = ".$this->id);
	}
}