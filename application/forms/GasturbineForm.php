<?php

class Form_GasturbineForm extends Zend_Form {

    public function __construct($options = null) {
        parent::__construct($options);

        $this->setName('Gas Turbine Profile');
        $this->addElementPrefixPath('Hive_Form_Decorators', 'Hive/Form/Decorators', 'decorator');
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
        if (Zend_Registry::get('role') == 'sa') {
            $plantObj = new Model_DbTable_Plant();
            $plantValue = $plantObj->fetchAll();

            $data = array();
            $data[''] = 'Select an Option';
            foreach ($plantValue as $pl) {
                if ($pl['plantId'] != 1) {
                    $data[$pl->plantId] = $pl->plantName;
                }
            }

            $plant = new Zend_Form_Element_Select('plantid');
            $plant->setLabel('Plant')
                    ->addMultiOptions($data)
                    ->addDecorator('Htmltag', array('tag' => 'br'))
                    ->setRequired(true)
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->addValidator('NotEmpty');

            $plant->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
                'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
            ));
        }

        $GTName = new Zend_Form_Element_Text('GTName');
        $GTName->setLabel('GT Name')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'br'))
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator(Model_Validators::alnum());

        $GTName->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $gtid = new Zend_Form_Element_Hidden('GTId');
        $gtid->setAttrib('GTId', $this->getId());

        $GTModelNum = new Zend_Form_Element_Select('GTModelNum');
        $GTModelNum->setLabel('GT Model Number')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'br'))
                ->addMultiOptions(array('v93.4A' => 'v93.4A'))
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $GTModelNum->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $EOHDate = new ZendX_JQuery_Form_Element_DatePicker('EOHDate',
                        array('jQueryParams' => array('dateFormat' => 'yy-mm-dd', 'defaultDate' => '0', 'changeYear' => 'true'))
        );

        $EOHDate->setLabel('EOH Date (last updated)')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'br'))
                ->addValidator('NotEmpty')
                ->addValidator(Model_Validators::dateval())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $EOHDate->setDecorators(array(
        array('UiWidgetElement', array('tag' => '')),
        array('Errors'),
        array('Description', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'td')),
        array('Label', array('tag' => 'td', 'class' =>'element')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $EOH = new Zend_Form_Element_Text('EOH');
        $EOH->setLabel('EOH')
                ->addDecorator('Htmltag', array('tag' => 'br'))
                ->setRequired(true)
                ->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $EOH->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));



        $numStarts = new Zend_Form_Element_Text('numStarts');
        $numStarts->setLabel('Number of Starts')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'br'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $numStarts->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $numTrips = new Zend_Form_Element_Text('numTrips');
        $numTrips->setLabel('Number of Trips')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'br'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $numTrips->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $minorInspInter = new Zend_Form_Element_Text('minorInspInter');
        $minorInspInter->setLabel('Minor Inspection Inter')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'br'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $minorInspInter->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $HGPIInspInter = new Zend_Form_Element_Text('HGPIInspInter');
        $HGPIInspInter->setLabel('HGPI Inspection Inter')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'br'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $HGPIInspInter->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $EHGPIInspInter = new Zend_Form_Element_Text('EHGPIInspInter');
        $EHGPIInspInter->setLabel('EHGPI Inspection Inter')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'br'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $EHGPIInspInter->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $nextMinor = new ZendX_JQuery_Form_Element_DatePicker('nextMinor',
                        array('jQueryParams' => array('dateFormat' => 'yy-mm-dd', 'defaultDate' => '0', 'changeYear' => 'true'))
        );
        $nextMinor->setLabel('Next Minor Inspection')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'br'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::dateval())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $nextMinor->setDecorators(array(
        array('UiWidgetElement', array('tag' => '')),
        array('Errors'),
        array('Description', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'td')),
        array('Label', array('tag' => 'td', 'class' =>'element')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $nextMajor = new ZendX_JQuery_Form_Element_DatePicker('nextMajor',
                        array('jQueryParams' => array('dateFormat' => 'yy-mm-dd', 'defaultDate' => '0', 'changeYear' => 'true'))
        );
        $nextMajor->setLabel('Next Major Inspection')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'br'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::dateval())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $nextMajor->setDecorators(array(
        array('UiWidgetElement', array('tag' => '')),
        array('Errors'),
        array('Description', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'td')),
        array('Label', array('tag' => 'td', 'class' =>'element')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $submit = new Zend_Form_Element_Button('submit');
        $submit->addDecorator('Htmltag', array('tag' => 'p'));
        $submit->setAttrib('id', 'submitbutton')
                ->setAttrib('type', 'submit');
        $submit->setDecorators(array('ViewHelper', 'Description', 'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td',
                    'colspan' => '2', 'align' => 'center')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        if (Zend_Registry::get('role') == 'sa')
            $this->addElement($plant);

        $this->addElements(array($GTName, $gtid, $GTModelNum, $EOHDate, $EOH, $numStarts,
            $numTrips, $minorInspInter, $HGPIInspInter, $EHGPIInspInter, $nextMinor,
            $nextMajor, $submit));

        $this->setDecorators(array('FormElements', array(array('data' => 'HtmlTag'), array('tag' => 'table')), 'Form'));
    }

}

?>
