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
$limit = !empty($_REQUEST['limit']) ? $_REQUEST['limit'] : 20;

switch ($_REQUEST['method'])
{
    case 'get_listing':
        $entity_organization = new entity();
        $sql = 'SELECT Listing.id, Listing.title, Listing.address, Listing.address2, Listing.city, Listing.state, Listing.zip_code, tbl_entity_place.id AS place_id, tbl_entity_place.formatted_address, tbl_entity_place.name as place_name, tbl_entity_place.alternate_name as place_alt_name, tbl_entity_place.locality as place_locality FROM Listing LEFT JOIN tbl_entity_organization ON Listing.id = tbl_entity_organization.id LEFT JOIN tbl_entity_place ON tbl_entity_organization.place_id = tbl_entity_place.id WHERE tbl_entity_organization.import_error < 5 AND Listing.address != "" AND Listing.status = "A" AND Listing.importid != 10003'; // AND tbl_entity_organization.status = "A" AND Listing.importID != 10003
        if (!empty($_REQUEST['listing_id']))
        {
            $sql .= ' AND Listing.id NOT IN ('.$_REQUEST['listing_id'].')';
        }
        $sql .= ' ORDER BY Listing.city, Listing.state, Listing.zip_code, Listing.address';
        $sql .= ' LIMIT '.$limit;
        $query = $entity_organization->query($sql);
        if ($query === false)
        {
            $ajax_result['success'] = false;
            $ajax_result['error_message'][] = 'Fail to get listing from database';
            break;
        }
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $result_row_index=>&$result_row)
        {
            $address_additional = [];
            $address_refined = trim($result_row['address']);
            preg_match_all('/((g|general |g\.|g\s)?p(ost|\.|\s)?o(ffice|\.|\s)? box|shop|unit|level|lvl|suite|ste|studio|office|factory|shed)\s+([^\s|,|\/|\)]+)|(([^,|\/|\-]+)(shopping centre|shopping center|plaza|arcade|shoppingtown|ground level|hotel|motel|hospital))|(\d+(st|nd|rd|th)?\s(floor|level))|([u|l]\d+)/i', $address_refined, $matches);
            foreach($matches[0] as $match_index=>$match)
            {
                $match = trim($match);
                $address_additional[] = $match;
                $address_refined = preg_replace('/'.$match.'([\s|,|\/|\-]*)/i', '', $address_refined);
            }

            $address_refined = trim($address_refined,'-,/');
            $address_refined = preg_replace('/^([^\s|,|\)|\-]*)(\s*\-\s*)?(\d*)([,|\/]*)/','$1$4',$address_refined);
            $result_row['address_additional'] = implode(', ',$address_additional);
            $result_row['address_refined'] = strtolower($address_refined);
        }
        $ajax_result['updated_data'] = $result;
        break;
    case 'set_listing':
        if (empty($_REQUEST['id']))
        {
            $ajax_result['success'] = false;
            $ajax_result['error_message'][] = 'Listing id not provided';
            break;
        }
        $ajax_result['updated_data'] = [];
        foreach ($_REQUEST['id'] as $listing_id_key=>$listing_id)
        {
            $update_row = ['id'=>$listing_id,'import_error'=>5];
            if (isset($_REQUEST['place']))
            {
                if (empty($_REQUEST['place']))
                {
                    $update_row['address_additional'] = '';
                    $update_row['place_id'] = '';
                    $update_row['place'] = '';
                }
                else
                {
                    $place = $_REQUEST['place'];
                    foreach ($place as $place_attribute=>&$place_attribute_value)
                    {
                        if (is_array($place_attribute_value))
                        {
                            $place_attribute_value = json_encode($place_attribute_value);
                        }
                    }
                    $entity_place = new entity_place();
                    $entity_place->set(['row'=>[$place]]);
                    $update_row['place_id'] = $place['id'];
                    $update_row['address_additional'] = $_REQUEST['address_additional'];
                    if (!empty($_REQUEST['region']))
                    {
                        $update_row['place'] = $_REQUEST['region'];
                    }
                    else
                    {
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
                                    $ajax_result['success'] = false;
                                    $ajax_result['error_message'][] = 'Ambiguous region name '.$organization_google_place[$region_type];
                                    foreach($entity_place_region->row as $row_index=>$row)
                                    {
                                        $ajax_result['error_message'][] = $row['formatted_address'].' ['.$row['id'].']';
                                    }
                                }
                            }
                        }
                        $update_row['place'] = $organization_place;
                    }

                }
            }
//        $entity_organization = new entity_organization($_REQUEST['id']);
//        $entity_organization->update($update_row);
            $entity_organization_obj = new entity_organization();
            $entity_organization_obj->set(['row'=>[$update_row],'fields'=>array_keys($update_row)]);
            $ajax_result['updated_data'] = array_merge($ajax_result['updated_data'],$entity_organization_obj->get(['fields'=>array_keys($update_row)]));
        }



        break;
}

//$entity_organization = new entity_organization();
//$result = $entity_organization->get(['where'=>'tbl_entity_organization.place_id != "" AND tbl_entity_organization.place_id NOT IN (SELECT id FROM tbl_entity_place)','limit'=>$limit]);
//if (empty($result))
//{
//    $ajax_result['success'] = false;
//    $ajax_result['error_message'] = 'Fail to get organizations from database';
//}
//else
//{
//    foreach($result as $result_row_index => $result_row)
//    {
//        $content_row = ['status'=>'OK'];
//        $entity_place = new entity_place();
//        $request = 'https://maps.googleapis.com/maps/api/place/details/json?placeid='.$result_row['place_id'].'&key='.$global_preference->google_api_credential_server;
//        $response = file_get_contents($request);
//        if (empty($response))
//        {
//            $content_row['status'] = 'REQUEST_DENIED';
//            $content_row['message'] = 'Fail to get place info from Google, No Response';
//            $ajax_result['updated_data'][] = $content_row;
//            continue;
//        }
//        $response = json_decode($response,true);
//        if (!is_array($response))
//        {
//            $content_row['status'] = 'REQUEST_DENIED';
//            $content_row['message'] = 'Fail to get place info from Google, Illegal Format Response';
//            $ajax_result['updated_data'][] = $content_row;
//            continue;
//        }
//        if ($response['status'] != 'OK')
//        {
//            $content_row['status'] = $response['status'];
//            $content_row['message'] = 'Fail to get place info from Google. Organization '.$result_row['id'];
//            if (!empty($response['error_message'])) $content_row['message'] .= ' - '.$response['error_message'];
//        }
//        if (empty($response['result']))
//        {
//            $content_row['status'] = 'ZERO_RESULTS';
//            $content_row['message'] = 'Fail to get place info from Google. Given Place ID returns empty result';
//        }
//        if ($content_row['status'] != 'OK')
//        {
//            $ajax_result['updated_data'][] = $content_row;
//            $entity_organization_obj = new entity_organization();
//            $entity_organization_obj->set(['row'=>[['id'=>$result_row['id'],'place_id'=>'']],'fields'=>['id','place_id']]);
//            continue;
//        }
//        $organization_google_place = $format->flatten_google_place($response['result']);
//        if ($organization_google_place['country'] != 'Australia')
//        {
//            $content_row['status'] = 'REQUEST_DENIED';
//            $content_row['message'] = 'Place is not inside Australia';
//            $ajax_result['updated_data'][] = $content_row;
//            $entity_organization_obj = new entity_organization();
//            $entity_organization_obj->set(['row'=>[['id'=>$result_row['id'],'place_id'=>'']],'fields'=>['id','place_id']]);
//            continue;
//        }
//
//        $entity_place->row[] = $organization_google_place;
//        if ($result_row['place_id'] != $organization_google_place['id'])
//        {
//            $entity_organization_obj = new entity_organization();
//            $entity_organization_obj->set(['row'=>[['id'=>$result_row['id'],'place_id'=>$organization_google_place['id']]],'fields'=>['id','place_id']]);
//        }
//
//        $request = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$organization_google_place['location_latitude'].','.$organization_google_place['location_longitude'].'&key='.$global_preference->google_api_credential_server;
//        $response = file_get_contents($request);
//        if (empty($response))
//        {
//            $content_row['status'] = 'REQUEST_DENIED';
//            $content_row['message'] = 'Fail to get place info from Google, No Response';
//            $ajax_result['updated_data'][] = $content_row;
//            continue;
//        }
//        $response = json_decode($response,true);
//        if ($response['status'] != 'OK')
//        {
//            $content_row['status'] = $response['status'];
//            $content_row['message'] = 'Fail to get reverse geocoding results from Google. '.$response['error_message'];
//            $ajax_result['updated_data'][] = $content_row;
//            continue;
//        }
//        if (empty($response['results']))
//        {
//            $content_row['status'] = 'ZERO_RESULTS';
//            $content_row['message'] = 'Fail to get reverse geocoding results from Google. Given Location returns empty result. Organization '.$result_row['id'];
//            $ajax_result['updated_data'][] = $content_row;
//            continue;
//        }
//        $region_types = ['locality','sublocality','postal_code','country','administrative_area_level_1','administrative_area_level_2'];
//        $organization_place = [];
//        foreach($response['results'] as $response_result_row_index => $response_result_row)
//        {
//            $type = array_intersect($response_result_row['types'], $region_types);
//            if (!empty($type))
//            {
//                // If the result_row is a region type, store the row into tbl_entity_place and relation into tbl_rel_organization_to_place
//                $organization_region_google_place = $format->flatten_google_place($response_result_row);
//                $organization_place[] = $organization_region_google_place['id'];
//                $entity_place->row[] = $organization_region_google_place;
//            }
//        }
//        $entity_organization_obj = new entity_organization();
//        $entity_organization_obj->set(['row'=>[['id'=>$result_row['id'],'place'=>$organization_place]],'fields'=>['id','place']]);
//        $entity_place->set();
//        $content_row = [
//            'status'=>'OK',
//            'message'=>'Organization '.$result_row['id'].' Place Set'
//        ];
//        $ajax_result['updated_data'][] = $content_row;
//    }
//}

$ajax_result['execution_time'] = round(microtime(true) - $timestamp, 2);
if (!empty($_REQUEST['start_time'])) $ajax_result['start_time'] = $_REQUEST['start_time'];
print_r(json_encode($ajax_result));