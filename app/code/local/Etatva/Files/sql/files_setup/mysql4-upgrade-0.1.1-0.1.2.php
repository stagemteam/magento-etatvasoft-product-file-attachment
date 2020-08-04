<?php
	$installer = $this;
	$installer->startSetup();

	$installer->run("
	ALTER TABLE {$this->getTable('files')}
	    ADD COLUMN file_timestamp BIGINT(10)
	");

	$installer->endSetup();