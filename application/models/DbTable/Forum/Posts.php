<?php
class Model_DbTable_Forum_Posts extends Zend_Db_Table_Abstract
{
    /**
     * Overrides the $_name of the parent class to indicate the name of the table it maps to
     *
     * @var String
     */
    protected $_name = 'forum_posts';
    
    /**
     * Unique ID of the post(primary key)
     * 
     * @var Integer
     */
     protected $postId;
     
     /**
      * Array containing all details about the post
      * 
      * @var Array
      */
      protected $postData;
      
      /**
       * Initializes values and fetches the respective Post details
       *
       * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
       * @param Integer - Primary Key of the table
       */
       function __construct($config = array(), $postId = 0)
       {
           parent::__construct($config);
            
           $this->postData = array();
            
           if($postId != 0) {
               $postData = $this->fetchRow("post_id = " . $postId);
               $this->postId = $postId;
               $this->postData = $postData->toArray();
           }
       }
       
       /**
        * Gets the array containing all details about the post
        * 
        * @return Array
        */
        public function getPostData(){
            return $this->postData;
        }
       
       /**
        * Lists all the posts in the forum
        * 
        * @return Array
        */
        public static function getList($options = array()) {
        	if(count($options)) {
        		if($options['post_id']){
        			$where = " WHERE post_id = " . $options['post_id'];
        		}
        	}
            $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
            $stmt = $dbAdapter->query("SELECT * FROM forum_posts" . $where);
            $list = $stmt->fetchAll();
            array($list);   
            return $list;
        }
}