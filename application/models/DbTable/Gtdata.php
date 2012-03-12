
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
	 * Type of the Gtdata - Finding/Upgrade/Life Time Extension (LTE)
	 * 
	 * @var String 
	 */
	protected $type;
	
    /**
     * Title of the GT Data
     *
     * @var String
     */
    protected $title;
	
	/**
	 * Date and Time when the gtdata was added/last updated
	 * 
	 * @var Timestamp
	 */
	protected $updatetime;  
	 

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
            $this->updatetime = $gtDataRow['updatedate'];
            $this->user = $gtDataRow['userupdate'];
			$this->type = $gtDataRow['type'];
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
	 * Gets the GT Id of the gtdata
	 * 
	 * @return Integer
	 */
	public function getGTId(){
		return $this->gtid;
	}
	
	/**
	 * Gets the type of the gtdata
	 * 
	 * @return String
	 */
	public function getType(){
		return $this->type;
	}
	
	/**
	 * Gets the type title - Finding (finding) , Upgrade (upgrade) or LTE (lte)
	 */
	public function getTypeTitle(){
		if($this->type == "finding" || $this->type == "upgrade"){
			return ucfirst($this->type);
		}
		else {
			return strtoupper($this->type);
		}
	}
	
	/**
	 * Gets the last update time of the gtdata
	 * 
	 * @return Timestamp
	 */
	public function getUpdateTime()
	{
		return $this->updatetime;
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
	 * Gets the id of the user who added the gtdata or updated it lastly
	 * 
	 * @return Integer
	 */
	public function getUserUpdate(){
		return $this->gtData['userupdate'];
	}
	
	/**
	 * Gets the name of the system to which the finding/upgrade/lte belongs
	 * 
	 * @return String
	 */
	public function getSystemName(){
		$sys = new Model_DbTable_Gtsystems(Zend_Db_Table_Abstract::getDefaultAdapter(),$this->gtData['sysId']);
		return $sys->getSysName();
	}
	
	/**
	 * Gets the name of the sub-system to which the finding/upgrade/lte belongs
	 * 
	 * @return String
	 */
	public function getSubSystemName(){		
		$subsys = new Model_DbTable_Gtsubsystems(Zend_Db_Table_Abstract::getDefaultAdapter(),$this->gtData['subSysId']);
		
		return $subsys->getsubSysName();
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
     * Updates the Table based on the local values stored
     */
    public function save(){
        if($this->id){
            $data = $this->gtData;
            $where['id = ?'] = $this->id;
            $this->update($data,$where);
        } else{
            $data = $this->gtData;
            $this->id = $this->insert($data);
			
        }
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
    public static function getCount($options = array()){

        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();

        if (count($options['columns'])){
        	$where = " WHERE ";
			foreach($options['columns'] as $key => $value){
				$where .= $key . " = '" . $value . "' AND ";
			}
			$where = substr($where,0,strlen($where)-4);
        }
        $stmt = $dbAdapter->query("SELECT COUNT(*) as cntRow FROM gtdata " . $like . " " . $where . " " . $order);
        $list = $stmt->fetchAll();
		
		return $list[0]["cntRow"];
    }
	
	/**
     * Deletes the GT Data
     */
    public function deleteGtdata(){
    	$this->delete('id = ' . $this->id);
    }    

}

?>