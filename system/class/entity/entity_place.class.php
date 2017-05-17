<?php
// Class Object
// Name: entity_place
// Description: Google Place Information

class entity_place extends entity
{
    function sync($parameter = array())
    {
        // set default sync parameters for view table
        $sync_parameter['sync_table'] = 'tbl_view_place';
        $sync_parameter['update_fields'] = array(
            'id' => 'tbl_entity_place.id',
            'friendly_uri' => 'tbl_entity_place.friendly_uri',
            'name' => 'tbl_entity_place.name',
            'alternate_name' => 'tbl_entity_place.alternate_name',
            'description' => 'tbl_entity_place.description',
            'enter_time' => 'tbl_entity_place.enter_time',
            'update_time' => 'tbl_entity_place.update_time',
            'view_time' => '"'.date('Y-m-d H:i:s').'"'
        );

        $sync_parameter['group'] = array(
            'tbl_entity_place.id'
        );

        $sync_parameter['fulltext_key'] = array();

        $sync_parameter = array_merge($sync_parameter, $parameter);

        $result[] = parent::sync($sync_parameter);

        return $result;

    }
}