<?php

class Model_DbTable_Gasturbine extends Zend_Db_Table_Abstract {

    /**
	 * Overrides the $_name of the parent class to indicate the name of the table it maps to
	 *
	 * @var String 
	 */
    protected $_name = 'gasturbines';

    /**
     * Primary Key of the table
     *
     * @var Integer
     */
    protected $gtId;

    /**
     * Name of the GT
     *
     * @var String
     */
    protected $gtName;

    /**
     * Model Number of the GT
     *
     * @var String
     */
    protected $gtModelNum;

    /**
     * Plant Id to which the GT belongs
     *
     * @var Integer
     */
    protected $plantId;
    

    /**
     * Gasturbine Data
     *
     * @var Array
     */
    protected $gtData;

    /**
     * Last Updated Time
     *
     * @var String
     */
    protected $time;

    /**
	 * Initializes values and fetches the respective GT details using the id as argument
	 * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
	 * @param Integer - Primary Key of the table
	 */
    public function  __construct($config = array(), $gtid = 0) {

        parent::__construct($config);

        $gtData = array();
        if($gtid){
            $gtRow = $this->fetchRow("GTId = " . $gtid);
            $this->gtData = $gtRow->toArray();
            
            $this->gtId = $this->gtData['GTId'];
            $this->gtName = $this->gtData['GTName'];
            $this->gtModelNum = $this->gtData['GTModelNum'];
            $this->plantId = $this->gtData['plantId'];
            $this->time = $this->gtData['timeupdate'];
        }
    }

    /**
     * Gets the GT Id
     *
     * @return Integer
     */
    public function getGTId(){
        return $this->gtId;
    }

    /**
     * Gets the GT Name
     *
     * @return String
     */
    public function getGTName(){
        return $this->gtName;
    }

    /**
     * Gets the GT Model Number
     *
     * @return String
     */
    public function getGTModelNum(){
        return $this->gtModelNum;
    }

    /**
     * Gets the Plant Id to which the GT belongs
     *
     * @return Integer
     */
    public function getPlantId(){
        return $this->plantId;
    }

    /**
     * Gets the Last Update Time
     *
     * @return String
     */
    public function getLastUpdateTime(){
        return $this->time;
    }

    /**
     * Gets all the GT Data
     *
     * @return Array
     */
    public function getGTData(){
        return $this->gtData;
    }
    
    /**
     * Gets all GTs
     *
     * @return Zend_Db_Select
     */
    public static function getList($options = array()){
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
		
        if (count($options['columns'])){
        	$where = " WHERE ";
			foreach($options['columns'] as $key => $value){
				$where .= $key . " = '" . $value . "' AND ";
			}
			$where = substr($where,0,strlen($where)-4);
			
        }
        
        $stmt = $dbAdapter->query("SELECT * FROM gasturbines " . $where);
        $list = $stmt->fetchAll();
        array($list);
        return $list;
    }
	
	/**
	 * Gets the name of the Gasturbine given as argument
	 * 
	 * @param GT Id
	 */
	public static function getGTurbineName($gtid){
		$dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
		$stmt = $dbAdapter->query("SELECT * FROM gasturbines WHERE GTId = " . $gtid);
        $list = $stmt->fetchAll();
		array($list);
		return $list[0]['GTName'];
	}
    
    /**
     * Sets the GT Name
     *
     * @param String - Name of the GT
     */
    public function setGTName($gtName){
        $this->gtName = $gtName;
    }

    /**
     * Sets the GT Model Number
     *
     * @param String - Model Number of the GT
     */
    public function setGTModelNum($gtModelNum){
        $this->gtModelNum = $gtModelNum;
    }

    /**
     * Sets the Plant to which the GT belongs
     *
     * @param Integer - Id of the Plant
     */
    public function setPlantId($pid){
        $this->plantId = $pid;
    }

    /**
     * Sets all the data in the GT
     *
     * @param Array
     */
    public function setGTData($data){
        if($this->gtId == 0 || $this->gtId == $data['GTId']){
            $this->gtData = $data;
            $this->gtId = $data['GTId'];
            $this->gtName = $data['GTName'];
            $this->gtModelNum = $data['GTModelNum'];
            $this->plantId = $data['plantId'];
            $this->time = $data['timeupdate'];
        }
    }

    /**
     * Updates the database with the local data
     */
    public function save(){
        $data = $this->gtData;
        if($this->gtId){
            $where['GTId = ?'] = $this->gtId;
            $this->update($data,$where);
            //notifications and array difference
        } else{
            $this->insert($data);
            $this->gtId = $this->getAdapter()->lastInsertId();

        }
    }

    /**
     *  Deletes the GT
     */
    public function del()
    {
        $this->delete("GTId = " . $this->gtId);
    }

//    public function getGT($gtid) {
//        $gtid = (int) $gtid;
//        $row = $this->fetchRow('gtid = ' . $gtid);
//        if (!$row) {
//            throw new Exception("Could not find row $gtid");
//        }
//        return $row->toArray();
//    }
//
//    public function getGTP($pid){
//        $pid = (int) $pid;
//        $row = $this->fetchAll('plantId = ' . $pid);
//        if(!$row){
//            throw new Exception("Could not find row with plantId =  $pid");
//        }
//        return $row->toArray();
//    }
//
//    public function add($content) {
//
//		$role = Zend_Registry::get('role');
//		if($role == 'ca')
//		{
//			$umodel = new Model_DbTable_Userprofile();
//			$user = $umodel->getUser(Zend_Auth::getInstance()->getStorage()->read()->id);
//			$upid = $user['plantId'];
//			$content['plantId'] = $upid;
//		}
//
//
//		//Increase numOfGT by one before adding the gasturbine
//
//		$pmodel = new Model_DbTable_Plant();
//		$pid = intval($content['plantid']);
//		$plant = $pmodel->fetchRow("plantId = " . $pid);
//		$numOfGT = (int)$plant['numOfGT'];
//		$numOfGT++;
//		$where['plantId = ?'] = $pid;
//		$data['numOfGT'] = $numOfGT;
//		$pmodel->update($data,$where);
//
//        $this->insert($content);
//
//
//
//		$newid = $this->getAdapter()->lastInsertId();
//
//        $nf = new Model_DbTable_Notification();
//        $nf->add($this->getAdapter()->lastInsertId(), 'gasturbine', 1);
//		return $newid;
//    }
//
//    public function updateGT($content) {
//        $where = $this->getAdapter()->quoteInto('GTId = ?', $content['GTId']);
//        $this->update($content, $where);
//    }
//
//    public function listGT() {
//        $db = Zend_Db_Table::getDefaultAdapter();
//        $selectPlants = new Zend_Db_Select($db);
//        $selectPlants->from('gasturbines');
//
//        return $selectPlants;
//    }
//
//    public function getGTList()
//    {
//    	$row = $this->fetchAll();
//    	return $row->toArray();
//    }
//
//    public function listPlantGt($pid)
//    {
//    	$db = Zend_Db_Table::getDefaultAdapter();
//        $selectGt = new Zend_Db_Select($db);
//        $selectGt->from('gasturbines')
//        		 ->where('plantId = ?',$pid);
//
//        return $selectGt;
//    }
//
//    public function listPlantGtArray($pid) {
//    	$pid = (int) $pid;
//        $row = $this->fetchAll('plantId = ' . $pid);
//        if(!$row){
//            throw new Exception("Could not find row with plantId =  $pid");
//        }
//        return $row->toArray();
//
//   	}
	

}

?>