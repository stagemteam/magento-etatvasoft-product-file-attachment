<?php
	$installer = $this;
	$installer->startSetup();

	$installer->run("
		DROP TABLE IF EXISTS {$this->getTable('files')};

		CREATE TABLE {$this->getTable('files')} (
		file_id int(11) NOT NULL AUTO_INCREMENT,
		prod_id int(10) UNSIGNED NOT NULL,
		file_title text NOT NULL ,
		prod_file varchar(255) NOT NULL,
		file_type varchar(10) NOT NULL ,
		file_size int(15) NOT NULL ,
		file_sortorder int(5) NOT NULL,
		PRIMARY KEY (file_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

		ALTER TABLE {$this->getTable('files')}
		ADD
		CONSTRAINT `FK_FILES_PROD_ID_CAT_PRD_ENTT_ENTT_ID` FOREIGN KEY (`prod_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

    ");

	$installer->endSetup();
?>