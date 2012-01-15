<?php

class Model_DbTable_Gtsystems extends Zend_Db_Table_Abstract {

    /**
	 * Overrides the $_name of the parent class
     * to indicate the name of the table it maps to
	 *
	 * @var String
	 */
    protected $_name = 'gtsystems';

    /**
     * Primary key of the Table
     * 
     * @var Integer
     */
    protected $sysId;
    
    /**
     * System Name
     * 
     * @var String
     */
    protected $sysName;

    /**
     * All the data in a row
     *
     * @var Array
     */
    protected $data;

    /**
     * Initializes values and fetches the respective GT Data details using the id as argument
     * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
	 * @param Integer - Primary Key of the table
     */
    public function  __construct($config = array(), $sysId = 0) {
        parent::__construct($config);

        if($sysId){
            $data = $this->fetchRow("sysId = " . $sysId);
            $this->data = $data;
            $this->sysId = $sysId;
            $this->sysName = $data['sysName'];
        }
    }

    /**
     * Returns the Id
     *
     * @return Integer
     */
    public function getId(){
        return $this->sysId;
    }

    /**
     * Returns the System Name
     *
     * @return String
     */
    public function getSysName(){
        return $this->sysName;
    }

    /**
     * Sets the System Name
     *
     * @param String - System Name
     */
    public function setSysName($sysName){
        $this->data['sysName'] = $this->sysName = $sysName;
    }

    /**
     * Returns all the Data in a row
     *
     * @return Array
     */
    public function getData(){
        return $this->data;
    }

    /**
     * Sets all the data in a row
     *
     * @param Array
     */
    public function setData($data){
        if($data['sysId'] == $this->sysId || !$this->sysId){
            $this->data = $data;
            
            $this->sysName = $this->data['sysName'];
        }
    }

    /*
     * Updates the database with the local data
     */
    public function save(){
        if($this->sysId){
            $data = $this->data;
            $where['sysId = ?'] = $this->sysId;
            $this->update($data,$where);
        } else{
            $data = $this->data;
            $this->insert($data);
        }
    }

    /**
     * Deletes the Current System
     */
    public function delete(){
        $this->delete('sysId =' . (int) $this->sysId);
    }

    /**
     * Gets all Systems
     *
     * @return Array
     */
    public static function getList(){
        $rows = Zend_Db_Table_Abstract::getDefaultAdapter()->query("SELECT * FROM gtsystems")->fetchAll();
        return $rows;
    }

    /**
     * Returns the Count of the GT Systems
     *
     * @return Integer
     */
    public static function getCount(){
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $dbAdapter->query("SELECT COUNT(*) AS count FROM gtsystems");
        $countRow = $stmt->fetchAll();
        return $countRow[0]["count"];
    }

//    public function getSystem($sid)
//    {
//        $sid = (int)$sid;
//        $row = $this->fetchRow('sysId = ' . $sid);
//        return $row->toArray();
//    }
}