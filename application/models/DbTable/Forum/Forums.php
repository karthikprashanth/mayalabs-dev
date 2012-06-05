<?php
class Model_DbTable_Forum_Forums extends Zend_Db_Table_Abstract
{
    /**
     * Overrides the $_name of the parent class to indicate the name of the table it maps to
     *
     * @var String
     */
    protected $_name = 'forum_forums';
    
    /**
     * Unique ID of the forum(primary key)
     * 
     * @var Integer
     */
    protected $forumId;
     
     /**
      * Array containing all details about the forum
      * 
      * @var Array
      */
    protected $forumData;
      
      /**
       * Initializes values and fetches the respective forum details
       *
       * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
       * @param Integer - Primary Key of the table
       */
     function __construct($config = array(), $forumId = 0)
     {
         parent::__construct($config);
            
         $this->forumData = array();
            
         if($forumId != 0) {
             $forumData = $this->fetchRow("forum_id = " . $forumId);
             $this->forumId = $forumId;
             $this->forumData = $forumData->toArray();
          }
      }
       
      /**
       * Gets the array containing all details about the post
       * 
       * @return Array
       */
      public function getForumData(){
        return $this->forumData;
      }
 
}