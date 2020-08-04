<?php
require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';

class Etatva_Files_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    /**
	 * Save product data action
	 * operation : add/edit/delete uploaded file(s) data to/from custom table 'files' for this product
	 */
	public function saveAction()
    {
    	$storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $productId      = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);

        $data = $this->getRequest()->getPost();

		if ($data) {
            if (!isset($data['product']['stock_data']['use_config_manage_stock'])) {
                $data['product']['stock_data']['use_config_manage_stock'] = 0;
            }
            $product = $this->_initProductSave();

            try {
                $product->save();
                $productId = $product->getId();

                if(count($data['downloadable']['sample'])>0 && $product->getTypeID()!='downloadable')
				{
	                foreach($data['downloadable']['sample'] as $currentFile)
					{
						$filesModel = Mage::getModel('files/files');

						$fileInfo=$this->getFileName($currentFile['file']);
                        $fileDetails=explode(",",$fileInfo);
                        $fileExt=$this->getFileExtension($fileDetails[0]);
						if($currentFile['is_delete']==null && $currentFile['sample_id']==0)
						{
							$filesModel->setProdId($productId)
							->setFileTitle($currentFile['title'])
							->setProdFile($fileDetails[0])
		                    ->setFileType($fileExt)
							->setFileSize($fileDetails[1])
		                    ->setFileSortorder($currentFile['sort_order'])
							->setFileCreation(date("Y-m-d H:i:s"))
							->setFileLastmodification(date("Y-m-d H:i:s"))
							->setFileTimestamp(time())
		                    ->save();
		                }
						else if($currentFile['is_delete']==null && $currentFile['sample_id']!=0)
						{
							$fileDetailsModificationFlag=$this->checkIfFileDetailsModified($currentFile['sample_id'],$currentFile['title'],$fileDetails[0],$currentFile['sort_order']);
                            $filesModel->load($currentFile['sample_id'])
								->setProdId($productId)
								->setFileTitle($currentFile['title'])
								->setProdFile($fileDetails[0])
			                    ->setFileType($fileExt)
								->setFileSize($fileDetails[1])
			                    ->setFileSortorder($currentFile['sort_order'])
								->save();

							if($fileDetailsModificationFlag==0)
							{
								$filesModel->load($currentFile['sample_id'])
									->setFileLastmodification(date("Y-m-d H:i:s"))
									->save();
							}
						}
						else if($currentFile['is_delete']==1 && $currentFile['sample_id']!=0)
						{
							$filesModel->setId($currentFile['sample_id'])->delete();
						}
                        else if($currentFile['is_delete']==1 && $currentFile['sample_id']==0 && $currentFile['file']!='[]')
						{
							$filesModel->setId($currentFile['sample_id'])->delete();
                            $fileData = json_decode($currentFile['file']);
                            $fileDir = Mage::getBaseDir('media').DS."product_custom_files";
                            $fileDir.= $fileData[0]->file;
                            if(file_exists($fileDir))
                            {
                                unlink($fileDir);
                            }
						}
						$filesModel->unsetData();
                		sleep(1);
				    }
                }
				if (isset($data['copy_to_stores'])) {
                    foreach ($data['copy_to_stores'] as $storeTo=>$storeFrom) {
                        $newProduct = Mage::getModel('catalog/product')
                            ->setStoreId($storeFrom)
                            ->load($productId)
                            ->setStoreId($storeTo)
                            ->save();
                    }
                }

                //Mage::getModel('catalogrule/rule')->applyAllRulesToProduct($productId);

                $this->_getSession()->addSuccess($this->__('The product has been saved.'));
            } catch (Mage_Core_Exception $e) {
            	$this->_getSession()->addError($e->getMessage())
                    ->setProductData($data);
                $redirectBack = true;
            } catch (Exception $e) {
            	Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $productId,
                '_current'=>true
            ));
        } elseif($this->getRequest()->getParam('popup')) {
        	$this->_redirect('*/*/created', array(
                '_current'   => true,
                'id'         => $productId,
                'edit'       => $isEdit
            ));
        } else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }

	/**
	 * Duplicate product data action
	 * operation : Copy and insert uploaded file(s) data to custom table 'files' for this product
	 */
	public function duplicateAction()
    {
        $product = $this->_initProduct();
        try
		{
            $newProduct = $product->duplicate();
			if($product->getTypeID()!='downloadable')
			{
               	$productFiles = Mage::getModel('files/files')->getCollection()
						->addFieldToFilter('prod_id',$product->getId());
                $filesModel = Mage::getModel('files/files');

				foreach($productFiles->getData() as $currentFile)
				{

					$filesModel->setProdId($newProduct->getId())
						->setFileTitle($currentFile['file_title'])
						->setProdFile($currentFile['prod_file'])
	                    ->setFileType($currentFile['file_type'])
						->setFileSize($currentFile['file_size'])
	                    ->setFileSortorder($currentFile['file_sortorder'])
						->setFileCreation(date("Y-m-d H:i:s"))
						->setFileLastmodification(date("Y-m-d H:i:s"))
						->setFileTimestamp(time())
	                    ->save();
					$filesModel->unsetData();
				}
            }
            $this->_getSession()->addSuccess($this->__('The product has been duplicated.'));
            $this->_redirect('*/*/edit', array('_current'=>true, 'id'=>$newProduct->getId()));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('_current'=>true));
        }
    }

	/**
	 * Get file name from string pattern
	 */
	protected function getFileName($string)
	{
		$tempString = substr($string, 2);
		$arr1=array();
		$arr1 = explode("}",$tempString);
		$arr2=array();
		$arr2=explode(",",$arr1[0]);
        $dataArray=explode(":",$arr2[0]);
		$fileName=$dataArray[1];

		$charPosition= strpos($fileName, "\"");
		$fileName=substr($fileName,$charPosition+1);

		$arr3=array();
		$arr3 = explode("\"",$fileName);
		$fileName=$arr3[0];
        $dataArray=explode(":",$arr2[2]);
		$fileSize=$dataArray[1];
        $fileInfo=$fileName.",".$fileSize;

		return $fileInfo;
	}

	/**
	 * Get file extension from file name
	 * output : File extension
	 */
	protected function getFileExtension($fileName)
	{
		$charPosition= strrpos($fileName, ".");
		$fileExt=substr($fileName,$charPosition+1);
		return $fileExt;
	}

	/**
	 * Check if this file's title,sort-order and/or file itself has been modified or not
	 * output : flag-> 0: modified , 1: not modified
	 */
	protected function checkIfFileDetailsModified($fileId,$currentFileTitle,$currentFile,$currentFileSortorder)
	{
		$fileData = Mage::getModel('files/files')->getCollection()
    		->addFieldToFilter('file_id',$fileId)
    		->addFieldToSelect('file_title')
			->addFieldToSelect('prod_file')
			->addFieldToSelect('file_sortorder');
        $savedDetails=$fileData->getData();

		if(strcmp($currentFileTitle,$savedDetails[0]['file_title'])!=0)
			return 0;
		else if(strcmp($currentFile,$savedDetails[0]['prod_file'])!=0)
			return 0;
		else if($currentFileSortorder!=$savedDetails[0]['file_sortorder'])
			return 0;

		return 1;
	}

    /**
     * Operations ::
     * 1.Delete record frmom database ,
     * 2.Physically deleting file
     */
    public function deleteRecordAction()
    {
        $fileDir = Mage::getBaseDir('media').DS."product_custom_files";

        $fileId = $this->getRequest()->getParam('file_id');
        $fileData = Mage::getModel('files/files')->getCollection()
    		->addFieldToFilter('file_id',$fileId);
        foreach($fileData as $file)
        {
            if($file!=null || $file->getData()!=null)
            {
                $fileDir.= $file->getProdFile();
                if(file_exists($fileDir))
                {
                    unlink($fileDir);
                }
                $file->delete();
            }
        }
    }

}
