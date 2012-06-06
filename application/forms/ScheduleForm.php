<?php

class Form_ScheduleForm extends Zend_Form {
	
    public function __construct($options = null) {
        parent::__construct($options);

        $this->setName('Add Schedule');
        $this->addElementPrefixPath('Hive_Form_Decorators', 'Hive/Form/Decorators', 'decorator');


        $f_day = new ZendX_JQuery_Form_Element_DatePicker('first_day',
                        array('jQueryParams' => array('dateFormat' => 'yy-mm-dd', 'defaultDate' => '0', 'changeYear' => 'true')));
        $f_day->setLabel('First Day')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'br'))
                ->addValidator('NotEmpty')
				->setAttrib('class','sch-form')
                ->addValidator(Model_Validators::dateval())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $f_day->setDecorators(array(
        array('UiWidgetElement', array('tag' => '')),
        array('Errors'),
        array('Description', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'td')),
        array('Label', array('tag' => 'td', 'class' =>'element')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $l_day = new ZendX_JQuery_Form_Element_DatePicker('last_day',
                        array('jQueryParams' => array('dateFormat' => 'yy-mm-dd', 'defaultDate' => '0', 'changeYear' => 'true')));
        $l_day->setLabel('Last Day')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'br'))
                ->addValidator('NotEmpty')
				->setAttrib('class','sch-form')
                ->addValidator(Model_Validators::dateval())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $l_day->setDecorators(array(
        array('UiWidgetElement', array('tag' => '')),
        array('Errors'),
        array('Description', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'td')),
        array('Label', array('tag' => 'td', 'class' =>'element')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

		$cId = new Zend_Form_Element_Hidden('cId');
		$cId->setAttrib('value','');

        $this->addElements(array($f_day, $l_day,$cId));
        $this->setDecorators(array('FormElements', array(array('data' => 'HtmlTag'), array('tag' => 'table')), 'Form'));
    }


}

