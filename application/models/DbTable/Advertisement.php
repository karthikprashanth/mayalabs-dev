<?php

class Model_DbTable_Advertisement extends Zend_Db_Table_Abstract {

    protected $_name = 'advertisements';

    public function getAdvertisement($advertid) {
        $advertid = (int) $advertid;
        $row = $this->fetchRow('advertId = ' . $advertid);
        if (!$row) {
            throw new Exception("Could not find row $advertid");
        }
        return $row->toArray();
    }

    public function add($content) {

        $this->insert($content);
    }

    public function deleteAdvertisement($id) {
        $this->delete('advertId =' . (int) $id);
    }

    public function updateAdvertisement($content) {
        $where = $this->getAdapter()->quoteInto('advertId = ?', $content['advertId']);
        $this->update($content, $where);
    }

    public function listadvertisement() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $selectAdverts = new Zend_Db_Select($db);
        $selectAdverts->from('advertisements');

        return $selectAdverts;
    }

    public function randomAd() {
        $row = $this->fetchRow(null, new Zend_Db_Expr('RAND()'));
        if (!$row) {
            throw new Exception("Could not find row $advertid");
        }
        return $row->toArray();
    }

    public function timeStamp() {
        $time = date('Y-m-d H:i:s');
        return $time;
    }

    public function listAds() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $selectAds = new Zend_Db_Select($db);
        $selectAds->from('advertisements');
        return $selectAds;
    }

}