TRUNCATE TABLE `tbl_rel_category_to_gallery`;
INSERT INTO `tbl_rel_category_to_gallery`(`category_id`, `gallery_id`, `relation`)
  (SELECT `item_id`, `gallery_id`,  '' FROM `gallery_item` JOIN tbl_entity_category ON tbl_entity_category.id = item_id WHERE `item_type` = 'listingcategory');