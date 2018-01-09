<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 7/06/2016
 * Time: 1:45 PM
 */
define('PATH_SITE_BASE', dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR);
include('../../system/config/config.php');
$timestamp = microtime(true);
$ajax_result = [
    'content'=>[],
    'success'=>true,
    'error_message'=>[]
];
$limit = !empty($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;

function convert_google_place($value)
{
    if (!isset($value['place_id']))
    {
        $GLOBALS['global_message']->error = 'place_id is mandatory for google_place object';
        return false;
    }
    $result = ['id'=>$value['place_id']];
    unset($value['place_id']);
    unset($value['id']);

    //$flatten_fields = ['formatted_address','formatted_phone_number','international_phone_number','name','opening_hours','permanently_closed','photos','place_id','rating','reviews','types','utc_offset','vicinity','website'];
    $place_type = explode(',',$value['types'])[0];
    $address_component_field = ['subpremise','street_number','route','sublocality','locality','colloquial_area','postal_code','administrative_area_level_2','administrative_area_level_1','country'];

    foreach ($address_component_field as $address_component_field_name_index=>$address_component_field_name)
    {
        if (!empty($value[$address_component_field_name]))
        {
            $result[$address_component_field_name] = $value[$address_component_field_name];
        }
    }
    if (isset($value[$place_type.'_short']))
    {
        $result['alternate_name'] = $value[$place_type.'_short'];
    }
    if (isset($result['street_number']))
    {
        // If street_number is provided, add street_number to route
        $result['alternate_name'] = $value['street_number'].' '.$value['route'];
    }
    if (isset($result['subpremise']))
    {
        // If street_number is provided, add street_number to route
        $result['alternate_name'] = $value['subpremise'].'/'.$result['alternate_name'];
    }

    if (isset($value['types']))
    {
        $result['types'] = json_encode(explode(',',$value['types']));
    }

    $name_translate = [
        'geometry_location_lat'=>'location_latitude',
        'geometry_location_lng'=>'location_longitude',
        'geometry_viewport_northeast_lat'=>'viewport_northeast_latitude',
        'geometry_viewport_northeast_lng'=>'viewport_northeast_longitude',
        'geometry_viewport_southwest_lat'=>'viewport_southwest_latitude',
        'geometry_viewport_southwest_lng'=>'viewport_southwest_longitude',
        'geometry_bounds_northeast_lat'=>'bounds_northeast_latitude',
        'geometry_bounds_northeast_lng'=>'bounds_northeast_longitude',
        'geometry_bounds_southwest_lat'=>'bounds_southwest_latitude',
        'geometry_bounds_southwest_lng'=>'bounds_southwest_longitude',
        'icon'=>'icon',
        'formatted_address'=>'formatted_address',
        'formatted_phone_number'=>'formatted_phone_number',
        'international_phone_number'=>'international_phone_number',
        'utc_offset'=>'utc_offset',
        'opening_hours'=>'opening_hours',
        'permanently_closed'=>'permanently_closed',
        'photos'=>'photos',
        'rating'=>'rating',
        'reviews'=>'reviews',
        'url'=>'url',
        'website'=>'website',
    ];

    foreach ($name_translate as $old_field_name=>$new_field_name)
    {
        if (!empty($value[$old_field_name]))
        {
            $result[$new_field_name] = $value[$old_field_name];
        }
    }

    if (!isset($result['name']) AND isset($value[$place_type]))
    {
        $result['name'] = $value[$place_type];
        if (isset($result['street_number']))
        {
            // If street_number is provided, add street_number to route
            $result['name'] = $result['street_number'].' '.$result['name'];
        }
    }
    if (!isset($result['name']))
    {
        $result['name'] = $result['alternate_name'];
//        echo '<pre>';
//        print_r($value);print_r($result);exit;
    }
    $format = format::get_obj();
    switch($place_type)
    {
        case 'administrative_area_level_1':
        case 'administrative_area_level_2':
            $result['friendly_uri'] = $format->file_name( $result['alternate_name']);
            break;
        default:
            $result['friendly_uri'] = $format->file_name( $result['name']);
    }

    return $result;
}

$entity_organization_obj = new entity_organization();
$result = $entity_organization_obj->get(['where'=>'tbl_entity_organization.import_error < 1 AND id NOT IN (SELECT DISTINCT organization_id FROM tbl_rel_organization_to_place)','limit'=>$limit]);
$rel_row = [];
$organization_place_merged = [];
foreach($result as $result_row_index => $result_row)
{
    if (!empty($result_row['place_id']))
    {
        $organization_place_merged[] = $result_row['place_id'];
    }
    $entity_hierarchy_obj = new entity(null,['table'=>'tbl_entity_listing_place_hierarchy']);
    $entity_hierarchy_result = $entity_hierarchy_obj->get(['where'=>'place_id = "'.$result_row['place_id'].'"']);

    if (!empty($entity_hierarchy_result))
    {
        foreach ($entity_hierarchy_result as $hierarchy_row_index=>$hierarchy_row)
        {
            $i = 0;
            while ($i < 12 AND $hierarchy_row['locality'] != $hierarchy_row['additional_'.$i])
            {
                $i++;
            }
            $organization_place = [];
            while ($i < 12)
            {
                if (!empty($hierarchy_row['additional_'.$i]))
                {
                    $organization_place[] = $hierarchy_row['additional_'.$i];
                }
                $i++;
                if ($hierarchy_row['additional_'.$i] == 'ChIJ38WHZwf9KysRUhNblaFnglM')
                {
                    $i = 12;
                }
            }
        }
        $ajax_result['updated_data'][] = print_r('Listing '.$result_row['id'].' relation inserted'.PHP_EOL,true);
    }
    else
    {
        $ajax_result['updated_data'][] = print_r('Listing '.$result_row['id'].' Place id '.$result_row['place_id'].' not found in hierarchy table'.PHP_EOL,true);
        $entity_organization_obj_err = new entity_organization($result_row['id']);
        $entity_organization_obj_err->update(['import_error'=>2]);
    }

    if (!empty($organization_place))
    {
        $rel_row[] = ['id'=>$result_row['id'],'place'=>$organization_place];
        $organization_place_merged = array_merge($organization_place_merged,$organization_place);
    }
}
if (!empty($organization_place_merged))
{
    $organization_place_merged = array_unique($organization_place_merged);
    $entity_old_place_obj = new entity(null,['table'=>'tbl_entity_listing_place']);
    $entity_place_obj = new entity_place();
    $entity_place_obj->get(['where'=>'id IN ("'.implode('","',$organization_place_merged).'")']);
    if (!empty($entity_place_obj->id_group))
    {
        $organization_place_merged = array_diff($organization_place_merged,$entity_place_obj->id_group);
    }
    $old_place_row = $entity_old_place_obj->get(['where'=>'place_id IN ("'.implode('","',$organization_place_merged).'")']);
    $new_place_row = [];
    foreach($old_place_row as $place_row_index=>$place_row)
    {
        $new_place_row[] = convert_google_place($place_row);
    }
    $entity_place_obj = new entity_place();
    $entity_place_obj->set(['row'=>$new_place_row]);
}

$entity_organization_obj->set(['row'=>$rel_row,'fields'=>['id','place']]);


$ajax_result['updated_data'][] = print_r($global_message->display(),true);
$ajax_result['execution_time'] = round(microtime(true) - $timestamp, 2);
if (!empty($_REQUEST['start_time'])) $ajax_result['start_time'] = $_REQUEST['start_time'];
print_r(json_encode($ajax_result));