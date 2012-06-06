<?php

class ReportsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    
    }

    public function generateAction()
    {
    	if($this->getRequest()->isPost())
		{
			
			$type = $this->getRequest()->getPost('type');
			$id = $this->getRequest()->getPost('id');
			$pdfgenModel = new Model_PdfgenGetHTML();
			$htmlStr = $pdfgenModel->getHTMLStr($type,$id);
			
			$funcs = new Model_Functions();
	    	$ippath = APPLICATION_PATH;
			$ippath = substr($ippath,0,strlen($ippath)-11);
			$ippath = $ippath . "public" . DIRECTORY_SEPARATOR . "pdfgen". DIRECTORY_SEPARATOR;
			$ippath = $funcs->pathFormat($ippath);
			
			$oppath = APPLICATION_PATH;
			$oppath = substr($oppath,0,strlen($oppath)-11);
			$oppath = $oppath . "public" . DIRECTORY_SEPARATOR . "pdfgen". DIRECTORY_SEPARATOR;
			$oppath = $oppath . "reports" . DIRECTORY_SEPARATOR;
			$oppath = $funcs->pathFormat($oppath);
			
			ini_set("memory_limit","100M");
			require($ippath . 'html2fpdf.php');
			$pdf=new HTML2FPDF();
			$pdf->AddPage();
			$pdf->WriteHTML($htmlStr);
			$filename = strtoupper($type) ."_".rand(1,999999) .".pdf";
			$pdf->Output($oppath . $filename);
			$this->_redirect("/pdfgen/reports/".$filename);
			
		}
        
    }

    public function configureAction()
    {
        if($this->getRequest()->isPost())
        {
	       	
	       	$id = $this->_getParam('id',0);
	       	$type = $this->_getParam('type',0);
	       	
	       	if($type == "gtdataview")
	       	{
	       		$gtdatamodel = new Model_DbTable_Gtdata();
	       		$gtdata = $gtdatamodel->getData($id);
	       		$gtid = $gtdata['gtid'];
	       	}
	       	else {
	       		$gtid = $id;
	       	}
			$form = new Form_ReportsForm();
			$form->setVars($id,$type);
			$form->showForm();
			
			$form->setAction("/reports/genconfig?gtid=".$gtid);
			$form->setMethod("post");
			
			$data = $this->getRequest()->getPost();
			
			$this->view->form = $form;
		}
    }

    public function genconfigAction()
    {
        
        $funcs = new Model_Functions();
		
		$ippath = APPLICATION_PATH;
		$ippath = substr($ippath,0,strlen($ippath)-11);
		$ippath = $ippath . "public" . DIRECTORY_SEPARATOR . "pdfgen". DIRECTORY_SEPARATOR;
		$ippath = $funcs->pathFormat($ippath);
		
		$oppath = APPLICATION_PATH;
		$oppath = substr($oppath,0,strlen($oppath)-11);
		$oppath = $oppath . "public" . DIRECTORY_SEPARATOR . "pdfgen". DIRECTORY_SEPARATOR;
		$oppath = $oppath . "reports" . DIRECTORY_SEPARATOR;
		$oppath = $funcs->pathFormat($oppath);
        
        $gtdet = $this->getRequest()->getPost('gtdet');
        $fvalues = $this->getRequest()->getPost('findings');
        $uvalues = $this->getRequest()->getPost('upgrades');
        $lvalues = $this->getRequest()->getPost('ltes');
        
        $f = (count($fvalues) > 0)?1:0;
        $u = (count($uvalues) > 0)?1:0;
        $l = (count($lvalues) > 0)?1:0;
        $id = $this->_getParam('gtid',0);
        $valid = count($fvalues) + count($uvalues) + count($lvalues);
        
        if(!$valid && !$gtdet)
        {
        	echo "Atleast one option has to be selected";
        	return;
        }
		
		$pdfgenModel = new Model_PdfgenGetHTML();
		$gtmodel = new Model_DbTable_Gasturbine();
		$pmodel = new Model_DbTable_Plant();
		$gt = $gtmodel->getGT($id);
		$plant = $pmodel->getPlant($gt['plantId']);
		$plantname = $plant['plantName'];
		$gtname = $gt['GTName'];
		$html = "<center><h1>".$plantname."</h1></center>";
		$html .= "<center><h2>Machine Report of " . $gtname . "</h2></center>";
		
		if($gtdet)
		{
			$html .= $pdfgenModel->getHTMLStr("machinedet", $id,"ext");		
		}
		
		if($f)
		{
			$html .= "<center><h2><u>Findings</u></h2></center>";
			for($i=0;$i<count($fvalues);$i++)
			{
				$html .= $pdfgenModel->getHTMLstr("finding",$fvalues[$i],"ext");
			}
		}
		
		if($u)
		{
			$html .= "<center><h2><u>Upgrades</u></h2></center>";
			for($i=0;$i<count($uvalues);$i++)
			{
				$html .= $pdfgenModel->getHTMLstr("upgrade",$uvalues[$i],"ext");
			}
			
		}
		if($l)
		{
			$html .= "<center><h2><u>LTEs</u></h2></center>";
			for($i=0;$i<count($lvalues);$i++)
			{
				$html .= $pdfgenModel->getHTMLstr("lte",$lvalues[$i],"ext");
			}
		}
		
		ini_set("memory_limit","100M");
		require($ippath . 'html2fpdf.php');
		$pdf=new HTML2FPDF();
		$pdf->AddPage();
		$pdf->WriteHTML($html);
		$filename = "MACHINEFULL_".rand(1,999999) .".pdf";
		$pdf->Output($oppath . $filename);
		$this->_redirect("/pdfgen/reports/".$filename);
	
    }
}