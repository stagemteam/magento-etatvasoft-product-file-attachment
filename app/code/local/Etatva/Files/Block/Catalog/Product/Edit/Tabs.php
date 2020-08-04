<?php
class Etatva_Files_Block_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
	public function __construct()
    {
        parent::__construct();
	}
	/**
	 * Add tab under Product Information section
	 * Tab will not be added of product type is 'Downloadable'
     * Tab name : 'Product Files'
	 */
    protected function _prepareLayout()
	{
		parent::_prepareLayout();
		if(Mage::registry('current_product')->getTypeID()!='downloadable')
		{
            $product = $this->getProduct();
	        if (!($setId = $product->getAttributeSetId())) {
	            $setId = $this->getRequest()->getParam('set', null);
	        }
	        if ($setId) {
                $this->addTab('files', array(
		                    'label'     => Mage::helper('files')->__('Product Files'),
		                    'content'   => $this->getLayout()
		                        ->createBlock('files/catalog_product_edit_tab_file')->_toHtml(),
		                ));
			}
		}
	}
}