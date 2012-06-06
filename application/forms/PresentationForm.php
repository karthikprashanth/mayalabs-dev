<?php

class Form_PresentationForm extends Zend_Form {

    public function __construct($options = null) {
        parent::__construct($options);

        $this->setName('attachments');

        $gtid = new Zend_Form_Element_Hidden('GTId');
        $gtid->setAttrib('value', 'TESTING');

        $Title = new Zend_Form_Element_Text('title');
        $Title->setLabel('Attachment Title')
                ->setRequired(true)
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');


        $appath = substr(APPLICATION_PATH, 0, strlen(APPLICATION_PATH) - 12);

        $content = new Zend_Form_Element_File('content');
        $content->setLabel('Upload the File')
				->setRequired(true)
                ->setDestination($appath . '/public/uploads');

        $info = new Zend_Form_Element_Hidden('info');
        $info->setLabel("(allowed formats - pdf,ppt,pptx,xls,xlsx,doc,docx,jpg,jpeg,png,gif)");

        $submit = new Zend_Form_Element_Button('submit');
        $submit->addDecorator('Htmltag', array('tag' => 'p'));
        $submit->setAttrib('id', 'submitbutton')
                ->setAttrib('class', 'gt-add')
                ->setAttrib('type', 'submit');

        $this->addElements(array($gtid, $Title, $content, $info, $submit));
    }

}

?>
