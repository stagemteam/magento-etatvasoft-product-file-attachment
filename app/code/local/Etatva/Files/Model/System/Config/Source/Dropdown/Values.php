<?php
class Etatva_Files_Model_System_Config_Source_Dropdown_Values
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'file_sortorder',
                'label' => 'File sort-order(default)',
            ),
            array(
                'value' => 'file_downloads',
                'label' => 'Download count',
            ),
			array(
                'value' => 'file_size',
                'label' => 'File size',
            ),
        );
    }
}