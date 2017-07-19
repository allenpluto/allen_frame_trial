TRUNCATE TABLE `tbl_rel_gallery_to_organization`;
INSERT INTO `tbl_rel_gallery_to_organization`(`gallery_id`, `organization_id`, `relation`)
  (SELECT `gallery_id`, `item_id`, '' FROM `gallery_item` JOIN tbl_entity_organization ON tbl_entity_organization.id = item_id WHERE `item_type` = 'listing');