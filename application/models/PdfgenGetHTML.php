<?php
	class Model_PdfgenGetHTML
	{
		public function getHTMLStr($type,$id,$mode = "")
		{
			$doctype = "<?xml version='1.0' encoding='iso-8859-1'?>
						<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>";
			$head =	"<head>
						<title>Nations and Flags</title>
						<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
					</head>";
			if(in_array($type,array('finding','upgrade','lte')))
			{
				$gtdatamodel = new Model_DbTable_Gtdata();
				$gtmodel = new Model_DbTable_Gasturbine();
				$pmodel = new Model_DbTable_Plant();
				$umodel = new Model_DbTable_Userprofile();
				$presmodel = new Model_DbTable_Presentation();
				$sysmodel = new Model_DbTable_Gtsystems();
				$subsysmodel = new Model_DbTable_Gtsubsystems();
				
				$gtdata = $gtdatamodel->getData($id);
				$gt = $gtmodel->getGT($gtdata['gtid']);
				$plant = $pmodel->getPlant($gt['plantId']);
				$user = $umodel->getUser($gtdata['userupdate']);
				$uplant = $pmodel->getPlant($user['plantId']);
				$subsys = $subsysmodel->getSubSystem($gtdata['subSysId']);
				$system = $sysmodel->getSystem($subsys['sysId']);
				 
				$gtdatatype = ucfirst($type);
				$plantname = $plant['plantName'];
				$gtname = $gt['GTName'];
				$title = $gtdata['title'];
				$sys = $system['sysName'];
				$subsys = $subsys['subSysName'];
				$data = strip_tags($gtdata['data']);
				$username = $user['firstName'] . " " . $user['lastName'];
				$uplantname = $uplant['plantName'];
				$datetime = $gtdata['updatedate'];
				$presdata = explode(",",substr($gtdata['presentationId'],0,strlen($gtdata['presentationId'])-1));
				$pres = "";
				for($i=0;$i<count($presdata);$i++)
				{
					$presentation = $presmodel->getPresentation($presdata[$i]);
					$pres = $pres . $presentation['title'] . ",";
				}
				$pres = substr($pres,0,strlen($pres)-1);
				
				if($mode == '')
				{
					$bodycontent = "<center><h1>".$plantname."</h1></center>";
					$bodycontent .= "<center><h2>" . $gtdatatype . " Report of " . $gtname . "</h2></center>";
				}
				$bodycontent .= "<center><h2>" . $title . "</h2></center>";
				$bodycontent .= "<center><h3>System/Subsystem : " . $sys . "/" . $subsys . "</h3></center>";
				$bodycontent .= "<center>" . $data . "</center><br><br>";
				$bodycontent .= "<b>Presentations : </b>" . $pres . "<br>";
				$bodycontent .= "<i><b>Last updated on </b>" . $datetime . " <b>by</b> " . $username . " (" . $uplantname . ")</i><br><br>";
				
			}
			else if(in_array($type,array('fdatalist','udatalist','ldatalist'))) 
			{
				$gtdatatype['fdatalist'] = "finding";
				$gtdatatype['udatalist'] = "upgrade";
				$gtdatatype['ldatalist'] = "lte";
				$gtdatamodel = new Model_DbTable_Gtdata();
				
				$datatype = ($gtdatatype[$type] != 'lte')?ucfirst($gtdatatype[$type]) . "s" : strtoupper($gtdatatype[$type]) . "s";
					
				$gtmodel = new Model_DbTable_Gasturbine();
				$pmodel = new Model_DbTable_Plant();
				$umodel = new Model_DbTable_Userprofile();
				$presmodel = new Model_DbTable_Presentation();
				
				$gtdatalist = $gtdatamodel->getDataByType($id, $gtdatatype[$type]);
				$gt = $gtmodel->getGT($id);
				$plant = $pmodel->getPlant($gt['plantId']);
				$plantname = $plant['plantName'];
				$gtname = $gt['GTName'];
				if($mode == "")
				{
					$bodycontent = "<center><h1>".$plantname."</h1></center>";
					$bodycontent .= "<center><h2>" . $datatype . " Report of " . $gtname . "</h2></center>";
				}
				else {
					$bodycontent = "<center><h2><u>".$datatype."</u></h2></center>";
				}
				foreach($gtdatalist as $gtdata)
				{
					$bodycontent .= $this->getHTMLStr($gtdatatype[$type], $gtdata['id'],"recur");
				}
			}
			else {
				$gtmodel = new Model_DbTable_Gasturbine();
				$pmodel = new Model_DbTable_Plant();
				
				$gt = $gtmodel->getGT($id);
				$plant = $pmodel->getPlant($gt['plantId']);
				$plantname = $plant['plantName'];
				$gtname = $gt['GTName'];
				$gtmodel = $gt['GTModelNum'];
				$eoh = $gt['EOHDate'];
				$starts = $gt['numStarts'];
				$trips = $gt['numTrips'];
				$minorinspint = $gt['minorInspInter'];
				$hgpiinspint = $gt['HGPIInspInter'];
				$ehgpiinspint = $gt['EHGPIInspInter'];
				$minor = $gt['nextMinor'];
				$major = $gt['nextMajor'];
				if($mode == "")
				{
					$bodycontent = "<center><h1>".$plantname."</h1></center>";
					$bodycontent .= "<center><h2>Machine Report of " . $gtname . "</h2></center>";
				}
				$bodycontent .= "<center><table border='1' width='500' cellspacing='0' cellpadding='2' align='center'>
				<tr><td><b>GT Name</b></td><td width='200'>$gtname</td></tr>
				<tr><td><b>GT Model Number</b></td><td width='200'>$gtmodel</td></tr>
				<tr><td><b>Plant Name</b></td><td width='200'>$plantname</td></tr>
				<tr><td><b>EOH Date</b></td><td width='200'>$eoh</td></tr>
				<tr><td><b>Number of Starts</b></td><td width='200'>$starts</td></tr>
				<tr><td><b>Number of Trips</b></td><td width='200'>$trips</td></tr>
				<tr><td><b>Minor Inspection Inter</b></td><td width='200'>$minorinspint</td></tr>
				<tr><td><b>HGPI Inspection Inter</b></td><td width='200'>$hgpiinspint</td></tr>
				<tr><td><b>EHGPI Inspection Inter</b></td><td width='200'>$ehgpiinspint</td></tr>
				<tr><td><b>Next Minor Inspection</b></td><td width='200'>$minor</td></tr>
				<tr><td><b>Next Major Inspection</b></td><td width='200'>$major</td></tr>
				</table></center><br><br>";
				if($mode == "")
				{
					$gtdatamodel = new Model_DbTable_Gtdata();
					if($gtdatamodel->getTypeCount('finding',$id))
					{
						$bodycontent .= $this->getHTMLStr("fdatalist", $id,"recur");
					}
					if($gtdatamodel->getTypeCount('upgrade',$id))
					{
						$bodycontent .= $this->getHTMLStr("udatalist", $id,"recur");
					}
					if($gtdatamodel->getTypeCount('lte',$id))
					{
						$bodycontent .= $this->getHTMLStr("ldatalist", $id,"recur");
					}
				}
				
			}
			
			$body =	"<body>".$bodycontent."</body>";
			
			$html = $doctype . $body . $head;
			return $html;
			
		}
	}
?>