<?php

class Model_DbTable_Presentation extends Zend_Db_Table_Abstract {

    /*
	 * Overrides the $_name of the parent class to indicate the name of the table it maps to
	 *
	 * @var String
	 */
	protected $_name = 'presentations';

    /*
     * Primary Key of the table
     *
     * @var Integer
     */
    protected $id;

    /**
     * Title of the presentation
     *
     * @var String
     */
    protected $title;

    /**
     * Id of the GT to which the presentation belongs
     *
     * @var Integer
     */
    protected $gtid;
    
    /**
     * Contains all the data of the Presentation
     * 
     * @var Array     
     */
    protected $prData;

    /*
	 * Initializes values and fetches the respective Presentation details using the id as argument
	 * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
	 * @param Integer - Primary Key of the table
	 */
    public function  __construct($config = array(), $id=0) {
        parent::__construct($config);

        
        $prData = array();
        if($id){
            $prDataRow = $this->fetchRow("id = " . $id);

            $this->prData = $prDataRow->toArray();
            $this->id = $prDataRow['presentaionId'];
            $this->gtid = $prDataRow['GTId'];
            $this->title = $prDataRow['title'];
        }
    }

    /**
     * Gets the Presentation Id
     *
     * @return Integer
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Gets the Presentation Title
     *
     * @return String
     */
    public function getTitle(){
        return $this->title;
    }

    /**
     * Gets the GT Id
     *
     * @return Integer
     */
    public function getGTId(){
        return $this->gtid;
    }

    /**
     * Gets all the Presentation Data
     *
     * @return Array
     */
    public function getData(){
        return $this->prData;
    }

    /**
     * Sets the Presentation Id
     *
     * @param Integer
     */
    public function setId($id){
        $this->id = $id;
        $this->prData['presentaionId'] = $id;
    }

    /**
     * Sets the Presentation Title
     *
     * @param String
     */
    public function setTitle($title){
        $this->title = $title;
        $this->prData['title'] = $title;
    }

    /**
     * Sets the GT Id
     *
     * @param Integer
     */
    public function setGTId($gtid){
        $this->gtid = $gtid;
        $this->prData['GTId'] = $gtid;
    }

    /**
     * Sets all the Presentation Data
     *
     * @param Array
     */
    public function setData($prData){
        if($this->id == 0 || $prData['presentationId'] == $this->id){

            $this->id = $prData['presentaionId'];
            $this->gtid = $prData['GTId'];
            $this->title = $prData['title'];

            $this->prData = $prData;
        }
    }
    
    /**
     * Updates the Table based on the local values stored
     */
    public function save(){
        if($this->id){
            $data = $this->prData;
            $where['presentaionId = ?'] = $this->id;
            $this->update($data,$where);
        } else{
            $data = $this->prData;
            $this->insert($data);
        }
    }

    /**
     * Deletes the presentation
     */
    public function delete(){
    	$this->delete('id = ' . $this->id);
    }

    /**
     * Lists all the presentaions of the specified GT
     *
     * @param Integer - GT Id
     */
    public static function getList($gtid){
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = $dbAdapter->query("SELECT * FROM presentations WHERE `GTId` = '$gtid' ORDER BY timeupdate DESC");
		$rSet = $select->fetchAll($select);
		return array($rSet);
    }



//	public function getPresentation($id)
//	{
//		$pid = (int)$id;
//		$row = $this->fetchRow('presentationId = ' . $pid);
//		if (!$row) {
//			throw new Exception("Could not find row $pid");
//		}
//		return $row->toArray();
//	}
//
//	public function add($content) {
//                $currentdate= date('Y-m-d H:i:s');
//                $data = array_merge($content,array('userupdate'=>Zend_Auth::getInstance()->getStorage()->read()->id,'timeupdate'=>$currentdate));
//            	$this->insert($data);
//	}
//
//	public function deletePresentation($id)
//        {
//            $this->delete('id =' .(int)$id);
//        }
//
//        public function listPresentation($id){
//			$select = $this->select()
//					->where("GTId = " . $id)
//				   ->order('timeupdate DESC');
//		$rSet = $this->fetchAll($select);
//		return $rSet->toArray();
//        }    
}

?>