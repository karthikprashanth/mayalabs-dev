<?php
class SearchController extends Zend_Controller_Action
{

    public function init()
    {
        
    }

    public function indexAction()
    {
		$searchForm = new Form_SearchForm();
		$searchForm->showfilters();
		$searchForm->removeElement('keyword');
		$this->view->advSearch = $searchForm;
		
		$this->view->syslist = Model_DbTable_Gtsystems::getList();
		$this->view->subsyslist = Model_DbTable_Gtsubsystems::getList();		
		
        if($this->_getParam('keyword',"") != "")
		{
			$query = strip_tags($this->_getParam('keyword',""));
			$query = stripslashes($query);
			
		}
		if($this->_getParam('plantname',"") != "")
		{
			$query = $query . " " . $this->_getParam('plantname',"");
		}
		if($this->_getParam('type',"") != "")
		{
			$query = $query . " " . $this->_getParam('type',"");
		}
		if($this->_getParam('sysname',"") != "")
		{
			$query = $query . " " . $this->_getParam('sysname',"");
		}
		if($this->_getParam('subsysname',"") != "")
		{
			$query = $query . " " . $this->_getParam('subsysname',"");
		}
		if($this->_getParam('toi',"") != "")
		{
			$query = $query . " " . $this->_getParam('toi',"");
		}
		$this->view->query = $query;
		$this->view->eoh = $this->_getParam('eoh',"");
		if(isset($_GET['keyword']))
		{
			$this->view->keyword = $_GET['keyword'];
		}
		$this->view->nature = $this->_getParam('nature');
    }
	
	public function searchmatrixAction()
	{
		$searchForm = new Form_SearchForm();
		$searchForm->showfilters();
		$searchForm->removeElement('keyword');
		$this->view->advSearch = $searchForm;
	}
	
	public function viewAction()
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$keyword = $this->_getParam('keyword');
		$displayMode = $this->_getParam('displaymode');
		$this->view->displayMode = $displayMode;
		
		if($this->getRequest()->isGet() || $keyword != "")
		{
			
			$cat = $this->_getParam('cat');
			$ll = $this->_getParam('ll');
			$ul = $this->_getParam('ul');
			$eoh = $this->_getParam('eoh');
			
			if  ($eoh != "")
			{
				$from =  substr($eoh,0,strlen($eoh)-(strpos($eoh,"-")+1));
				$to = substr($eoh,strpos($eoh,"-")+1);
				$from = (int)$from - ((int)$from % 5000);
				$to = (int)$to - ((int)$to % 5000);
				$this->view->eohfrom = $from;
				$this->view->eohto = $to;
			}
			$queryStr = $this->_getParam('keyword',0);
			
			$t1= time();
			$appath = substr(APPLICATION_PATH,0,strlen(APPLICATION_PATH)-12);
			$path = $appath . DIRECTORY_SEPARATOR . "search" . DIRECTORY_SEPARATOR . "gtdata";
			$index = Zend_Search_Lucene::open($path);
			$qarray = explode(" ",$queryStr);
			$valid = true;
			foreach($qarray as $q)
			{
				if(strlen($q) <= 2)
				{
					$valid = false;
				}
			}
			if($valid)
				$queryStr = $queryStr . "*";
			Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive());
			if($eoh != "")
  				$results = $index->find($queryStr . " OR eoh:[$from TO $to]");
			else 
				$results = $index->find($queryStr);
			
			
			if($cat == "gt")
			{
				$count = 0;
				$type['finding'] = "findings";
				$type['upgrade'] = "upgrades";
				$type['lte'] = "lte";
				$i = 1;
				
				foreach($results as $result)
				{
					if($i<$ll || $i > $ul)
					{
						$i++;
						continue;
					}
					$i++;
					
					$data[$count]['id'] = $result->dataid;
					
					$gtdataid = $data[$count]['id'];
					
					$gd = new Model_DbTable_Gtdata(Zend_Db_Table_Abstract::getDefaultAdapter(),$gtdataid);
					
					if(!count($gd))
					{
						$i--;
						continue;
					}
					
					$gtdata = $gd->getData();
					
					$user = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$gtdata['userupdate']);
					$userplantid = $user->getPlantId();
					$userFullName = $user->getFullName();
					$uplantname = $user->getPlantName();
					
					$gt = new Model_DbTable_Gasturbine(Zend_Db_Table_Abstract::getDefaultAdapter(),$gtdata['gtid']);
					
					$gtname = $gt->getGTName();
					
					$sys = new Model_DbTable_Gtsystems(Zend_Db_Table_Abstract::getDefaultAdapter(),$gtdata['sysId']);
					$sysname = $sys->getSysName();
					
					$subsys = new Model_DbTable_Gtsubsystems(Zend_Db_Table_Abstract::getDefaultAdapter(),$gtdata['subSysId']);
					$subsysname = $subsys->getsubSysName();
					
					$data[$count]['url'] = "/" . $type[$gtdata['type']] . "/view?id=" . $data[$count]['id'];				
					$data[$count]['gtid'] = $gtdata['gtid'];
					$data[$count]['gtname'] = $gtname;
					$data[$count]['updatedate'] = $gtdata['updatedate'];
					$data[$count]['data'] = strip_tags($gtdata['data']);
					$data[$count]['type'] = $gtdata['type'];
					$data[$count]['userupdate'] = $gtdata['userupdate'];
					$data[$count]['userfullname'] = $userFullName;
					$data[$count]['userplantid'] = $userplantid;
					$data[$count]['userplantname'] = $uplantname;
					$data[$count]['sysname'] = $sysname;
					$data[$count]['subsysname'] = $subsysname;
					$data[$count]['eoh'] = $gtdata['EOH'];
					$data[$count]['toi'] = $gtdata['TOI'];
					$data[$count]['dof'] = $gtdata['DOF'];
					$data[$count]['title'] = $gtdata['title'];
					$data[$count]['score'] = $result->score;	
					$count++;
				}
				
				$this->view->searchData = $data;
				$this->view->tgr = $i-1;
			}
			
			$count = 0;
			
			if($cat == "forum")
			{
				
				$appath = substr(APPLICATION_PATH,0,strlen(APPLICATION_PATH)-12);
				$path = $appath . DIRECTORY_SEPARATOR . "search" . DIRECTORY_SEPARATOR . "forum";
				$forumIndex = Zend_Search_Lucene::open($path);
				
				$results = $forumIndex->find($queryStr . " OR eoh:[$from TO $to]");
				
				$i=1;
				$tc=0;
				foreach($results as $result)
				{	
					if($i<$ll || $i > $ul)
					{
						continue;
					}
					$i++;
					$fdata[$count]['post_id'] = $result->post_id;
					$pid = $fdata[$count]['post_id'];
					
					$post = new Model_DbTable_Forum_Posts(Zend_Db_Table_Abstract::getDefaultAdapter(),$fdata[$count]['post_id']);
					$postData = $post->getPostData();
					if(!count($post))
					{
						$i--;
						continue;						
					}
					
					$fid = $postData['forum_id'];
					$tid = $postData['topic_id'];					
					$uid = $postData['poster_id'];
					
					$user = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$uid);
					$userFullName = $user->getFullName();
					$userplantid = $user->getPlantId();
					$uplantname = $user->getPlantName();
					
					$topic = new Model_DbTable_Forum_Topics(Zend_Db_Table_Abstract::getDefaultAdapter(),$tid);					
					$topicdata = $topic->getTopicData();
					$topicname = $topicdata['topic_title'];
					
					$forum = new Model_DbTable_Forum_Forums(Zend_Db_Table_Abstract::getDefaultAdapter(),$fid);
					$forumdata = $forum->getForumData();
					$forumname = $forumdata['forum_name'];
					
					$fdata[$count]['url'] = "/forums/viewtopic.php?f=" .$fid ."&t=".$tid."&p=".$pid."#p".$pid;
					$fdata[$count]['post_id'] = $pid;
					$fdata[$count]['topic_id'] = $tid;
					$fdata[$count]['forum_id'] = $fid;
					$fdata[$count]['poster_id'] = $uid;
					$fdata[$count]['post_subject'] = $postData['post_subject'];
					$fdata[$count]['post_text'] = strip_tags($postData['post_text']);
					$fdata[$count]['topicname'] = $topicname;
					$fdata[$count]['forumname'] = $forumname;
					$fdata[$count]['userfullname'] = $userFullName;
					$fdata[$count]['userplantid'] = $userplantid;
					$fdata[$count]['userplantname'] = $uplantname;
					$fdata[$count]['lucene_id'] = $result->id;
								
					$count++;
					
				}
				
				$this->view->forumData = $fdata;
				$this->view->fgr = $i-1;
				
			}
			
			if($ul > $i)
			{
				$ul = $i-1;
			}
			$this->view->ll = $ll;
			$this->view->ul = $ul;
			$this->view->queryStr = $queryStr;
			$t2=time();	                	 
		}		
   	}
	
	public function searchindexAction()
	{
		//indexing gtdata//
		
		$gtdataList = Model_DbTable_Gtdata::getList();
        
		$appath = substr(APPLICATION_PATH,0,strlen(APPLICATION_PATH)-12);
		$path = $appath . DIRECTORY_SEPARATOR . "search" . DIRECTORY_SEPARATOR . "gtdata";
		$index = Zend_Search_Lucene::create($path);
		echo "<h1>Indexed Gasturbine Data</h1>";
		foreach($gtdataList as $gtd)
		{    
		    $gtdata = new Model_DbTable_Gtdata(Zend_Db_Table_Abstract::getDefaultAdapter(),$gtd['id']);
            
            $gdArray = $gtdata->getData();
			echo $gtdata->getTitle() . " " . $gtdata->getType() . "<br>"; 
			
            $user = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$gtd['userupdate']);
            
			$uplant = $user->getPlantId();
			$uplantname = $user->getPlantName();
			
            $system = new Model_DbTable_Gtsystems(Zend_Db_Table_Abstract::getDefaultAdapter(),$gtd['sysId']);
            $sysname = $system->getSysName();
			
			$eoh = $gdArray['EOH'];
			$toi = $gdArray['TOI'];
			
            
			if($gdArray['subSysId'] != 34 && $gdArray['subSysId'] != 0)
			{
				$subsystem = new Model_DbTable_Gtsubsystems(Zend_Db_Table_Abstract::getDefaultAdapter(),$gtd['subSysId']);
                $subsysname = $subsystem->getsubSysName();
			}
			
			$doc = new Zend_Search_Lucene_Document();
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('dataid',$gdArray['id']));
			
			$doc->addField(Zend_Search_Lucene_Field::UnStored('title',$gdArray['title']));
			$doc->addField(Zend_Search_Lucene_Field::UnStored('data',$gdArray['data']));
			$doc->addField(Zend_Search_Lucene_Field::UnStored('type',$gdArray['type']));
			
			$doc->addField(Zend_Search_Lucene_Field::UnStored('sysname',$sysname));  
			$doc->addField(Zend_Search_Lucene_Field::UnStored('subsysname',$subsysname));
			$doc->addField(Zend_Search_Lucene_Field::Keyword('eoh',$eoh));
			$doc->addField(Zend_Search_Lucene_Field::UnStored('toi',$toi));
            
			$doc->addField(Zend_Search_Lucene_Field::UnStored('userplantname',$uplantname));
			
			$index->addDocument($doc);
		}
		$index->commit();  
		$index->optimize();
		
		//forum data indexing//
		
		$forumPosts = Model_DbTable_Forum_Posts::getList();
		
	    $appath = substr(APPLICATION_PATH,0,strlen(APPLICATION_PATH)-12);
		$path = $appath . DIRECTORY_SEPARATOR . "search" . DIRECTORY_SEPARATOR . "forum";
		
		$index = Zend_Search_Lucene::create($path);
		
		foreach($forumPosts as $post)
		{
			$doc = new Zend_Search_Lucene_Document();
			
			$forumTopic = new Model_DbTable_Forum_Topics(Zend_Db_Table_Abstract::getDefaultAdapter(),$post['topic_id']);
            $forumTopicData = $forumTopic->getTopicData();
			$topicname = $forumTopicData['topic_title'];
			
			
			$forum = new Model_DbTable_Forum_Forums(Zend_Db_Table_Abstract::getDefaultAdapter(),$post['forum_id']);
			$forumData = $forum->getForumData();
			$forumname = $forumData['forum_name'];
			
			$poster = new Model_DbTable_Userprofile(Zend_Db_Table_Abstract::getDefaultAdapter(),$post['poster_id']);
			$uplantname = $poster->getPlantName();
			
			$doc->addField(Zend_Search_Lucene_Field::Keyword('post_id',$post['post_id']));
			
			$doc->addField(Zend_Search_Lucene_Field::UnStored('post_subject',$post['post_subject']));
			$doc->addField(Zend_Search_Lucene_Field::UnStored('post_text',$post['post_text']));
			$doc->addField(Zend_Search_Lucene_Field::UnStored('topicname',$topicname));
			$doc->addField(Zend_Search_Lucene_Field::UnStored('forumname',$forumname));
			
			$doc->addField(Zend_Search_Lucene_Field::UnStored('userplantname',$uplantname));
			$index->addDocument($doc);
		}
		$index->commit();  
		$index->optimize();
        
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $baseUrl = new Zend_View_Helper_BaseUrl();
        $this->_redirect("/dashboard/index");
	}

	public function searchlayoutAction()
	{		
        if (!$this->_request->isXmlHttpRequest())
            $this->_helper->viewRenderer->setResponseSegment('searchlayout');
	}	
}