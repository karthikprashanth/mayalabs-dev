<?php

class Form_PlantForm extends ZendX_JQuery_Form {

    private $mode;

    public function setMode($m) {
        $this->mode = $m;
    }

    public function showForm() {
    	
        $this->setName('Plant Profile');
        $this->addElementPrefixPath('Hive_Form_Decorators', 'Hive/Form/Decorators', 'decorator');

        $this->setAttrib('id', 'plantForm');
        $this->setDecorators(array('FormElements',
            array('TabContainer', array('id' => 'tabContainer',
                    'style' => 'width: 450px;',
            )), 'Form',
        ));

        $partPlant1 = new ZendX_JQuery_Form();
        $partPlant1->setDecorators(array('FormElements',
            array('HtmlTag',
                array('tag' => 'dl')),
            array('TabPane',
                array('jQueryParams' => array('containerId' => 'plantForm',
                        'title' => 'Corporate Info')))
        ));

        $partPlant2 = new ZendX_JQuery_Form();
        $partPlant2->setDecorators(array('FormElements',
            array('HtmlTag',
                array('tag' => 'dl')),
            array('TabPane',
                array('jQueryParams' => array('containerId' => 'plantForm',
                        'title' => 'Plant Info')))
        ));


        $partPlant3 = new ZendX_JQuery_Form();
        $partPlant3->setDecorators(array('FormElements',
            array('HtmlTag',
                array('tag' => 'dl')),
            array('TabPane',
                array('jQueryParams' => array('containerId' => 'plantForm',
                        'title' => 'GT Statistics')))
        ));


        $corporateName = new Zend_Form_Element_Text('corporateName');
        $corporateName->setLabel('Corporate Name')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                ->addValidator('NotEmpty')
                ->addValidator(Model_Validators::alpha())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
		

        $corporateLocation = new Zend_Form_Element_Text('corporateLocation');
        $corporateLocation->setLabel('Corporate Location')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                ->addValidator('NotEmpty')
                ->addValidator(Model_Validators::alpha())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $corporateProvince = new Zend_Form_Element_Text('corporateProvince');
        $corporateProvince->setLabel('Corporate Province')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                ->addValidator('NotEmpty')
                ->addValidator(Model_Validators::alpha())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $corporateState = new Zend_Form_Element_Text('corporateState');
        $corporateState->setLabel('Corporate State')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                ->addValidator('NotEmpty')
                ->addValidator(Model_Validators::alpha())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $corporateCountry = new Zend_Form_Element_Text('corporateCountry');
        $corporateCountry->setLabel('Corporate Country')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                ->addValidator('NotEmpty')
                ->addValidator(Model_Validators::alpha())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $corporateZip = new Zend_Form_Element_Text('corporateZip');
        $corporateZip->setLabel('Corporate Zip')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                ->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $corporateTelephone = new Zend_Form_Element_Text('corporateTelephone');
        $corporateTelephone->setLabel('Corporate Telephone')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addValidator(Model_Validators::regex('/[^0-9+-]+/'))
                ->addFilter('StringTrim');

        $corporateFax = new Zend_Form_Element_Text('corporateFax');
        $corporateFax->setLabel('Corporate Fax')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                //->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addValidator(Model_Validators::regex('/[^0-9+-]+/'))
                ->addFilter('StringTrim');

        $corporateWebsite = new Zend_Form_Element_Text('corporateWebsite');
        $corporateWebsite->setLabel('Corporate Website')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::regex('/[^a-z0-9_.@]+/'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        if ($this->mode == 'edit') {
            $save1 = new Zend_Form_Element_Submit('submit1');
            $save1->setAttrib('id', 'save1')
                    ->setLabel("Save")
                    ->setAttrib('class', 'user-save');

            $mymode = new Zend_Form_Element_Hidden('modeselect');
            $mymode->setAttrib('id', 'modeselect');

            $savec1 = new Zend_Form_Element_Submit('submit2');
            $savec1->setAttrib('id', 'savec1')
                    ->setLabel("Save & Continue")
                    ->setAttrib('class', 'user-save');
        }
        $plantName = new Zend_Form_Element_Text('plantName');
        $plantName->setLabel('Plant Name')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                ->addValidator('NotEmpty')
                ->addValidator(Model_Validators::alpha())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $plantLocation = new Zend_Form_Element_Text('plantLocation');
        $plantLocation->setLabel('Plant Location')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                ->addValidator('NotEmpty')
                ->addValidator(Model_Validators::alpha())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');


        $plantState = new Zend_Form_Element_Text('plantState');
        $plantState->setLabel('Plant State')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                ->addValidator('NotEmpty')
                ->addValidator(Model_Validators::alpha())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $plantCountry = new Zend_Form_Element_Text('plantCountry');
        $plantCountry->setLabel('Plant Country')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                ->addValidator('NotEmpty')
                ->addValidator(Model_Validators::alpha())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $plantZip = new Zend_Form_Element_Text('plantZip');
        $plantZip->setLabel('Plant Zip')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                ->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $plantTelephone = new Zend_Form_Element_Text('plantTelephone');
        $plantTelephone->setLabel('Plant Telephone')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addValidator(Model_Validators::regex('/[^0-9+-]+/'))
                ->addFilter('StringTrim');

        $plantFax = new Zend_Form_Element_Text('plantFax');
        $plantFax->setLabel('Plant Fax')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::regex('/[^0-9+-]+/'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $plantWebsite = new Zend_Form_Element_Text('plantWebsite');
        $plantWebsite->setLabel('Plant Website')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::regex('/[^a-z0-9_.@]+/'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        /*$numOfGT = new Zend_Form_Element_Text('numOfGT');
        $numOfGT->setLabel('Number Of GT')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                ->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');*/
        if ($this->mode == 'edit') {
            $save2 = new Zend_Form_Element_Submit('submit3');
            $save2->setAttrib('id', 'save2')
                    ->setLabel("Save")
                    ->setAttrib('class', 'user-save');

            $savec2 = new Zend_Form_Element_Submit('submit4');
            $savec2->setAttrib('id', 'savec2')
                    ->setLabel("Save & Continue")
                    ->setAttrib('class', 'user-save');
        }

        $GTMake = new Zend_Form_Element_Select('GTMake');
        $GTMake->setLabel('GT Make')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                //->addValidator('NotEmpty')
                ->addMultiOptions(array('Siemens' => 'Siemens'))
                ->addValidator(Model_Validators::alpha())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $GTBaseModel = new Zend_Form_Element_Select('GTBaseModel');
        $GTBaseModel->setLabel('GT Base Model')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                ->addMultiOptions(array('v93.4A' => 'v93.4A'))
                //->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $plantAmbientTempMax = new Zend_Form_Element_Text('plantAmbientTempMax');
        $plantAmbientTempMax->setLabel('Plant Ambient Temp Max')
				->setValue(NULL)
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $plantAmbientTempMin = new Zend_Form_Element_Text('plantAmbientTempMin');
        $plantAmbientTempMin->setLabel('Plant Ambient Temp Min')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $plantAmbientTempAvg = new Zend_Form_Element_Text('plantAmbientTempAvg');
        $plantAmbientTempAvg->setLabel('Plant Ambient Temp Avg')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $PLFMax = new Zend_Form_Element_Text('PLFMax');
        $PLFMax->setLabel('PLF Max')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $PLFMin = new Zend_Form_Element_Text('PLFMin');
        $PLFMin->setLabel('PLF Min')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');


        $PLFAvg = new Zend_Form_Element_Text('PLFAvg');
        $PLFAvg->setLabel('PLF Avg')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');



        $GTStartMax = new Zend_Form_Element_Text('GTStartMax');
        $GTStartMax->setLabel('GT Start Max')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');



        $GTStartMin = new Zend_Form_Element_Text('GTStartMin');
        $GTStartMin->setLabel('GT Start Min')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                ->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $GTStartAvg = new Zend_Form_Element_Text('GTStartAvg');
        $GTStartAvg->setLabel('GT Start Avg')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');


        $GTTripMax = new Zend_Form_Element_Text('GTTripMax');
        $GTTripMax->setLabel('GT Trip Max')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');


        $GTTripMin = new Zend_Form_Element_Text('GTTripMin');
        $GTTripMin->setLabel('GT Trip Min')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');


        $GTTripAvg = new Zend_Form_Element_Text('GTTripAvg');
        $GTTripAvg->setLabel('GT Trip Avg')
                //->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'div'))
                //->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        if ($this->mode == 'edit') {
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->addDecorator('Htmltag', array('tag' => 'p'));
            $submit->setAttrib('id', 'save3')->setAttrib('class', 'gt-add');

            $savec3 = new Zend_Form_Element_Submit('submit5');
            $savec3->setAttrib('id', 'savec3')
                    ->setLabel("Save & Continue")
                    ->setAttrib('class', 'user-save');
        }

        if ($this->mode == 'add') {
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->addDecorator('Htmltag', array('tag' => 'p'));
            $submit->setAttrib('id', 'save3')
                    ->setAttrib('class', 'gt-add');
        }

        $partPlant1->addElements(array($corporateName, $corporateLocation,
            $corporateProvince, $corporateState, $corporateCountry, $corporateZip,
            $corporateTelephone, $corporateFax, $corporateWebsite, $save1, $savec1, $mymode));
        $partPlant2->addElements(array($plantName,
            $plantLocation, $plantState, $plantCountry, $plantZip, $plantTelephone,
            $plantFax, $plantWebsite, $save2, $savec2));
        $partPlant3->addElements(array($GTMake, $GTBaseModel,
            $plantAmbientTempMax, $plantAmbientTempMin, $plantAmbientTempAvg,
            $PLFMax, $PLFMin, $PLFAvg, $GTStartMax, $GTStartMin, $GTStartAvg,
            $GTTripMax, $GTTripMin, $GTTripAvg, $submit, $save3, $savec3));

        $this->addSubForm($partPlant1, 'partPlant1');
        $this->addSubForm($partPlant2, 'partPlant2');
        $this->addSubForm($partPlant3, 'partPlant3');

//        $this->setDecorators(array('FormElements', array(array('data' => 'HtmlTag'), array('tag' => 'table')), 'partPlant1'));
//        $this->setDecorators(array('FormElements', array(array('data' => 'HtmlTag'), array('tag' => 'table')), 'partPlant2'));
//        $this->setDecorators(array('FormElements', array(array('data' => 'HtmlTag'), array('tag' => 'table')), 'partPlant3'));

    }

}

?>
