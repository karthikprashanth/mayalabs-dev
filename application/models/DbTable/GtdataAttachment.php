<?php

class  Model_DbTable_GtdataAttachment extends Zend_Db_Table_Abstract  {

    /**
    * Overrides the $_name of the parent class to indicate the name of the table it maps to
    *
    * @var String
    */
    protected $_name = 'gtdata_attachment';

    /**
     * Unique Id for the Gtdata Attachment
     *
     * @var Integer
     */
    protected $id;

    /**
     * Id of the Attachment in the attachment table
     *
     * @var Integer
     */
    protected $attachmentId;

	/**
	 * Id of the gtdata to which the attachment belongs
	 * 
	 * @var Integer
	 */
	protected $gtdataId;

    /**
     * Entire data about the Gtdata attachment
     *
     * @var Array
     */
    protected $data;

    /**
	 * Initializes values and fetches the respective Attachment details using the Id as argument
	 * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
	 * @param Integer - Primary Key of the table
	 */
    public function __construct($config = array(),$id=0){
        parent::__construct($config);

        $data = array();

        if($id){
            $data = $this->fetchRow("id = ".$id);
            $this->data = $data->toArray();
            $this->id = $this->data['id'];
            $this->gtdataId = $this->data['gtdataId'];
            $this->attachmentid = $this->data['attachmentId'];
        }
    }

    /**
     * Gets the Unique Id of the attachment
     *
     * @return Integer
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Gets the Id of the Gtdata to which the attachment belongs to
     *
     * @return Integer
     */
    public function getGtdataId(){
        return $this->gtdataId;
    }

    /**
     * Sets the Id of the Gtdata to which the attachment belongs
     *
     * @param Integer $gtdataId
     */
    public function setGtdataId($gtdataId){
        $this->gtdataId = $gtdataId;
        $this->data['gtdataId'] = $gtdataId;
    }

    /**
     * Gets the Id of the attachment in the Attachment table
     *
     * @return Integer
     */
    public function getAttachmentId(){
        return $this->attachmentid;
    }

    /**
     * Sets the Id of the attachment in the Attachment table
     *
     * @param Integer $attachmentId
     */
    public function setAttachmentId($attachmentId){
        $this->attachmentid = $attachmentId;
        $this->data['attachmentId'] = $attachmentId;
    }

    /**
     * Gets all data about the gtdata attachment
     * 
     * @return Array
     */
    public function getData(){
        return $this->data;
    }

    /**
     * Sets the local data array
     *
     * @param Array $data
     */
    public function setData($data){
        if($data['id'] == 0 || $this->id == $data['id']){
            $this->data = $data;

            $this->gtdataId = $data['gtdataId'];
            $this->attachmentId = $data['attachmentId'];
        }
    }

    /**
     * Gets the list of gtdata attachments
     *
     * @param Array $options - Extra option to filter the contents of the list
     * @return Array
     */
    public static function getList($options = array()){
        
        if(count($options)){
            if($options['orderby']){
                $order = "ORDER BY " . $options['orderby'];
            }

            if (count($options['columns'])){
                $where = " WHERE ";
                foreach($options['columns'] as $key => $value){
                    $where .= $key . " = '" . $value . "' AND ";
                }
                $where = substr($where,0,strlen($where)-4);
            }
			
            $query = "SELECT * FROM gtdata_attachment " . $where . " " . $order;
            
        }
        
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $stmt = $dbAdapter->query($query);
        $list = $stmt->fetchAll();
        array($list);
            
        return $list;
    }

    /**
     * Gets the number of gtdata attachments based on extra options
     *
     * @param Array $options - Extra options to filter the contents
     * @return Array
     */
    public static function getCount($options = array()){
        if (count($options['columns'])){
        	$where = " WHERE ";
			foreach($options['columns'] as $key => $value){
				$where .= $key . " = '" . $value . "' AND ";
			}
			$where = substr($where,0,strlen($where)-4);
        }

        $stmt = $dbAdapter->query("SELECT COUNT(*) as count FROM gtdata_attachment " . $where);
        $countRow = $stmt->fetchAll();
        return $countRow[0]["count"];
    }

    /**
     * Based on the value of id , either Inserts or Updates the content of the local data array
     */
    public function save(){
        if($this->id){
            $where = $this->getAdapter()->quoteInto('id = ?', $this->id);
            $this->update($this->data, $where);
        }
        else{
            $this->id = $this->insert($this->data);
            $this->data['id'] = $this->id;
        }
    }

    /**
     * Deletes the gtdata attachment from the database
     */
    public function deleteGTdataAttachment(){
    	$this->delete('id = '.$this->id);
    }

}
?>