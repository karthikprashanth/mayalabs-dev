<?php

class Form_ConferenceForm extends Zend_Form {

    public function __construct($options = null) {
        parent::__construct($options);

        $place = new Zend_Form_Element_Text('place');
        $place->setLabel('Place')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'br'))
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator(Model_Validators::alnum());
        $place->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $year = new Zend_Form_Element_Text('year');
        $year->setLabel('Year')
                ->setRequired(true)
                ->addDecorator('Htmltag', array('tag' => 'br'))
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator(Model_Validators::int())
                ->addValidator(Model_Validators::stringlength(4, 4));
        $year->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $plantObj = new Model_DbTable_Plant();
        $plantValue = $plantObj->fetchAll();

        $data = array();
        $data[''] = 'Select an Option';
        foreach ($plantValue as $pl) {
            if ($pl['plantId'] != 1) {
                $data[$pl->plantId] = $pl->plantName;
            }
        }

        $host = new Zend_Form_Element_Select('host');
        $host->setLabel('Host')
                ->addMultiOptions($data)
                ->addDecorator('Htmltag', array('tag' => 'br'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $host->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

		$ablabel = new Zend_Form_Element_Hidden('ablabel');
		$ablabel->setLabel('Abstract');
		$ablabel->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));
        $abstract = new Zend_Form_Element_Textarea('abstract');
        $abstract
                ->setRequired(true)
                ->setAttrib('COLS', '16')
                ->setAttrib('ROWS', '5')
                ->addDecorator('Htmltag', array('tag' => 'br'))
                ->addFilter('StringTrim');
        $abstract->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $submit = new Zend_Form_Element_Button('submit');
        $submit->addDecorator('Htmltag', array('tag' => 'p'));
        $submit->setAttrib('id', 'submitbutton')
                ->setAttrib('type', 'submit')
                ->setAttrib('class', 'gt-add');
        $submit->setDecorators(array('ViewHelper', 'Description', 'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td',
                    'colspan' => '2', 'align' => 'center')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $this->addElements(array($place, $year, $host, $ablabel,$abstract, $submit));
        $this->setDecorators(array('FormElements', array(array('data' => 'HtmlTag'), array('tag' => 'table')), 'Form'));
    }

}

?>