<?php
	class Model_DbTable_SearchIndex extends Zend_Db_Table_Abstract
	{
		/**
	     * Overrides the $_name of the parent class to indicate the name of the table it maps to
	     *
	     * @var String
	     */
		protected $_name = 'search_post_index';
		
		/**
		 * Unique ID of the search post index
		 * 
		 * @var Integer
		 */
		protected $id;
		 
		/**
		 * Id of the post to be indexed
		 * 
		 * @var Integer
		 */		  
		protected $postId;
		  
		/**
		 * Type of operation to be performed: add - new index will be added , edit - existing index will be edited
		 * delete - existing index will be deleted
		 * 
		 * @var String
		 */
		protected $type;
		 
		 /**
		  * Array containing all the details
		  * 
		  * @var Array
		  */
		protected $data;
		 
		 /**
	      * Initializes values and fetches the respective Index details using ID as argument
	      * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
	      * @param Integer - Primary Key of the table
	      */
		function __construct($config = array(),$id=0){
			parent::__construct($config);
			
			$this->data = array();
			
			if($id){
				$dataRow = $this->fetchRow("id = " . $id);
				
				$this->data = $dataRow->toArray();
				$this->id = $this->data['id'];
				$this->postId = $this->data['post_id'];
				$this->type = $this->data['type'];
			}
		}
		
		/**
		 * Gets the ID of the index
		 * 
		 * @return Integer
		 */
		public function getId(){
			return $this->id;
		}
		
		/**
		 * Gets the Post ID of the index
		 * 
		 * @return Integer
		 */
		public function getPostId() {
			return $this->postId;
		}
		
		/**
		 * Gets the Type of the index
		 * 
		 * @return String
		 */
		public function getType(){
			return $this->type;
		}
		
		/**
		 * Gets the list of all tuples
		 * 
		 * @return Array
		 */
		public static function getList(){
	        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
	
	        $stmt = $dbAdapter->query("SELECT * FROM search_post_index");
	        $list = $stmt->fetchAll();
	        array($list);
	
	        return $list;
	    }    
		
		/**
		 * Gets the number of tuples
		 * 
		 * @return Integer
		 */
		public static function getCount() {
			$dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();

	        $stmt = $dbAdapter->query("SELECT COUNT(*) as cntRow FROM search_post_index");
	        $list = $stmt->fetchAll();
			
			return $list[0]["cntRow"];
		}
		  
		/**
		 * Deletes the index data
		 */
		public function deleteIndexData() {
			$this->delete("id = " . $this->id);
		}
	}