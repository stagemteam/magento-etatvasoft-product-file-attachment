<?php
class Etatva_Files_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Function to handle request to download file for Files module
	 * @param : file_timestamp
	 * operation : increase file download count by 1
	 */
    public function downloadAction()
	{
    	$sampleId = $this->getRequest()->getParam('id', 0);
		$sample = Mage::getModel('files/files')->load($sampleId, 'file_timestamp');
		if ($sample->getId())
		{
            $resource = Mage::getBaseDir('media').DS.'downloadable'.DS.'files'.DS.'links'. $sample->getProdFile();
             $resourceType = 'file';
            try {
                $this->_processDownload($sample,$resource, $resourceType);
				$downloadCount=$sample->getFileDownloads();
                Mage::getModel('files/files')->load($sample->getId())
								->setFileDownloads(++$downloadCount)
								->save();
                exit(0);
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError(Mage::helper('files')->__('An error occurred while getting requested content. Please contact the store owner.'));
            }
        }
	}
	protected function _processDownload($sample,$resource, $resourceType)
    {
        $contentDisposition='inline';
        $fileName=$sample->getProdFile();
        $charPosition= strrpos($fileName, "/");
		$fileName=substr($fileName,$charPosition+1);
        if (function_exists('mime_content_type')) {
                $contentType = mime_content_type(Mage::getBaseDir('media'). 'downloadable'  . 'files' . 'links' .  $sample->getProdFile());
            } else {
                $contentType=$sample->getFileType();
            }
		$this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', $sample->getFileSize())
            ->setHeader('Content-Disposition', $contentDisposition . '; filename='.$fileName);

        $this->getResponse()
            ->clearBody();
        $this->getResponse()
            ->sendHeaders();

		$handle = new Varien_Io_File();
        $handle->open(array('path'=>Mage::getBaseDir('var')));
        if (!$handle->fileExists($resource, true)) {
            Mage::throwException(Mage::helper('downloadable')->__('The file does not exist.'));
        }
        $handle->streamOpen($resource, 'r');

        while ($buffer = $handle->streamRead()) {
            print $buffer;
        }
    }
}
