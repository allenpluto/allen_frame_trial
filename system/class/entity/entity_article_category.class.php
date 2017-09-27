<?php
// Class Object
// Name: entity_article_category
// Description: article(buyer's guide) category

class entity_article_category extends entity
{
    function sync($parameter = array())
    {
        // set default sync parameters for index table
        $sync_parameter['sync_table'] = 'tbl_index_article_category';
        $sync_parameter['update_fields'] = array(
            'id' => 'tbl_entity_article_category.id',
            'name' => 'tbl_entity_article_category.name',
            'enter_time' => 'tbl_entity_article_category.enter_time',
            'update_time' => 'tbl_entity_article_category.update_time',
            'keywords' => 'tbl_entity_article_category.keywords',
            'status' => 'tbl_entity_article_category.status',
            'parent' => 'tbl_entity_article_category.parent_id',
            'sibling' => 'GROUP_CONCAT(DISTINCT tbl_sibling.id)',
            'child' => 'GROUP_CONCAT(DISTINCT tbl_child.id)'
        );


        $sync_parameter['join'] = array(
            'LEFT JOIN tbl_entity_article_category tbl_sibling ON tbl_entity_article_category.parent_id = tbl_sibling.parent_id',
            'LEFT JOIN tbl_entity_article_category tbl_child ON tbl_entity_article_category.id = tbl_child.parent_id'
        );

        $sync_parameter['where'] = array(
            'tbl_entity_article_category.status = "A"'
        );

        $sync_parameter['group'] = array(
            'tbl_entity_article_category.id'
        );

        $sync_parameter['fulltext_key'] = array();

        $sync_parameter = array_merge($sync_parameter, $parameter);

        $result[] = parent::sync($sync_parameter);

        // set default sync parameters for view table
        $sync_parameter['sync_table'] = 'tbl_view_article_category';
        $sync_parameter['update_fields'] = array(
            'id' => 'tbl_entity_article_category.id',
            'friendly_uri' => 'tbl_entity_article_category.friendly_uri',
            'name' => 'tbl_entity_article_category.name',
            'alternate_name' => 'tbl_entity_article_category.alternate_name',
            'description' => 'tbl_entity_article_category.description',
            'enter_time' => 'tbl_entity_article_category.enter_time',
            'update_time' => 'tbl_entity_article_category.update_time',
            'view_time' => '"'.date('Y-m-d H:i:s').'"',
            'keywords' => 'tbl_entity_article_category.keywords',
            'content' => 'tbl_entity_article_category.content',
            'status' => 'tbl_entity_article_category.status',
            'parent_id' => 'tbl_entity_article_category.parent_id'
        );

        $sync_parameter['where'] = array(
            'tbl_entity_article_category.status = "A"'
        );

        $sync_parameter['group'] = array(
            'tbl_entity_article_category.id'
        );

        $sync_parameter['fulltext_key'] = array();

        $sync_parameter = array_merge($sync_parameter, $parameter);

        $result[] = parent::sync($sync_parameter);

        return $result;

    }
}

?>