<?php
class Model_DbTable_Bookmark extends Zend_Db_Table_Abstract {

	protected $_name = 'bookmarks';

	public function getBookmark($bmid)
	{
		$bmid = (int)$bmid;
		$row = $this->fetchRow('bmId = ' . $bmid);
		if (!$row) {
			throw new Exception("Could not find row $bmid");
		}
		return $row->toArray();
	}

        public function checkBookmark($catid,$userid,$controller)
        {
            $catid=(int)$catid;
            $userid=(int)$userid;
            
            $row = $this->fetchRow($this->select()->where('userId =?',$userid)
                                                  ->where('category =?',$controller)
                                                  ->where('catId =?',$catid ));

            return $row;
        }
        
	public function add($content) {
            if($content['category'])
                $this->insert($content);
	}

        public function deleteBookmark($id)
        {
            $this->delete('bmId =' .(int)$id);
        }

	public function listBookmark(){
            $db=Zend_Db_Table::getDefaultAdapter();
            $selectAdverts= new Zend_Db_Select($db);
            $selectAdverts->from('bookmarks')
                          ->columns(array('bmId','category','catId'))
                          ->order('updatedtime')
                          ->limit(5);
            
            $uid = Zend_Auth::getInstance()->getStorage()->read()->id;
            $this->fetchAll(array('userId'=>$uid), 'updatedtime', 5);
            return $selectAdverts;
        }

        public function longlistBookmark() {
            $db=Zend_Db_Table::getDefaultAdapter();
            $uid = Zend_Auth::getInstance()->getStorage()->read()->id;

            $selectAdverts= new Zend_Db_Select($db);
            $selectAdverts->from('bookmarks')
                          ->where('userId=?',$uid)
                          ->columns(array('bmId','category','catId'))
                          ->order('updatedtime desc');

            return $selectAdverts;
        }
        
        public function timeStamp()
        {
            $time= date('Y-m-d H:i:s');
            return $time;
        }
}

?>
