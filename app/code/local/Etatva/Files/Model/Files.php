<?php
class Etatva_Files_Model_Files extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('files/files');
    }

    public function getProductFilesData($productId)
    {
    	$collection = Mage::getModel('files/files')->getCollection()
			->addFieldToFilter('prod_id',$productId);

		return $collection;
    }

	/**
     * Get file collection for Frontend in sorted manner
	 */
	public function getFileCollection($productId)
    {
        $sortBy=Mage::getStoreConfig('files/files/linkssortby');
        $sortOrder='DESC';
		if($sortBy=='file_sortorder')
			$sortOrder='ASC';
		$collection = Mage::getModel('files/files')->getCollection()
			->addFieldToFilter('prod_id',$productId)
			->setOrder($sortBy, $sortOrder);

        return $collection;
    }
}