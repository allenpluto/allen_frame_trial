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
    'updated_data'=>[],
    'success'=>true,
    'error_message'=>[]
];
$limit = !empty($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;

$entity_rel_place = new entity(null,['table'=>'tbl_rel_organization_to_place']);
$result = $entity_rel_place->get(['where'=>'place_id  NOT IN (SELECT id FROM tbl_entity_place)','limit'=>$limit]);
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
            $content_row['message'] = 'Fail to get place info from Google. Place '.$result_row['place_id'];
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
            continue;
        }
        $organization_google_place = $format->flatten_google_place($response['result']);
        if ($organization_google_place['country'] != 'Australia')
        {
            $content_row['status'] = 'REQUEST_DENIED';
            $content_row['message'] = 'Place is not inside Australia';
            $ajax_result['updated_data'][] = $organization_google_place;
            continue;
        }
        $entity_place->row[] = $organization_google_place;

        $entity_place->set();
        $content_row = [
            'status'=>'OK',
            'message'=>$result_row['place_id'].' Place Set '.$organization_google_place['name'].' '.$organization_google_place['types']
        ];
        $ajax_result['updated_data'][] = $content_row;
    }
}

$ajax_result['execution_time'] = round(microtime(true) - $timestamp, 2);
if (!empty($_REQUEST['start_time'])) $ajax_result['start_time'] = $_REQUEST['start_time'];
print_r(json_encode($ajax_result));