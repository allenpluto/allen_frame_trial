INSERT IGNORE INTO `tbl_entity_image`(`id`, `name`, `mime`, `source_file`)
  (SELECT image.id, CONCAT( TRIM( tbl_entity_category.name ) ,  " Gallery" ) AS name, IF(
      image.type =  'PNG',  'image/png',  'image/jpeg' ) AS mime, CONCAT(  "http://www.top4.com.au/custom/domain_1/image_files/", image.prefix,  "photo_", image.id,  ".", LOWER(
      TYPE ) ) AS source_file
   FROM tbl_entity_category
     JOIN  `tbl_rel_category_to_gallery` ON tbl_entity_category.id = tbl_rel_category_to_gallery.category_id
     JOIN tbl_rel_gallery_to_image ON tbl_rel_category_to_gallery.gallery_id = tbl_rel_gallery_to_image.gallery_id
     JOIN image ON image.id = tbl_rel_gallery_to_image.image_id)