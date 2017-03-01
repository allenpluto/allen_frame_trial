DROP TABLE IF EXISTS `tbl_entity_organization`;
CREATE TABLE `tbl_entity_organization` AS
  SELECT `tbl_entity_listing`.`id`,`tbl_entity_listing`.`friendly_url` AS friendly_uri,`tbl_entity_listing`.`name`,`tbl_entity_listing`.`alternate_name`,`tbl_entity_listing`.`description`,`tbl_entity_listing`.`image_id`,`tbl_entity_listing`.`enter_time`,`tbl_entity_listing`.`update_time`,`tbl_entity_listing`.`abn`,`tbl_entity_listing`.`account_id`,`tbl_entity_listing`.`subpremise`,`tbl_entity_listing`.`street_address`,`tbl_entity_listing`.`street_address_alt`,`tbl_entity_listing`.`place_id`,`tbl_entity_listing`.`suburb_id` AS locality,`tbl_entity_listing_place_hierarchy`.`administrative_area_level_2`,`tbl_entity_listing_place_hierarchy`.`administrative_area_level_1`,`tbl_entity_listing_place_hierarchy`.`country`,`tbl_entity_listing`.`keywords`,`tbl_entity_listing`.`logo_id`,`tbl_entity_listing`.`banner_id`,`tbl_entity_listing`.`email`,`tbl_entity_listing`.`telephone`,`tbl_entity_listing`.`alternate_telephone`,`tbl_entity_listing`.`mobile`,`tbl_entity_listing`.`fax_number`,`tbl_entity_listing`.`website_url`,`tbl_entity_listing`.`content`,`tbl_entity_listing`.`status`,`tbl_entity_listing`.`hours_work`,`tbl_entity_listing`.`facebook_link`,`tbl_entity_listing`.`twitter_link`,`tbl_entity_listing`.`linkedin_link`,`tbl_entity_listing`.`youtube_link`,`tbl_entity_listing`.`blog_link`,`tbl_entity_listing`.`pinterest_link`,`tbl_entity_listing`.`googleplus_link`,`tbl_entity_listing`.`import_error`
  FROM `tbl_entity_listing`
  LEFT JOIN `tbl_entity_listing_place_hierarchy` ON tbl_entity_listing.place_id = tbl_entity_listing_place_hierarchy.place_id
  ORDER BY `tbl_entity_listing`.`id`