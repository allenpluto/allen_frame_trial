INSERT INTO `tbl_entity_category`(`id`, `friendly_uri`, `name`, `alternate_name`, `description`, `keywords`, `content`, `status`, `parent_id`, `scoopit_uri`, `schema_itemtype`)
  (SELECT  `id` ,  `friendly_url` AS  'friendly_uri',  `page_title` AS 'name',  `title` AS 'alternate_name',  `seo_description` AS  'description',  `seo_keywords` AS  'keywords',  `content` , IF(  `featured` =  'y',  'A',  'S' ) AS  'status',  `category_id` AS  'parent_id',  `scoopit_url` AS  'scoopit_uri',  `schema_itemtype`
   FROM  `ListingCategory`)
ON DUPLICATE KEY UPDATE
  friendly_uri = VALUES(friendly_uri),
  name = VALUES(name),
  alternate_name = VALUES(alternate_name),
  description = VALUES(description),
  keywords = VALUES(keywords),
  content = VALUES(content),
  status = VALUES(status),
  parent_id = VALUES(parent_id),
  scoopit_uri = VALUES(scoopit_uri),
  schema_itemtype = VALUES(schema_itemtype)