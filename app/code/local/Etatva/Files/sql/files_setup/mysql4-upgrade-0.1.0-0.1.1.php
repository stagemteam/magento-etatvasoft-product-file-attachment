<?php
	$installer = $this;
	$installer->startSetup();

	$installer->run("
	ALTER TABLE {$this->getTable('files')}
	    ADD COLUMN file_downloads INT(10) NOT NULL ,
	    ADD COLUMN file_creation DATETIME,
	    ADD COLUMN file_lastmodification DATETIME
    ");

	$installer->endSetup();
?>