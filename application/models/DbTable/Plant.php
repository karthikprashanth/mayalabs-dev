<?php

class Model_DbTable_Plant extends Zend_Db_Table_Abstract {

    /**
     * Overrides the $_name of the parent class to indicate the name of the table it maps to
     *
     * @var String
     */
    protected $_name = 'plants';
    /**
     * Primary Key of the table
     *
     * @var Integer
     */
    protected $plantId;
    /**
     * Name of the Plant
     *
     * @var String
     */
    protected $plantName;
    /**
     * Corporate Name of the Plant
     *
     * @var String
     */
    protected $corporateName;
    /**
     * Contains all the details about the Plant
     *
     * @var Array
     */
    protected $plantData;

    /**
     * Initializes values and fetches the respective Plant details using the plantId as argument
     * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
     * @param Integer - Primary Key of the table
     */
    function __construct($config = array(), $plantId = 0) {
        parent::__construct($config);

        $plantData = array();

        if ($plantId) {
            $plantRow = $this->fetchRow("plantId = " . $plantId);

            $this->plantData = $plantRow->toArray();
            $this->plantId = $plantRow['plantId'];
            $this->plantName = $plantRow['plantName'];
            $this->corporateName = $plantRow['corporateName'];
        }
    }

    /**
     * Gets the Plant ID
     *
     * @return Integer
     */
    public function getPlantId() {
        return $this->plantId;
    }

    /**
     * Gets the Plant Name
     *
     * @return String
     */
    public function getPlantName() {
        return $this->plantName;
    }

    /**
     * Sets the name of the plant
     *
     * @param String - Name of the plant to be set
     */
    public function setPlantName($plantName) {
        $this->plantData['plantName'] = $plantName;
        $this->plantName = $plantName;
    }

    /**
     * Gets the Corporate Name of the Plant
     *
     * @return String
     */
    public function getCorporateName() {
        return $this->corporateName;
    }

    /**
     * Sets the Corporate Name of the plant
     *
     * @param String - Corporate Name of the plant to be set
     */
    public function setCorporateName($corporateName) {
        $this->plantData['corporateName'] = $corporateName;
        $this->corporateName = $corporateName;
    }

    /**
     * Gets the full data about the Plant
     * @return Array
     */
    public function getPlantData() {
        return $this->plantData;
    }

    /**
     * Sets the plant data
     *
     * @param Array - Contains Plant's details
     */
    public function setplantData($plantData) {
        if ($this->plantId == 0 || $this->plantId == $plantData['plantId']) {
            $this->plantId = $plantData['plantId'];
            $this->plantName = $plantData['plantName'];
            $this->corporateName = $plantData['corporateName'];
            $this->plantData = $plantData;
        }
    }

    /**
     * Returns a list containing all the plants
     *
     * @param Array - Options to filter the results
     * @return Array - A list of all Plants
     */
    public static function getList($options = array()) {
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
        
        $stmt = $dbAdapter->query("SELECT * FROM plants " . $like . " " . $where . " " . $order);
        $list = $stmt->fetchAll();
        array($list);
        
        return $list;
    }

    /**
     * Returns the total number of Plants
     *
     * @return Integer - The number of plants
     */
    public static function getCount() {
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $dbAdapter->query("SELECT COUNT(*) AS count FROM plants");
        $countRow = $stmt->fetchAll();
        return $countRow[0]["count"];
    }

    /**
     * Updates details about the plant
     *
     * @param Integer - ID of the Plant which has to be updated
     * @param Array - Content to be updated
     */
    public function save() {
        $notificationModel = new Model_DbTable_Notification();
        $notificationData = array(
            'catid' => $this->getAdapter()->lastInsertId(),
            'category' => 'plant',
            'userupdate' => Zend_Auth::getInstance()->getStorage()->read()->id);

        if ($this->plantData['plantId']) {
            $notificationData['edit'] = 0;
            //$notificationModel->setNotificationData($notificationData);
            //$notificationModel->save(); check for array difference here
            $where = $this->getAdapter()->quoteInto('plantId = ?', $this->plantId);
            $this->update($this->plantData, $where);
        } else {
            $this->plantId = $this->insert($this->plantData);
            $notificationData['edit'] = 1;
            //$notificationModel->setNotificationData($notificationData);
            //$notificationModel->save();
        }
    }

    /**
     * Deletes the plant and also its corresponding notification
     */
    public function deletePlant() {
        $this->delete('plantId = ' . $this->plantId);

        $notificationModel = new Model_DbTable_Notification(Zend_Db_Table_Abstract::getDefaultAdapter(),
                        'plant', $this->plantId);
        $notificationModel->delete();
    }

    /*public function add($content) {
        
        $plantid = $this->insert($content);

        $nf = new Model_DbTable_Notification();
        $nf->add($this->getAdapter()->lastInsertId(), 'plant', 1);

        return $plantid;
    }*/
   
	/*public function getPlant($pid) 
        {
        try {
	        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
	        $stmt = $dbAdapter->query("SELECT * FROM plants where plantId = $pid");
	        $row = $stmt->fetchAll();
			array($row);
			return $row[0];
        }
        catch(Exception $e) {
            echo $e;
        }
    }
    
    public function getPlantList()
    {
    	$row = $this->fetchAll();
    	return $row->toArray();
    }*/

    /*public function listPlants() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $selectPlants = new Zend_Db_Select($db);
        $selectPlants->from('plants');
        return $selectPlants;
    }
    public function getPlantName($id) {
        $row = $this->fetchAll();
        $row->toArray();
        foreach ($row as $pDet) {
            if ($pDet['plantId'] == $id) {
                $pName = $pDet['plantName'];
            }
        }
        return $pName;
    }

    public function getPlantId($pName) {
        $row = $this->fetchAll();
        $row->toArray();
        foreach ($row as $pDet) {
            if ($pDet['plantName'] == $pName) {
                $pId = $pDet['plantId'];
            }
        }
        return $pId;
    }
    
    public function getSearchResults($term) {
    	try {
    	
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $dbAdapter->query("SELECT * FROM plants WHERE plantName like '%".$term."%' ORDER BY plantName");
        $row = $stmt->fetchAll();
        return $row;
        }
        catch(Exception $e) {
            echo $e;
        }
    } 
	
	public function getAllPlants()
	{
		try {
    	
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $dbAdapter->query("SELECT * FROM plants ORDER BY plantName");
        $row = $stmt->fetchAll();
        return $row;
        }
        catch(Exception $e) {
            echo $e;
        }
	}
	
	public function getCount()
	{
		$row = $this->fetchAll();
		return count($row);
	}*/
}