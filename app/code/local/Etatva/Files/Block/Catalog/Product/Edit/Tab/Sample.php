<?php
class Etatva_Files_Block_Catalog_Product_Edit_Tab_Sample extends Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples
{
	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/edit/files/sample.phtml');
    }

	public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Check block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
         return false;
    }


    /**
     * Retrieve Add Button HTML
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        $addButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('files')->__('Add New File'),
                'id' => 'add_sample_item',
                'class' => 'add',
            ));
        return $addButton->toHtml();
    }

    /**
     * Retrieve samples array
     *
     * @return array
     */
    public function getSampleData()
    {
        $samplesArr = array();
        $model= Mage::getModel('files/files')->getProductFilesData($this->getProduct()->getId());

		foreach ($model->getData() as $item=>$value)
		{
            $tmpSampleItem = array(
                'sample_id' => $value['file_id'],
                'title' => $value['file_title'],
                'sample_url' => 'sample_url',
                'sample_type' => 'file',
                'sort_order' => $value['file_sortorder'],
            );
			$path=Mage::getBaseDir('media').DS.'downloadable'.DS.'files'.DS.'links';
            $file = Mage::helper('downloadable/file')->getFilePath(
                $path, $value['prod_file']
            );
		    if ($value['prod_file'] && !is_file($file)) {
                Mage::helper('core/file_storage_database')->saveFileToFilesystem($file);
            }
            if ($value['prod_file'] && is_file($file)) {
                $tmpSampleItem['file_save'] = array(
                    array(
                        'file' => $value['prod_file'],
                        'name' => Mage::helper('downloadable/file')->getFileFromPathFile($value['prod_file']),
                        'size' => filesize($file),
                        'status' => 'old'
                    ));
            }
            if ($this->getProduct() && $value['file_title']) {
                $tmpSampleItem['store_title'] = $value['file_title'];
            }
            $samplesArr[] = new Varien_Object($tmpSampleItem);
        }

        return $samplesArr;
    }

    /**
     * Check exists defined samples title
     *
     * @return bool
     */
    public function getUsedDefault()
    {
        return $this->getProduct()->getAttributeDefaultValue('samples_title') === false;
    }

    /**
     * Retrieve Default samples title
     *
     * @return string
     */
    public function getSamplesTitle()
    {
        return Mage::getStoreConfig(Mage_Downloadable_Model_Sample::XML_PATH_SAMPLES_TITLE);
    }

    /**
     * Prepare layout
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setChild(
            'upload_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData(array(
                    'id'      => '',
                    'label'   => Mage::helper('adminhtml')->__('Upload Files'),
                    'type'    => 'button',
                    'onclick' => 'Downloadable.massUploadByType(\'samples\')'
                ))
        );
        $this->_addElementIdsMapping(array(
            'container' => $this->getHtmlId() . '-new',
            'delete'    => $this->getHtmlId() . '-delete'
        ));
    }

    /**
     * Retrieve Upload button HTML
     *
     * @return string
     */
    public function getUploadButtonHtml()
    {
        return $this->getChild('upload_button')->toHtml();
    }

    /**
     * Retrive config json
     *
     * @return string
     */
  public function getConfigJson()
    {
        $this->getUploaderConfig()
            ->setFileParameterName('samples')
            ->setTarget(
                Mage::getModel('adminhtml/url')
                    ->addSessionParam()
                    ->getUrl('*/downloadable_file/uploadfile')
            );

         $this->getUploaderConfig()->setParams(array('form_key' => $this->getFormKey()));
        $this->getUploaderConfig()->setFileField('samples');
        $this->getUploaderConfig()->setFilters(array(
            'all'    => array(
                'label' => Mage::helper('adminhtml')->__('All Files'),
                'files' => array('*.*')
            )
        ));
         $this->getMiscConfig()
            ->setReplaceBrowseWithRemove(true)
        ;
        return Mage::helper('core')->jsonEncode(parent::getJsonConfig());
    }

    /**
     * Retrive config object
     *
     * @return Varien_Config
     */

    public function getBrowseButtonHtml()
    {
        return $this->getChild('browse_button')
            // Workaround for IE9
            ->setBeforeHtml('<div style="display:inline-block; " id="downloadable_sample_{{id}}_file-browse">')
            ->setAfterHtml('</div>')
            ->setId('downloadable_sample_{{id}}_file-browse_button')
            ->toHtml();
    }

     public function getDeleteButtonHtml()
    {
        return $this->getChild('delete_button')
            ->setLabel('')
            ->setId('downloadable_sample_{{id}}_file-delete')
            ->setStyle('display:none; width:31px;')
            ->toHtml();
    }

    public function getConfig()
    {
        return $this;
    }
}
?>