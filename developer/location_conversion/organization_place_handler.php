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
$result = $entity_organization->get(['where'=>'tbl_entity_organization.place_id != "" AND tbl_entity_organization.import_error = 5 AND tbl_entity_organization.id NOT IN (SELECT DISTINCT organization_id FROM tbl_rel_organization_to_place)','limit'=>$limit]);
if (empty($result))
{
    $ajax_result['success'] = false;
    $ajax_result['error_message'] = 'Fail to get organizations from database';
}
else
{
    foreach($result as $result_row_index => $result_row)
    {
        $content_row = ['status'=>'OK'];
        $entity_place = new entity_place();
        $entity_place_source = new entity_place($result_row['place_id']);
        if (count($entity_place_source->id_group) > 0)
        {
            $organization_google_place = end($entity_place_source->row);
        }
        else
        {
            $request = 'https://maps.googleapis.com/maps/api/place/details/json?placeid='.$result_row['place_id'].'&key='.$global_preference->google_api_credential_server;
            $response = file_get_contents($request);
            if (empty($response))
            {
                $content_row['status'] = 'REQUEST_DENIED';
                $content_row['message'] = 'Fail to get place info from Google, No Response';
                $ajax_result['updated_data'][] = $content_row;
                continue;
            }
            $response = json_decode($response,true);
            if (!is_array($response))
            {
                $content_row['status'] = 'REQUEST_DENIED';
                $content_row['message'] = 'Fail to get place info from Google, Illegal Format Response';
                $ajax_result['updated_data'][] = $content_row;
                continue;
            }
            if ($response['status'] != 'OK')
            {
                $content_row['status'] = $response['status'];
                $content_row['message'] = 'Fail to get place info from Google. Organization '.$result_row['id'];
                if (!empty($response['error_message'])) $content_row['message'] .= ' - '.$response['error_message'];
            }
            if (empty($response['result']))
            {
                $content_row['status'] = 'ZERO_RESULTS';
                $content_row['message'] = 'Fail to get place info from Google. Given Place ID returns empty result';
            }
            if ($content_row['status'] != 'OK')
            {
                $ajax_result['updated_data'][] = $content_row;
                $entity_organization_obj = new entity_organization();
                $entity_organization_obj->set(['row'=>[['id'=>$result_row['id'],'place_id'=>'']],'fields'=>['id','place_id']]);
                continue;
            }
            $organization_google_place = $format->flatten_google_place($response['result']);
            if ($organization_google_place['country'] != 'Australia')
            {
                $content_row['status'] = 'REQUEST_DENIED';
                $content_row['message'] = 'Place is not inside Australia';
                $ajax_result['updated_data'][] = $content_row;
                $entity_organization_obj = new entity_organization();
                $entity_organization_obj->set(['row'=>[['id'=>$result_row['id'],'place_id'=>'']],'fields'=>['id','place_id']]);
                continue;
            }
            $entity_place->row[] = $organization_google_place;
            if ($result_row['place_id'] != $organization_google_place['id'])
            {
                $entity_organization_obj = new entity_organization();
                $entity_organization_obj->set(['row'=>[['id'=>$result_row['id'],'place_id'=>$organization_google_place['id']]],'fields'=>['id','place_id']]);
            }
        }

        $region_types = ['sublocality','locality','postal_code','administrative_area_level_2','administrative_area_level_1','country'];
        $reverse_geo_coding_required = false;
        $organization_place = [];
        foreach($region_types as $region_type_index=>$region_type)
        {
            if (!empty($organization_google_place[$region_type]))
            {
                $entity_place_region = new entity_place();
                $entity_place_region->get(['where'=>['name = "'.$organization_google_place[$region_type].'"','types LIKE "[""'.$region_type.'%"' ]]);

                if (count($entity_place_region->id_group) == 1)
                {
                    $organization_place[] = end($entity_place_region->id_group);
                }
                else
                {
                    $reverse_geo_coding_required = true;
                    // If the region name is not unique, need further query
                    if (count($entity_place_region->id_group) == 0)
                    {
                        // Region name does not exist, need to use geo reverse coding to find it (new suburb, city etc that hasn't been lodged before)
                    }
                    else
                    {
//                        print_r($entity_place_region->row);
                    }
                }
            }
        }

        if ($reverse_geo_coding_required)
        {
            $request = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$organization_google_place['location_latitude'].','.$organization_google_place['location_longitude'].'&key='.$global_preference->google_api_credential_server;
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
                $content_row['message'] = 'Fail to get reverse geocoding results from Google. Given Location returns empty result. Organization '.$result_row['id'];
                $ajax_result['updated_data'][] = $content_row;
                continue;
            }
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
        }

        $entity_organization_obj = new entity_organization();
        $entity_organization_obj->set(['row'=>[['id'=>$result_row['id'],'place'=>$organization_place]],'fields'=>['id','place']]);
        $entity_place->set();
        $content_row = [
            'status'=>'OK',
            'message'=>'Organization '.$result_row['id'].' Place Set'
        ];
        $ajax_result['updated_data'][] = $content_row;
    }
}

$ajax_result['execution_time'] = round(microtime(true) - $timestamp, 2);
if (!empty($_REQUEST['start_time'])) $ajax_result['start_time'] = $_REQUEST['start_time'];
print_r(json_encode($ajax_result));