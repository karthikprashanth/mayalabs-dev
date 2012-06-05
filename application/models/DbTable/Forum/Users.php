<?php
class Model_DbTable_Forum_Users extends Zend_Db_Table_Abstract
{
	/**
     * Overrides the $_name of the parent class to indicate the name of the table it maps to
     *
     * @var String
     */
    protected $_name = 'forum_users';
    
    /**
     * Unique ID of the user(primary key)
     * 
     * @var Integer
     */
     protected $userId;
     
     /**
      * Array containing all details about the forum user
      * 
      * @var Array
      */
      protected $forumData;
      
      /**
       * Initializes values and fetches the respective User details using the userId as argument
       *
       * @param Zend_Db_Table_Adapter_Abstract - Default parameter as defined in the parent function
       * @param Integer - Primary Key of the table
       */
       function __construct($config = array(), $userId = 0)
       {
           parent::__construct($config);
            
           $this->forumData = array();
            
           if($userId != 0) {
               $forumData = $this->fetchRow("user_id = " . $userId);
               $this->userId = $userId;
               $this->forumData = $forumData->toArray();
           }
       }
       
       /**
        * Sets the password of the forum user
        * 
        * @param String
        */
        public function setPassword($password){
            $this->forumData['user_password'] = md5($password);
        }
        
        /**
         * Saves the local values to the database
         */
         public function save() {            
            $where = $this->getAdapter()->quoteInto('user_id = ?', $this->userId);
            $this->update($this->forumData, $where);
         }
}