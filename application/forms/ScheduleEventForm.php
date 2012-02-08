<?php

class Form_ScheduleEventForm extends Zend_Form {

    public function __construct($options = null) {
        parent::__construct($options);

        $event_date = new ZendX_JQuery_Form_Element_DatePicker('event_day',
                        array('jQueryParams' => array('dateFormat' => 'yy-mm-dd', 'defaultDate' => '0', 'changeYear' => 'true')));
        $event_date->setLabel('Event Date')
                ->addDecorator('Htmltag', array('tag' => 'br'))
                ->addValidator('NotEmpty')
				->setAttrib('class','event-form')
                ->addValidator(Model_Validators::dateval())
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        /*$event_date->setDecorators(array(
        array('UiWidgetElement', array('tag' => '')),
        array('Errors'),
        array('Description', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'td')),
        array('Label', array('tag' => 'td', 'class' =>'element')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));*/

            $timings = new Zend_Form_Element_Text('timing');
            $timings->setLabel('Event Timings')
                    ->addDecorator('Htmltag', array('tag' => 'br'))
                    ->addValidator('NotEmpty')
					->setAttrib('class','event-form')
                    ->addFilter('StripTags')
                    ->addValidator(Model_Validators::regex('/[^a-zA-Z0-9 :-]+/'))
                    ->addFilter('StringTrim');
                    /*->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));*/

            $description = new Zend_Form_Element_Textarea('desc');
            $description->setLabel('Description')
                    ->setAttrib('COLS', '16')
                    ->setAttrib('ROWS', '3')
					->setAttrib('class','event-form')
                    ->addDecorator('Htmltag', array('tag' => 'br'))
                    ->addValidator('NotEmpty')
                    ->addFilter('StripTags');
                    /*->addFilter('StringTrim')->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));*/

        

        $submit = new Zend_Form_Element_Button('add-event-button');
        $submit->addDecorator('Htmltag', array('tag' => 'p'));
        $submit->setAttrib('id', 'add-event-button')
                ->setAttrib('class', 'gt-add event-form')
				->setLabel("Add Event")
                ->setDecorators(array('ViewHelper', 'Description', 'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td',
                    'colspan' => '2', 'align' => 'center')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $this->addElements(array($timings ,$event_date,$description,$submit));
        /*$this->setDecorators(array('FormElements', array(array('data' => 'HtmlTag'), array('tag' => 'table')), 'Form'));*/
    }

}

