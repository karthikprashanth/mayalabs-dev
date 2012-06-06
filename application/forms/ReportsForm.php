<?php

class Form_ReportsForm extends Zend_Form {

    private $checkArray;
    private $gtid;
    private $type;

    public function setVars($id, $type) {
        $this->type = $type;

        if (in_array($type, array("finding", "upgrade", "lte"))) {
            $this->gtid = $id;
            $gtdatamodel = new Model_DbTable_Gtdata();
            $gtdata = $gtdatamodel->getDataByType($id, $type);
            $i = 0;
            foreach ($gtdata as $list) {
                $this->checkArray[$type][$i] = $list['id'];
                $i++;
            }
        } else if ($type == "gtdataview") {
            $gtdatamodel = new Model_DbTable_Gtdata();
            $gtdata = $gtdatamodel->getData($id);
            $datatype = $gtdata['type'];
            $this->gtid = $gtdata['gtid'];
            $this->checkArray[$datatype] = array($id);
        } else if ($type == "gtreport") {
            $this->gtid = $id;
            $gtdatamodel = new Model_DbTable_Gtdata();
            $gtdata = $gtdatamodel->getDataByGt($id);
            $i = 0;
            foreach ($gtdata as $list) {
                $this->checkArray[$list['type']][$i] = $list['id'];
                $i++;
            }
        }
    }

    public function showForm() {
        $gtid = $this->gtid;
        $pmodel = new Model_DbTable_Plant();
        $gtmodel = new Model_DbTable_Gasturbine();
        $gtdatamodel = new Model_DbTable_Gtdata();

        $gt = $gtmodel->getGT($gtid);
        $plant = $pmodel->getPlant($gt['plantId']);
        $gtdata = $gtdatamodel->getDataByGt($gtid);

        $gtDetails = new Zend_Form_Element_Checkbox('gtdet');
        $gtDetails->setLabel('Include GT Details');
        $gtDetails->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        if ($this->type == 'gtreport') {
            $gtDetails->setValue(1);
        }

        $findingOptions = new Zend_Form_Element_MultiCheckbox('findings');
        $findingOptions->setLabel('Select Findings');
        $findingOptions->setAttrib('value', 'yes');
        $findingOptions->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $upgradeOptions = new Zend_Form_Element_MultiCheckbox('upgrades');
        $upgradeOptions->setLabel('Select Upgrades');
        $upgradeOptions->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $lteOptions = new Zend_Form_Element_MultiCheckbox('ltes');
        $lteOptions->setLabel('Select LTEs');
        $lteOptions->setDecorators(array('ViewHelper', array('Description', array('tag' => '', 'escape' => false)),
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $f = 0;
        $u = 0;
        $l = 0;
        foreach ($gtdata as $list) {
            if ($list['type'] == 'finding') {
                $findingOptions->addMultiOption($list['id'], $list['title']);
                $f++;
            } else if ($list['type'] == 'upgrade') {
                $upgradeOptions->addMultiOption($list['id'], $list['title']);
                $u++;
            } else if ($list['type'] == 'lte') {
                $lteOptions->addMultiOption($list['id'], $list['title']);
                $l++;
            }
        }
        $findingOptions->setValue($this->checkArray['finding']);
        $upgradeOptions->setValue($this->checkArray['upgrade']);
        $lteOptions->setValue($this->checkArray['lte']);

        $elements = array();

        array_push($elements, $gtDetails);

        if ($f > 0) {
            array_push($elements, $findingOptions);
        }
        if ($u > 0) {
            array_push($elements, $upgradeOptions);
        }
        if ($l > 0) {
            array_push($elements, $lteOptions);
        }

        array_push($elements, $fValues);

        $submit = new Zend_Form_Element_Submit('Generate');
        $submit->addDecorator('Htmltag', array('tag' => 'p'));
        $submit->setAttrib('class', 'gt-report');
        $submit->setAttrib('id', 'submitbutton');
        $submit->setDecorators(array('ViewHelper', 'Description', 'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td',
                    'colspan' => '2', 'align' => 'center')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        array_push($elements, $submit);
        $this->addElements($elements);
        $this->setDecorators(array('FormElements', array(array('data' => 'HtmlTag'), array('tag' => 'table')), 'Form'));
    }

}