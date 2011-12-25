<?php

class Form_ScheduleEventForm extends Zend_Form {

    private $event_no;

    public function init() {

    }

    public function setmyvar($e_no) {
        $this->event_no = $e_no;
    }

    public function startform() {
        $i = 0;

        while ($i < $this->event_no) {

            $event_date = new ZendX_JQuery_Form_Element_DatePicker('event_day' . $i,
                            array('jQueryParams' => array('dateFormat' => 'yy-mm-dd', 'defaultDate' => '0', 'changeYear' => 'true')));
            $event_date->setLabel('Event Date')
                    ->setRequired(true)
                    ->addDecorator('Htmltag', array('tag' => 'br'))
                    ->addValidator('NotEmpty')
                    ->addValidator(Model_Validators::dateval())
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim');
            $event_date->setDecorators(array(
        array('UiWidgetElement', array('tag' => '')),
        array('Errors'),
        array('Description', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'td')),
        array('Label', array('tag' => 'td', 'class' =>'element')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

            $timings = new Zend_Form_Element_Text('timing' . $i);
            $timings->setLabel('Event Timings')
                    ->addDecorator('Htmltag', array('tag' => 'br'))
                    ->setRequired(true)
                    ->addValidator('NotEmpty')
                    ->addFilter('StripTags')
                    ->addValidator(Model_Validators::regex('/[^a-zA-Z0-9 :-]+/'))
                    ->addFilter('StringTrim')
                    ->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

            $description = new Zend_Form_Element_Textarea('desc' . $i);
            $description->setLabel('Description')
                    ->setAttrib('COLS', '16')
                    ->setAttrib('ROWS', '3')
                    ->addDecorator('Htmltag', array('tag' => 'br'))
                    ->setRequired(true)
                    ->addValidator('NotEmpty')
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

            $this->addElements(array($event_date, $timings, $description));
            $i++;
        }

        $submit = new Zend_Form_Element_Button('Add');
        $submit->addDecorator('Htmltag', array('tag' => 'p'));
        $submit->setAttrib('id', 'submitbutton')
                ->setAttrib('class', 'gt-add')
                ->setAttrib('type', 'submit')
                ->setDecorators(array('ViewHelper', 'Description', 'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td',
                    'colspan' => '2', 'align' => 'center')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $this->addElements(array($submit));
        $this->setDecorators(array('FormElements', array(array('data' => 'HtmlTag'), array('tag' => 'table')), 'Form'));
    }

}

