<?php
class Etatva_Files_Model_Mysql4_Files extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        // Note that the slider_id refers to the key field in your database table.
        $this->_init('files/files', 'file_id');
    }
}