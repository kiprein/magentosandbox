<?php

$installer = $this;

$installer->startSetup();

$installer->run("
#Old table cleanup
DROP TABLE `t_category_magento`;
DROP TABLE `t_code`;
DROP TABLE `t_service`;
DROP TABLE `product_search_data`;
DROP TABLE `contact`;

#Reset search options for attributes
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='92';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='135';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='136';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='137';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='138';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='139';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='163';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='185';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='186';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='189';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='201';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='202';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='215';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='216';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='217';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='218';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='220';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='221';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='222';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='223';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='225';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='226';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='227';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='228';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='229';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='230';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='232';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='234';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='235';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='236';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='238';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='239';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='240';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='241';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='242';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='243';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='244';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='245';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='246';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='247';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='248';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='249';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='271';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='272';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='274';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='293';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='294';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='296';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='297';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='298';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='299';
UPDATE `catalog_eav_attribute` SET `is_searchable`='0', `is_visible_in_advanced_search`='0' WHERE `attribute_id`='300';
");

$installer->endSetup();