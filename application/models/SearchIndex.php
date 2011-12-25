<?php
	class Model_SearchIndex
	{
		public function updateIndex($type,$id,$category = "")
		{
			
			$appath = substr(APPLICATION_PATH,0,strlen(APPLICATION_PATH)-12);
			$path = $appath . DIRECTORY_SEPARATOR . "search" . DIRECTORY_SEPARATOR . $type;
			$index = Zend_Search_Lucene::open($path);
			$doc = new Zend_Search_Lucene_Document();
			if($type == "gtdata")
			{
				
				$gtdatamodel = new Model_DbTable_Gtdata();
				$list = $gtdatamodel->getData($id);
				$umodel = new Model_DbTable_Userprofile();
				$user = $umodel->getUser($list['userupdate']);
				$username = $user['firstName'] . " " . $user['lastName'];
				$upmodel = new Model_DbTable_Plant();
				$uplant = $upmodel->getPlant($user['plantId']);
				$uplantname = $uplant['plantName'];
				
				$sysmodel = new Model_DbTable_Gtsystems();
				$sysname = $sysmodel->getSystem($list['sysId']);
				$sysname = $sysname['sysName'];
				
				$subsysmodel = new Model_DbTable_Gtsubsystems();
				if($list['subSysId'] != 34)
				{
					$subsysname = $subsysmodel->getSubSystem($list['subSysId']);
					$subsysname = $subsysname['subSysName'];
				}
				else {
					$subsysname = "-";
				}
				$doc->addField(Zend_Search_Lucene_Field::UnIndexed('dataid',$list['id']));
				
				$doc->addField(Zend_Search_Lucene_Field::UnStored('title',$list['title']));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('data',$list['data']));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('type',$list['type']));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('sysname',$sysname));  
				$doc->addField(Zend_Search_Lucene_Field::UnStored('subsysname',$subsysname));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('userplantname',$uplantname));
			}
			else if($type == "forum")
			{
				$forumModel = new Model_DbTable_Forum_Posts();
				$topicmodel = new Model_DbTable_Forum_Topics();
				if($category == "newtopic")
				{
					$temp_topic = $forumModel->fetchRow("topic_id = " . $id);
					$id = $temp_topic['post_id'];
				}
				$list = $forumModel->getPost($id);
				if($list == 0)
				{
					return;
				}
				$doc = new Zend_Search_Lucene_Document();
				
				$topic = $topicmodel->getTopic($list['topic_id']);
				if($topic == 0)
				{
					return;
				}
				$topicname = $topic['topic_title'];
				$forummodel = new Model_DbTable_Forum_Data();
				$forum = $forummodel->getForum($list['forum_id']);
				$forumname = $forum['forum_name'];
				$posterModel = new Model_DbTable_Userprofile();
				$poster = $posterModel->getUser($list['poster_id']);
				$postername = $poster['firstName'] . " " . $poster['lastName'];
				$plantmodel = new Model_DbTable_Plant();
				$plant = $plantmodel->getPlant($poster['plantId']);
				$uplantname = $plant['plantName'];
				
				$doc->addField(Zend_Search_Lucene_Field::UnIndexed('post_id',$list['post_id']));
				
				$doc->addField(Zend_Search_Lucene_Field::UnStored('post_subject',$list['post_subject']));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('post_text',$list['post_text']));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('topicname',$topicname));
				$doc->addField(Zend_Search_Lucene_Field::UnStored('forumname',$forumname));
				
				$doc->addField(Zend_Search_Lucene_Field::UnStored('userplantname',$uplantname));
			}
			$index->addDocument($doc);  
			$index->commit();  
			$index->optimize();
		}
	}
