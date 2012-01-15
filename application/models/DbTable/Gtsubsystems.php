<?php

class Model_DbTable_Gtsubsystems extends Zend_Db_Table_Abstract {

    /**
	 * Overrides the $_name of the parent class
     * to indicate the name of the table it maps to
	 *
	 * @var String
	 */
    protected $_name = 'gtsubsystems';

    /**
     * Primary key of the Table
     *
     * @var Integer
     */
    protected $id;


    /**
     * System Id to which the Sub System Belongs
     *
     * @var Integer
     */
    protected $sysId;


    /**
     * Sub System Name
     *
     * @var String
     */
    protected $subSysName;

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
    public function  __construct($config = array(), $id = 0) {
        parent::__construct($config);

        if($sysId){
            $data = $this->fetchRow("subSysId = " . $subSysId);
            $this->data = $data;
            $this->id = $id;
            $this->sysId = $data['sysId'];
            $this->subSysName = $data['subSysName'];
        }
    }

    /**
     * Returns the Id
     *
     * @return Integer
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Returns the System Id
     *
     * @return Integer
     */
    public function getsysId(){
        return $this->sysId;
    }

    /**
     * Sets the System Id
     *
     * @param Integer - System Id
     */
    public function setsysId($sysId){
        $this->sysId = $sysId;
    }

    /**
     * Returns the Sub System Name
     *
     * @return String
     */
    public function getsubSysName(){
        return $this->subSysName;
    }

    /**
     * Sets the Sub System Name
     *
     * @param String - System Name
     */
    public function setsubSysName($subSysName){
        $this->subSysName = $subSysName;
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
        if($data['id'] == $this->id || !$this->id){
            $this->data = $data;

            $this->subSysName = $data['subSysName'];
            $this->sysId = $data['sysId'];
        }
    }

    /*
     * Updates the database with the local data
     */
    public function save(){
        if($this->id){
            $data = $this->data;
            $where['id = ?'] = $this->id;
            $this->update($data,$where);
        } else{
            $data = $this->data;
            $this->id = $this->insert($data);
        }
    }

    /**
     * Deletes the Current Sub System
     */
    public function delete(){
        $this->delete('id =' . (int) $this->id);
    }

    /**
     * Gets all Sub Systems
     *
     * @param Integer - System Id
     * @return Array
     */
    public static function getList($sysId = 0){
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        if($sysId){            
            $rows = $dbAdapter->query("SELECT * FROM gtsubsystems WHERE `sysId` = '$sysId';")->fetchAll();
        } else{
            $rows = $dbAdapter->query("SELECT * FROM gtsubsystems;")->fetchAll();
        }
        return $rows;
    }

    /**
     * Returns the Count of the GT Sub Systems
     *
     * @return Integer
     */
    public static function getCount(){
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $dbAdapter->query("SELECT COUNT(*) AS count FROM gtsubsystems");
        $countRow = $stmt->fetchAll();
        return $countRow[0]["count"];
    }

//    public function getSubSystem($ssid) {
//        $ssid = (int) $ssid;
//        $row = $this->fetchRow('id = ' . $ssid);
//        return $row->toArray();
//    }
//
//    public function groupSubSystem($sid) {
//        $row = $this->fetchAll("sysId = " . $sid);
//        return $row->toArray();
//    }

}