<?php
class Model_DbTable_Forum_Topics extends Zend_Db_Table_Abstract
{
    /**
     * Overrides the $_name of the parent class to indicate the name of the table it maps to
     *
     * @var String
     */
    protected $_name = 'forum_topics';
    
    /**
     * Unique ID of the topic(primary key)
     * 
     * @var Integer
     */
    protected $topicId;
     
     /**
      * Array containing all details about the topic
      * 
      * @var Array
      */
    protected $topicData;
      
      /**
       * Initializes values and fetches the respective topic details
       *
       * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
       * @param Integer - Primary Key of the table
       */
     function __construct($config = array(), $topicId = 0)
     {
         parent::__construct($config);
         
         $topicData = array();
            
         if($topicId != 0) {
             $topicData = $this->fetchRow("topic_id = " . $topicId);
             $this->topicId = $topicId;
             $this->topicData = $topicData->toArray();
          }
      }
       
      /**
       * Gets the array containing all details about the topic
       * 
       * @return Array
       */
      public function getTopicData(){
        return $this->topicData;
      }
}