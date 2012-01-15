<?php

class Form_GTDataForm extends Zend_Form {

    public function showform($gturbineid, $gtdataid, $gtdatatype) {        
        $this->setName('GTData');
        
//        $pObj = new Model_DbTable_Presentation();
//        $presentationValue = $pObj->fetchAll("GTId = " . $gturbineid);
        
        $presentationValue = Model_DbTable_Presentation::getList($gturbineid);        

        $data = array();
        $data[''] = 'Select an Option';
        if ($gtdatatype == 'finding') {
            $doflabel = "Finding";
        } else {
            $doflabel = "Implementation";
        }        
        if ($gtdataid == 0) {
            foreach ($presentationValue as $pl) {
                $data[$pl->presentationId] = $pl->title;
            }            
        } else {
            $gtdatamodel = new Model_DbTable_Gtdata(Zend_Db_Table_Abstract::getDefaultAdapter(), $gtdataid);
            $gtdata = $gtdatamodel->getData();            

            $presid = explode(",", substr($gtdata['presentationId'], 0, strlen($gtdata['presentationId']) - 1));
            foreach ($presentationValue as $pl) {
                $add = true;
                for ($i = 0; $i < count($presid); $i++) {
                    if ($presid[$i] == $pl['presentationId']) {
                        $add = false;
                    }
                }
                if ($add) {
                    $data[$pl->presentationId] = $pl->title;
                }
            }
        }
        
//        $system = $sysModel->fetchAll();
        $system = Model_DbTable_Gtsystems::getList();

        
//        $subsystem = $sysSubModel->fetchAll();
        $subsystem = Model_DbTable_Gtsubsystems::getList();
        
        
        $sysNames = array();
        $sysNames[''] = 'Select an Option';
        $sysSubNames[''] = 'Select an Option';
        foreach ($system as $list) {            
            $sysNames[$list['sysId']] = $list['sysName'];
        }
        foreach ($subsystem as $slist) {            
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

        $prestitle1 = new Zend_Form_Element_Text('prestitle1');
        $prestitle1->setLabel('Attachment Title')
                
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $prestitle1->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $content1 = new Zend_Form_Element_File('content1');
        $content1->setLabel('Upload the File')
                ->setDestination($appath . '/public/uploads');

        $addmore = new Zend_Form_Element_Button('addmore');
        $addmore->setAttrib('id', 'addmore')
                ->setLabel("...")
                ->setAttrib("class", "gt-add");



        $prestitle2 = new Zend_Form_Element_Text('prestitle2');
        $prestitle2->setLabel('Attachment Title')
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $prestitle2->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $content2 = new Zend_Form_Element_File('content2');
        $content2->setLabel('Upload the File')
                ->setDestination($appath . '/public/uploads');


        $del2 = new Zend_Form_Element_Button('del2');
        $del2->setAttrib('id', 'del2')
                ->setLabel("...")
                ->setAttrib("class", "gt-delete");

        $prestitle3 = new Zend_Form_Element_Text('prestitle3');
        $prestitle3->setLabel('Attachment Title')
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $prestitle3->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $content3 = new Zend_Form_Element_File('content3');
        $content3->setLabel('Upload the File')
                ->setDestination($appath . '/public/uploads');


        $del3 = new Zend_Form_Element_Button('del3');
        $del3->setAttrib('id', 'del3')
                ->setLabel("...")
                ->setAttrib("class", "gt-delete");

        $prestitle4 = new Zend_Form_Element_Text('prestitle4');
        $prestitle4->setLabel('Attachment Title')
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $prestitle4->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $content4 = new Zend_Form_Element_File('content4');
        $content4->setLabel('Upload the File')
                ->setDestination($appath . '/public/uploads');


        $del4 = new Zend_Form_Element_Button('del4');
        $del4->setAttrib('id', 'del4')
                ->setLabel("...")
                ->setAttrib("class", "gt-delete");

        $prestitle5 = new Zend_Form_Element_Text('prestitle5');
        $prestitle5->setLabel('Attachment Title')
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $prestitle5->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $content5 = new Zend_Form_Element_File('content5');
        $content5->setLabel('Upload the File')
                ->setDestination($appath . '/public/uploads');

        $del5 = new Zend_Form_Element_Button('del5');
        $del5->setAttrib('id', 'del5')
                ->setLabel("...")
                ->setAttrib("class", "gt-delete");


        $info2 = new Zend_Form_Element_Hidden('info2');
        $info2->setLabel("Attach Files")
                ->addDecorator('Htmltag', array('tag' => 'b'));

        $info = new Zend_Form_Element_Hidden('info');
        $info->setLabel("(allowed formats - pdf,ppt,pptx,xls,xlsx,doc,docx,jpg,jpeg,png,gif)")
                ->addDecorator('Htmltag', array('tag' => 'b'));

        $pid = new Zend_Form_Element_Multiselect('presentationId');
        $pid->setLabel('Or Choose an Existing Presentation')
                ->addMultiOptions($data)
                
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $pid->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));


        $gtid = new Zend_Form_Element_Hidden('gtid');
        $gtid->setAttrib('value', 'TESTING');

        $id = new Zend_Form_Element_Hidden('id');
        $id->setAttrib('id', $this->getId());

        $Title = new Zend_Form_Element_Text('title');
        $Title->setLabel('Title')
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
            $info2, $info, $prestitle1, $content1, $prestitle2,
            $content2, $del2, $prestitle3, $content3, $del3, $prestitle4,
            $content4, $del4, $prestitle5, $content5, $del5, $addmore, $pid, $Title, $Data, $submit));
        $this->setDecorators(array('FormElements', array(array('data' => 'HtmlTag'), array('tag' => 'table')), 'Form'));
    }

}

?>