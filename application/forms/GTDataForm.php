<?php

class Form_GTDataForm extends Zend_Form {

    public function showform($gtid, $gtdataid = 0, $gtdatatype,$attach_ids = array()) {        
        $this->setName('GTData');
		       
        $data = array();
        $data[''] = 'Select an Option';
        if ($gtdatatype == 'finding') {
            $doflabel = "Finding";
        } else {
            $doflabel = "Implementation";
        }        
        
        $system = Model_DbTable_Gtsystems::getList();
		$sysNames[''] = 'Select an Option';
		foreach ($system as $list) {            
            $sysNames[$list['sysId']] = $list['sysName'];
        }
        
		if(!$gtdataid){
        	$subsystem = Model_DbTable_Gtsubsystems::getList();
		}
		else{
			$gtdata = new Model_DbTable_Gtdata(Zend_Db_Table_Abstract::getDefaultAdapter(),$gtdataid);
			$gtdArray = $gtdata->getData();
			$sysid = $gtdArray['sysId']; 
			$subsystem = Model_DbTable_Gtsubsystems::getList($sysid);
			
		}
		
        $sysSubNames[''] = 'Select an Option';
        foreach ($subsystem as $slist) {
        	if($slist['subSysName'] == '-') continue;            
            $sysSubNames[$slist['id']] = $slist['subSysName'];
        }

        $sys = new Zend_Form_Element_Select('sysId');
        $sys->setLabel('System Name')
                ->addMultiOptions($sysNames)
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');
        $sys->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $subsys = new Zend_Form_Element_Select('subSysId');
        $subsys->setLabel('Sub System Name')
                ->addMultiOptions($sysSubNames)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');
        $subsys->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $appath = substr(APPLICATION_PATH, 0, strlen(APPLICATION_PATH) - 12);

        $eoh = new Zend_Form_Element_Text('EOH');
        $eoh->setLabel('EOH at Occurence')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator(Model_Validators::int());
        $eoh->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $dof = new ZendX_JQuery_Form_Element_DatePicker('DOF',
                        array('jQueryParams' => array('dateFormat' => 'yy-mm-dd', 'defaultDate' => '0', 'changeYear' => 'true')));
        $dof->setLabel('Date of ' . $doflabel)
                
                ->addValidator(Model_Validators::dateval())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $dof->setDecorators(array(
        array('UiWidgetElement', array('tag' => '')),
        array('Errors'),
        array('Description', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'td')),
        array('Label', array('tag' => 'td', 'class' =>'element')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $insp = array('' => 'Select an Option', 'Minor' => 'Minor', 'HGPI' => 'HGPI', 'EHGPI' => 'EHGPI', 'Major' => 'Major', 'Unscheduled' => 'Unscheduled', 'Others' => 'Others');

        $toi = new Zend_Form_Element_Select('TOI');
        $toi->setLabel('Type of Inspection')
                ->addMultiOptions($insp)
                ->addDecorator('Htmltag', array('tag' => 'p'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');
        $toi->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

		$attachList = Model_DbTable_GtAttachment::getList(array('columns' => array('gtid' => $gtid)));
		
		$attachments[""] = "Select Attachments";
		foreach($attachList as $attach){
			if(count($attach_ids) && in_array($attach['attachmentId'],$attach_ids)){
				continue;
			}			
			$a = new Model_DbTable_Attachment(Zend_Db_Table_Abstract::getDefaultAdapter(),$attach['attachmentId']);
			$attachments[$a->getId()] = $a->getTitle();
		}
        $pid = new Zend_Form_Element_Multiselect('presentationId');
        $pid->setLabel('Choose Existing Attachments (Press Ctrl key and choose multiple attachments)');
        $pid->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));		
		
		$pid->addMultiOptions($attachments);
		
		
		
		$newAttach = new Zend_Form_Element_Button("add-attach");
		$newAttach->setLabel("Add a new attachment")
				  ->setAttrib("class","gt-add")
				  ->setAttrib("id","add-attach")
				  ->setAttrib("value","Add a new attachment")
				  ->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));
		
		$attach_ids = new Zend_Form_Element_Hidden("attach_ids");
		$attach_ids->setAttrib("value","TESTING")
				   ->setAttrib("id","attach_ids");

        $gtid = new Zend_Form_Element_Hidden('gtid');
        $gtid->setAttrib('value', 'TESTING');

        $id = new Zend_Form_Element_Hidden('id');
        $id->setAttrib('id', $this->getId());

        $Title = new Zend_Form_Element_Text('title');
        $Title->setLabel(($gtdatatype == 'lte'?strtoupper($gtdatatype):ucfirst($gtdatatype)) . ' Title')
                ->setRequired(true)
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $Title->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));


        $Data = new Zend_Form_Element_Textarea('data');
        $Data->setLabel('Data')
                ->setRequired(true)
                ->addValidator('NotEmpty')
                ->addFilter('StringTrim');
        $Data->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $submit = new Zend_Form_Element_Button('submit');
        $submit->addDecorator('Htmltag', array('tag' => 'p'));
        $submit->setAttrib('id', 'submitbutton')
                ->setAttrib('class', 'gt-add')
                ->setAttrib('type', 'submit');
        $submit->setDecorators(array('ViewHelper', 'Description', 'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td',
                    'colspan' => '2', 'align' => 'center')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $this->addElements(array($id, $gtid, $sys, $subsys, $eoh, $dof, $toi,
            $info2, $info, $addmore, $pid, $newAttach,$Title, $Data, $submit,$attach_ids));
        $this->setDecorators(array('FormElements', array(array('data' => 'HtmlTag'), array('tag' => 'table')), 'Form'));
    }

}

?>