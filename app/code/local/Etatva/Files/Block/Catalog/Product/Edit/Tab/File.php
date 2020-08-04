<?php
class Etatva_Files_Block_Catalog_Product_Edit_Tab_File extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/edit/files.phtml');
    }

	public function getProduct()
    {
        return Mage::registry('current_product');
    }
    public function isReadonly()
    {
        return false;
    }
    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('files')->__('Upload Files');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('files')->__('Product Files Information');
    }

    /**
     * Check if tab can be displayed
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check if tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {                                       
        $accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
	            ->setId('documentsInfo');

		if(Mage::getStoreConfig('files/files/status'))
		{
            $accordion->addItem('uploads', array(
	            'title'   => Mage::helper('files')->__('Upload File'),
	            'content' => $this->getLayout()
	                ->createBlock('files/catalog_product_edit_tab_sample')->toHtml(),
	            'open'    => true,
	        ));


        }
		else
		{
        	$accordion->addItem('nouploadmessage', array(
	            'title'   => Mage::helper('files')->__('Uploading Disabled'),
	            'content' => '<div style="margin:20px 30px;"><b>Uploading functionality is currently disabled. <u>To enable file uploading</u> go to Upload Product Files Setting in System Configuration and set "Allow Product File Uploads" field to Yes.</b></div>',
	            'open'    => true,
	        ));

		}
        $this->setChild('accordion', $accordion);

        return parent::_toHtml();
    }

}
?>