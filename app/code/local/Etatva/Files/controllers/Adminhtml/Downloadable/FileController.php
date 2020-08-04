<?php
require_once 'Mage/Downloadable/controllers/Adminhtml/Downloadable/FileController.php';
   
class Etatva_Files_Adminhtml_Downloadable_FileController extends Mage_Downloadable_Adminhtml_Downloadable_FileController
{
    /**
     * Upload file controller action
     */
    public function uploadfileAction()
    {
    	$folderName=Mage::getBaseDir('media').DS.'downloadable'.DS.'files'.DS.'links';
	   	$tmpPath = $folderName;
        $result = array();
		$allowedFormats=array();
		$allowedFormats=Mage::getStoreConfig('files/files/formats');
        $allowedFormats=explode(",",$allowedFormats);

        try {
            $uploader = new Mage_Core_Model_File_Uploader('samples'); 
        	$uploader->setAllowedExtensions($allowedFormats);
		    $uploader->setAllowRenameFiles(true);
			$uploader->setAllowCreateFolders(true);
            $uploader->setFilesDispersion(true);
            $allowedFileSize=$_FILES['samples']['size'];
			if($allowedFileSize > Mage::getStoreConfig('files/files/filesize'))
			{
            	throw new Exception('File size exceeded.');
			}
			$result = $uploader->save($tmpPath);

            if (isset($result['file'])) {
                $fullPath = rtrim($tmpPath, DS) . DS . ltrim($result['file'], DS);
                Mage::helper('core/file_storage_database')->saveFile($fullPath);

            }

            $result['cookie'] = array(
                'name'     => session_name(),
                'value'    => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path'     => $this->_getSession()->getCookiePath(),
                'domain'   => $this->_getSession()->getCookieDomain()
            );
        } catch (Exception $e) {
        	$result = array('error'=>$e->getMessage(), 'errorcode'=>$e->getCode());
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/products');
    }

}
