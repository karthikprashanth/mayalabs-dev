<?php

class Form_AttachmentForm extends Zend_Form {

    public function __construct($options = null) {
        parent::__construct($options);

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Attachment Title')
                ->setRequired(true)
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description')
                ->setRequired(true)
                ->addValidator('NotEmpty')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');


        $appath = substr(APPLICATION_PATH, 0, strlen(APPLICATION_PATH) - 12);
        $content = new Zend_Form_Element_File('content');
        $content->setLabel('Upload the Attachment')                
                ->setDestination($appath . '/public/uploads');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel("Upload");

        $this->addElements(array($title, $description, $content, $submit));
    }

}

?>
