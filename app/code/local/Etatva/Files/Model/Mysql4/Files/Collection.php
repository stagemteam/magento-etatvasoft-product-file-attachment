<?php

class Etatva_Files_Model_Mysql4_Files_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected $_previewFlag;


    public function _construct()
    {
        parent::_construct();
        $this->_init('files/files');
    }

    public function orderBySort(){
    	$this->getSelect()->order('sortorder');
    	return $this;
    }

    /**
     * Add Filter by store
     *
     * @param int|Mage_Core_Model_Store $store Store to be filtered
     * @return Etatva_Slider_Model_Mysql4_Slider_Collection Self
     */

    
}