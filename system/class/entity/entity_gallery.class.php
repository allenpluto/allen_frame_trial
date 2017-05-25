<?php
// Class Object
// Name: entity_gallery
// Description: gallery, image collection

class entity_gallery extends entity
{
    function __construct($value = Null, $parameter = array())
    {
        $default_parameter = [
            'relational_fields'=>[
                'image'=>[],
                'organization'=>[]
            ]
        ];
        $parameter = array_merge($default_parameter, $parameter);
        return parent::__construct($value, $parameter);
    }

    function sync($parameter = array())
    {
        // set default sync parameters for view table
        $sync_parameter['sync_table'] = 'tbl_view_gallery';
        $sync_parameter['update_fields'] = array(
            'id' => 'tbl_entity_gallery.id',
            'friendly_uri' => 'tbl_entity_gallery.friendly_uri',
            'name' => 'tbl_entity_gallery.name',
            'alternate_name' => 'tbl_entity_gallery.alternate_name',
            'description' => 'tbl_entity_gallery.description',
            'enter_time' => 'tbl_entity_gallery.enter_time',
            'update_time' => 'tbl_entity_gallery.update_time',
            'view_time' => '"'.date('Y-m-d H:i:s').'"',
            'account_id' => 'tbl_entity_gallery.account_id'
        );

        $sync_parameter['group'] = array(
            'tbl_entity_category.id'
        );

        $sync_parameter['fulltext_key'] = array();

        $sync_parameter = array_merge($sync_parameter, $parameter);

        $result = parent::sync($sync_parameter);

        return $result;

    }
}

?>