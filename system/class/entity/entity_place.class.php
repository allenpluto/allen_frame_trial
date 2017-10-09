<?php
// Class Object
// Name: entity_place
// Description: Google Place Information

class entity_place extends entity
{
    function get($parameter = array())
    {
        $result = parent::get($parameter);
        if (!empty($result))
        {
            $counter = 0;
            foreach($result as $row_index=>$row)
            {
                $this->id_group[':id_'.$counter] = $row['id'];
                $counter++;
            }
        }
    }

    function get_from_uri($value,$parameter = array())
    {
        $parameter = array(
            'bind_param' => array(':friendly_uri'=>$value),
            'where' => array('`friendly_uri` = :friendly_uri')
        );
        return $this->get($parameter);
    }

    function sync($parameter = array())
    {
        $result = array();


        // set default sync parameters for index table
        $sync_parameter['sync_table'] = 'tbl_index_place';
        $sync_parameter['update_fields'] = array(
            'id' => 'tbl_entity_place.id',
            'enter_time' => 'tbl_entity_place.enter_time',
            'update_time' => 'tbl_entity_place.update_time',
            'locality_id'=>'view_suburb.id',
            'postal_code_id'=>'view_post.id',
            'administrative_area_level_2_id'=>'view_region.id',
            'administrative_area_level_1_id'=>'view_state.id',
            'friendly_uri'=>'CONCAT(view_state.friendly_uri,"/",view_region.friendly_uri,"/",view_suburb.friendly_uri)',
            'organization_count'=>'COUNT(*)'
        );
        $sync_parameter['join'] = array(
            'JOIN (SELECT organization_id, id, friendly_uri FROM `tbl_rel_organization_to_place` JOIN tbl_entity_place ON tbl_rel_organization_to_place.place_id = tbl_entity_place.id AND types LIKE "[""locality%") view_suburb ON view_suburb.id = tbl_entity_place.id',
            'JOIN (SELECT organization_id, id, friendly_uri FROM `tbl_rel_organization_to_place` JOIN tbl_entity_place ON tbl_rel_organization_to_place.place_id = tbl_entity_place.id AND types LIKE "[""postal_code%") view_post ON view_suburb.organization_id = view_post.organization_id',
            'JOIN (SELECT organization_id, id, friendly_uri FROM `tbl_rel_organization_to_place` JOIN tbl_entity_place ON tbl_rel_organization_to_place.place_id = tbl_entity_place.id AND types LIKE "[""administrative_area_level_2%") view_region ON view_suburb.organization_id = view_region.organization_id',
            'JOIN (SELECT organization_id, id, friendly_uri FROM `tbl_rel_organization_to_place` JOIN tbl_entity_place ON tbl_rel_organization_to_place.place_id = tbl_entity_place.id AND types LIKE "[""administrative_area_level_1%") view_state ON view_suburb.organization_id = view_state.organization_id'
        );
        $sync_parameter['group'] = array(
            'view_suburb.id',
            'view_post.id',
            'view_region.id'
        );
        $sync_parameter['key'] = array(
            'key_suburb' => ['locality_id'],
            'key_post' => ['postal_code_id'],
            'key_region' => ['administrative_area_level_2_id'],
            'key_state' => ['administrative_area_level_1_id']
        );

        $sync_parameter = array_merge($sync_parameter, $parameter);

        $result[] = parent::sync($sync_parameter);


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
            'view_time' => '"'.date('Y-m-d H:i:s').'"',
            'subpremise' => 'subpremise',
            'street_number' => 'street_number',
            'route' => 'route',
            'locality' => 'locality',
            'colloquial_area' => 'colloquial_area',
            'postal_code' => 'postal_code',
            'administrative_area_level_2' => 'administrative_area_level_2',
            'administrative_area_level_1' => 'administrative_area_level_1',
            'country' => 'country',
            'location_latitude' => 'location_latitude',
            'location_longitude' => 'location_longitude',
            'viewport_northeast_latitude' => 'viewport_northeast_latitude',
            'viewport_northeast_longitude' => 'viewport_northeast_longitude',
            'viewport_southwest_latitude' => 'viewport_southwest_latitude',
            'viewport_southwest_longitude' => 'viewport_southwest_longitude',
            'formatted_address' => 'formatted_address'
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