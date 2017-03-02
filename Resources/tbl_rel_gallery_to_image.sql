TRUNCATE TABLE `tbl_rel_gallery_to_image`;
INSERT INTO `tbl_rel_gallery_to_image`(`gallery_id`, `image_id`, `relation`)
  (SELECT gallery_image.`gallery_id`, gallery_image.`image_id`, IF(gallery_image.`image_default` = 'y','thumb','') FROM `gallery_image` JOIN tbl_entity_image ON gallery_image.image_id = tbl_entity_image.id);