INSERT INTO `tbl_entity_articlecategory`(`id`, `friendly_uri`, `name`, `alternate_name`, `keywords`, `status`, `parent_id`)
  (SELECT `id`, `friendly_url`, `page_title`, `title`, `keywords`, IF(`featured`="y","A","S"), `article_category_parent_id`+`category_id`) FROM `top4_domain1`.`ArticleCategory`)