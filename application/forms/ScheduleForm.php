<?php

class Form_ScheduleForm extends Zend_Form {

    public function __construct($options = null) {
        parent::__construct($options);

        $this->setName('Add Schedule');
        $this->addElementPrefixPath('Hive_Form_Decorators', 'Hive/Form/Decorators', 'decorator');

        $days = new Zend_Form_Element_Text('days');
        $days->setLabel('No. of Days')
                ->addDecorator('Htmltag', array('tag' => 'br'))
                ->setRequired(true)
                ->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $f_day = new ZendX_JQuery_Form_Element_DatePicker('first_day',
                        array('jQueryParams' => array('dateFormat' => 'yy-mm-dd', 'defaultDate' => '0', 'changeYear' => 'true')));
        $f_day->setLabel('First Day')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'br'))
                ->addValidator('NotEmpty')
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

        $no_event = new Zend_Form_Element_Text('events');
        $no_event->setLabel('No. of Events')
                ->addDecorator('Htmltag', array('tag' => 'br'))
                ->setRequired(true)
                ->addValidator('NotEmpty')
                ->addValidator(Model_Validators::int())
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $submit = new Zend_Form_Element_Button('submit');
        $submit->addDecorator('Htmltag', array('tag' => 'p'));
        $submit->setAttrib('id', 'submitbutton')
                ->setAttrib('class', 'user-save')
                ->setAttrib('type', 'submit')
                ->setDecorators(array('ViewHelper', 'Description', 'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td',
                    'colspan' => '2', 'align' => 'center')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $this->addElements(array($days, $f_day, $l_day, $no_event, $submit));
        $this->setDecorators(array('FormElements', array(array('data' => 'HtmlTag'), array('tag' => 'table')), 'Form'));
    }

}

