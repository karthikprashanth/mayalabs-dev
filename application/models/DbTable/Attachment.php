<?php

class Model_DbTable_Attachment extends Zend_Db_Table_Abstract {

    /**
     * Overrides the $_name of the parent class to indicate the name of the table it maps to
     *
     * @var String
     */
    protected $_name = 'attachments';

    /**
     * Unique ID of the Attachment (Primary Key)
     *
     * @var Integer
     */
    protected $id;

    /**
     * Title of the Attachment
     *
     * @var String
     */
    protected $title;

    /**
     * Binary Content
     *
     * @var Long Blob
     */
    protected $content;

    /**
     * All the data of the Attachment
     *
     * @var Array
     */
    protected $data;

    /**
	 * Initializes values and fetches the respective Attachment details using the Id as argument
	 * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
	 * @param Integer - Primary Key of the table
	 */
    public function __construct($config = array(), $id = 0 ) {
        parent::__construct($config);
        $data = array();
        if($id){
            $data = $this->fetchRow("id = " . $id);
			
            $this->data = $data->toArray();
            $this->id = $data['id'];            
            $this->title = $data['title'];
            $this->content = $data['content'];
        }
    }

    /**
     * Gets the Id of the Attachment
     *
     * @return Integer
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Gets the Title of the Attachment
     *
     * @return String
     */
    public function getTitle(){
        return $this->title;
    }

    /**
     * Sets the Title of the Attachment
     *
     * @param String
     */
    public function setTitle($title){
        $this->title = $title;
        $this->data['title'] = $title;
    }

    /**
     * Gets the Description
     *
     * @return String
     */
    public function getDescription(){
        return $this->description;
    }

    /**
     * Sets the Description
     *
     * @param String
     */
    public function setDescription($description){
        $this->description = $description;
        $this->data['description'] = $description;
    }

    /**
     * Gets the Content
     *
     * @return String
     */
    public function getContent(){
        return $this->content;
    }

    /**
     * Sets the Content
     *
     * @param String
     */
    public function setContent($content){
        $this->content = $content;
        $this->data['content'] = $content;
    }

    /**
     * Gets all the data of the Attachment
     *
     * @return Array
     */
    public function getData(){
        return $this->data;
    }

    /**
     * Sets all the data of the Attachment
     *
     * @param Array
     */
    public function setData($data){        

        if($this->id == 0 || $data['id'] == $this->id){
            $this->data = $data;

            $this->title = $data['title'];
            $this->description = $data['description'];
            $this->content = $data['content'];
            $this->filetype = $data['filetype'];
        }
    }
	
	/**
	 * Gets the type of the attachment file
	 * 
	 * @return String
	 */
	public function getFileExtension(){
		return $this->data['filetype'];
	}

    /**
     * Gets the type of the file
     *
     * @return String
     */
    public function getFiletype(){
        
        $prestype['pdf']  =  "PDF File";
		$prestype['jpeg'] =  "Image";
		$prestype['jpg']  =  "Image";
		$prestype['png']  =  "Image";
		$prestype['gif']  =  "Image";
		$prestype['doc']  =  "Word Document";
		$prestype['docx'] =  "Word Document";
		$prestype['xls']  =  "Excel Sheet";
		$prestype['xlsx'] =  "Excel Sheet";
		$prestype['ppt']  =  "Powerpoint Presentation";
		$prestype['pptx'] =  "Powerpoint Presentation";
        
        return $prestype[$this->data['filetype']];
        
    }

    /**
     * Gets the name of the user who added/updated this attachment
     *
     * @return String
     */
    public function getUsername(){
        $uid = $this->data['updatedBy'];
        
        return Model_DbTable_Userprofile::getUserName($uid);
    }
	
	
	
	/**
	 * Gets the name of the plant to which the user belongs
	 * 
	 * @return String
	 */
	public function getUserPlantName(){
		$up = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$this->data['updatedBy']);
		return $up->getPlantName();
	}
    
    /**
     * Updates the database with the local values
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
     * Deletes the presentation
     */
    public function deleteAttachment(){
    	$this->delete('id = ' . $this->id);
    }
}