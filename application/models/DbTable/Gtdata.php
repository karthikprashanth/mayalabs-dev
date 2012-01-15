
<?php

class Model_DbTable_Gtdata extends Zend_Db_Table_Abstract {

    /**
	 * Overrides the $_name of the parent class to indicate the name of the table it maps to
	 *
	 * @var String 
	 */
    protected $_name = 'gtdata';

    /**
     * Primary Key of the table
     *
     * @var Integer
     */
    protected $id;

    /**
     * GT to which it belongs
     *
     * @var Integer
     */
    protected $gtid;

    /**
     * Title of the GT Data
     *
     * @var String
     */
    protected $title;

    /**
     * Details in the GT Data
     *
     * @var Array
     */
    protected $gtData;    

    /**
     * Whether mail has been sent
     *
     * @var Boolean
     */
    protected $mailed;

    /**
	 * Initializes values and fetches the respective GT Data details using the id as argument
	 * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
	 * @param Integer - Primary Key of the table
	 */
    function  __construct($config = array(), $id = 0) {

        parent::__construct($config);
		
        $gtData = array();
        if($id){
            $gtDataRow = $this->fetchRow("id = " . $id);

            $this->gtData = $gtDataRow->toArray();
            $this->id = $gtDataRow['id'];
            $this->gtid = $gtDataRow['gtid'];
            $this->title = $gtDataRow['title'];
            $this->time = $gtDataRow['updatetime'];
            $this->user = $gtDataRow['userupdate'];
            $this->mailed = $gtDataRow['mailed'];
        }
    }

    /**
     * Gets the GT Data Id
     *
     * @return Integer
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Gets the GT Data title
     *
     * @return String
     */
    public function getTitle(){
        return $this->title;
    }

    /**
     * Gets the GT Data Details
     *
     * @return Array
     */
    public function getData(){
        return $this->gtData;
    }    

    /**
     * Gets the Mailed staus
     *
     * @return Boolean
     */
    public function getMailedStatus(){
        return $this->mailed;
    }

    /**
     * Sets the GT Id to which the GT data belongs
     *
     * @param Integer - Id of the GT
     */
    public function setGTId($gtId){
        $this->gtid = $gtId;
    }

    /**
     * Sets the GT Data Title
     *
     * @param String - Title of the GT Data
     */
    public function setTitle($title){
        $this->title = $title;
    }

    /**
     * Sets the Mailed Status
     *
     * @param Boolean
     */
    public function setMailed($mailed){
        $this->mailed = $mailed;
    }

    /**
     * Sets all the GT Data
     *
     * @param Array - All the GT Data
     */
    public function setGTData($gtData){
        if($this->id == 0 || $gtData['id'] == $this->id){
            $this->id = $gtData['id'];
            $this->gtid = $gtData['gtid'];
            $this->title = $gtData['title'];
            $this->mailed = $gtData['mailed'];

            $this->gtData = $gtData;
        }
    }

    /**
     * Checks whether the User belongs to the Gasturbine
     * which contains this GT Data
     *
     * @return Boolean
     */
    public function isBelong(){
        $id = $this->id;

    	$gtmodel = new Model_DbTable_Gasturbine();
    	$gt = $gtmodel->getGT($gtid);

    	$uid = Zend_Auth::getInstance()->getStorage()->read()->id;
    	$umodel = new Model_DbTable_Userprofile();
    	$user = $umodel->getUser($uid);

    	if((int)$user['plantId'] == (int)$gt['plantId']) {
    		return true;
    	} else {
    		return false;
    	}
    }

    /**
     * Updates the Table based on the local values stored
     */
    public function save(){
        if($this->id){
            $data = $this->gtData;
            $where['id = ?'] = $this->id;
            $this->update($data,$where);
        } else{
            $data = $this->gtData;
            $this->insert($data);
        }
    }

    /**
     * Deletes the GT Data
     */
    public function delete(){
    	$this->delete('id = ' . $this->id);
    }

    /**
     * Gets all the Un-Mailed Rows
     *
     * @return Array
     */
    public static function getUnmailedData(){
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $row = $dbAdapter->query("SELECT * FROM gtdata WHERE `mailed` = '0';")->fetchAll();
     	return $row->toArray();
    }

    /**
     * Sets the Mailed status of all GT Data to Mailed
     */
    public static function setMailedStatus(){
     	Zend_Db_Table_Abstract::getDefaultAdapter()->query("UPDATE gtdata SET `mailed` = '1';");
    }

    /**
     * Returns a list containing all the GT Data
     *
     * @param Array - Options to filter the results
     * @return Array - A list of all GT Data
     */
    public static function getList($options = array()){
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();

        if (count($options)) {
            if ($options['orderby'] != "") {
                $order = "ORDER BY " . $options['orderby'];
            }

            if ($options['likeColumn'] != "") {
                $like = "WHERE " . $options['likeColumn'] . " LIKE '%" . $options['likeTerm'] . "%'";
            }
        }

        if (count($options['columns'])){
        	$where = " WHERE ";
			foreach($options['columns'] as $key => $value){
				$where .= $key . " = '" . $value . "' AND ";
			}
			$where = substr($where,0,strlen($where)-4);
        }

        $stmt = $dbAdapter->query("SELECT * FROM gtdata " . $like . " " . $where . " " . $order);
        $list = $stmt->fetchAll();
        array($list);

        return $list;
    }    

    /**
     * Returns the Count of GT Data in the specified type and GT
     *
     * @param String - Type of the GT Data
     * @return Integer
     */
    public static function getCount($type){

        $where = "WHERE `type` = '$type';";

        $countRow = Zend_Db_Table_Abstract::getDefaultAdapter()->query("SELECT COUNT(*) AS count FROM gtdata " . $where)->fetchAll();
		return $countRow[0]["count"];
    }    

//    public function getData($id) {
//        $id = (int) $id;
//        $row = $this->fetchRow('id = ' . $id);
//        if (!$row) {
//            $row = array();
//			return $row;
//        }
//        return $row->toArray();
//    }
//
//    public function isGTBelong($id) {
//    	$id = (int)$id;
//
//    	$gtdata = $this->fetchRow('id = ' . $id);
//    	$gtdata = $gtdata->toArray();
//    	$gtid = $gtdata['gtid'];
//
//    	$gtmodel = new Model_DbTable_Gasturbine();
//    	$gt = $gtmodel->getGT($gtid);
//
//
//    	$uid = Zend_Auth::getInstance()->getStorage()->read()->id;
//    	$umodel = new Model_DbTable_Userprofile();
//    	$user = $umodel->getUser($uid);
//
//    	if((int)$user['plantId'] == (int)$gt['plantId']) {
//    		return true;
//    	}
//    	else {
//    		return false;
//    	}
//     }
//
//     public function getDataByType($gtid,$type)
//     {
//     	$row = $this->fetchAll("gtid = " . $gtid . " AND type = '" . $type . "'");
//     	return $row->toArray();
//     }
//
//     public function getDataByGt($gtid)
//     {
//     	$row = $this->fetchAll("gtid = " . $gtid);
//     	return $row->toArray();
//     }
//
//     public function getUnmailedData()
//     {
//     	$row = $this->fetchAll("mailed = 0");
//     	return $row->toArray();
//     }
//
//     public function setMailed()
//     {
//     	$data = array('mailed' => 1);
//     	$where['mailed = ?'] = 0;
//     	$this->update($data,$where);
//     }
//
//	 public function getTypeCount($type,$id)
//	 {
//	 	$row = $this->fetchAll("gtid = " . $id . " AND type = '" . $type . "'");
//		return count($row->toArray());
//	 }
}

?>