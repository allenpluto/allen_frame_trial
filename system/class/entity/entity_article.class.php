<?php
// Class Object
// Name: entity_article
// Description: Article module (Blog, Buyers Guides, Press Release...)

// image_id in image_object reference to source image. One source image may have zero to multiple thumbnail (cropped versions) for different scenario. Only source image may save exifData, any thumbnail can be regenerated using source image exifData and 
class entity_article extends entity
{
    function sync($parameter = array())
    {
        $sync_parameter = array();

        // set default sync parameters for index table
        //$parameter['sync_table'] = str_replace('entity','index',$this->parameter['table']);
        $sync_parameter['sync_table'] = 'tbl_view_article';
        $sync_parameter['update_fields'] = array(
            'id' => 'tbl_entity_article.id',
            'friendly_uri' => 'tbl_entity_article.friendly_uri',
            'name' => 'tbl_entity_article.name',
            'alternate_name' => 'tbl_entity_article.alternate_name',
            'description' => 'tbl_entity_article.description',
            'enter_time' => 'tbl_entity_article.enter_time',
            'update_time' => 'tbl_entity_article.update_time',
            'view_time' => '"'.date('Y-m-d H:i:s').'"',
            'page_title' => 'tbl_entity_article.page_title',
            'page_content' => 'tbl_entity_article.page_content',
            'extra_field' => 'tbl_entity_article.extra_field'
        );

        $sync_parameter = array_merge($sync_parameter, $parameter);

        if (!isset($sync_parameter['sync_type']))
        {
            $sync_parameter['sync_type'] = 'differential_sync';
        }
        return parent::sync($sync_parameter);
    }
}