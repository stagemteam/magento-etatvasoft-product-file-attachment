<?php
class Etatva_Files_Block_Product_View extends Mage_Catalog_Block_Product_View
{
	/** Convert file size
	 *	to readable format
	 *  @param : file size in bytes
	 * output : depending on size output will be in B,KB or MB.
	 */
    public function getModifiedFileSize($size)
	{
		$divCount=0;
		while($size>1024.00)
		{
              	$size=(float)$size/1024;
			$divCount++;
		}
		if($divCount==0){$sizeAppend=" B ";}
		else if($divCount==1){$sizeAppend=" KB ";}
		else if($divCount==2){$sizeAppend=" MB ";}
		$size=round($size, 2).$sizeAppend;
		return $size;
	}
}