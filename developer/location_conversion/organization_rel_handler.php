<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 7/06/2016
 * Time: 1:45 PM
 */
define('PATH_SITE_BASE', dirname(__DIR__).DIRECTORY_SEPARATOR);
include('../../system/config/config.php');
$timestamp = microtime(true);
$ajax_result = [
    'updated_data'=>[],
    'success'=>true,
    'error_message'=>[]
];
$limit = !empty($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;

$entity_organization = new entity_organization();
$result = $entity_organization->get(['where'=>'tbl_entity_organization.place_id != "" AND tbl_entity_organization.import_error < 3 AND tbl_entity_organization.id NOT IN (SELECT DISTINCT organization_id FROM tbl_rel_organization_to_place)','limit'=>$limit]);
if (empty($result))
{
    $ajax_result['success'] = false;
    $ajax_result['error_message'] = 'Fail to get organizations from database';
}
else
{
    foreach($result as $result_row_index => $result_row)
    {
        $entity_place = new entity_place($result_row['place_id']);
        $place_row = end($entity_place->row);

        if (empty($place_row))
        {
            $content_row['status'] = 'ZERO_RESULTS';
            $content_row['message'] = 'Place id does not exist in tbl_entity_place';
            $ajax_result['updated_data'][] = $content_row;
            continue;
        }
        $request = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$place_row['location_latitude'].','.$place_row['location_longitude'].'&key='.$global_preference->google_api_credential_server;
        $response = file_get_contents($request);
        if (empty($response))
        {
            $content_row['status'] = 'REQUEST_DENIED';
            $content_row['message'] = 'Fail to get place info from Google, No Response';
            $ajax_result['updated_data'][] = $content_row;
            continue;
        }
        $response = json_decode($response,true);
        if ($response['status'] != 'OK')
        {
            $content_row['status'] = $response['status'];
            $content_row['message'] = 'Fail to get reverse geocoding results from Google. '.$response['error_message'];
            $ajax_result['updated_data'][] = $content_row;
            continue;
        }
        if (empty($response['results']))
        {
            $content_row['status'] = 'ZERO_RESULTS';
            $content_row['message'] = 'Fail to get reverse geocoding results from Google. Given Location returns empty result';
            $ajax_result['updated_data'][] = $content_row;
            continue;
        }
        $region_types = ['locality','sublocality','postal_code','country','administrative_area_level_1','administrative_area_level_2'];
        $organization_place = [];
        foreach($response['results'] as $response_result_row_index => $response_result_row)
        {
            $type = array_intersect($response_result_row['types'], $region_types);
            if (!empty($type))
            {
                // If the result_row is a region type, store the row into tbl_entity_place and relation into tbl_rel_organization_to_place
                $organization_region_google_place = $format->flatten_google_place($response_result_row);
                $organization_place[] = $organization_region_google_place['id'];
                $entity_place->row[] = $organization_region_google_place;
            }
        }
        if (count($organization_place) == 0)
        {
            $content_row['status'] = 'ZERO_RESULTS';
            $content_row['message'] = 'Google reverse geocoding did not return any region related address components. Organization '.$result_row['id'].': '.$place_row['id'];
            $ajax_result['updated_data'][] = $content_row;
            $entity_organization_obj_err = new entity_organization($result_row['id']);
            $entity_organization_obj_err->update(['import_error'=>3]);
            continue;
        }

        $entity_organization_obj = new entity_organization();
        $entity_organization_obj->set(['row'=>[['id'=>$result_row['id'],'place'=>$organization_place]],'fields'=>['id','place']]);
        $entity_place->set();
        $content_row = [
            'status'=>'OK',
            'message'=>'Organization '.end($entity_organization_obj->id_group).' Place Set'
        ];
        $ajax_result['updated_data'][] = $content_row;
    }
}

$ajax_result['execution_time'] = round(microtime(true) - $timestamp, 2);
if (!empty($_REQUEST['start_time'])) $ajax_result['start_time'] = $_REQUEST['start_time'];
print_r(json_encode($ajax_result));