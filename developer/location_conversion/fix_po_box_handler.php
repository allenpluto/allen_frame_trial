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

$entity_organization = new entity_organization();
$sql = 'SELECT Listing.id, Listing.title, Listing.address, Listing.address2, Listing.city, Listing.state, Listing.zip_code, tbl_entity_organization.place_id AS current_place_id, tbl_entity_place.id AS place_id, tbl_entity_place.formatted_address, tbl_entity_place.location_latitude, tbl_entity_place.location_longitude, tbl_entity_place.name as place_name, tbl_entity_place.alternate_name as place_alt_name, tbl_entity_place.locality as place_locality, tbl_entity_place.postal_code, tbl_entity_place.administrative_area_level_2, tbl_entity_place.administrative_area_level_1, tbl_entity_place.country FROM Listing JOIN tbl_entity_organization ON Listing.id = tbl_entity_organization.id JOIN tbl_entity_place ON (Listing.city = tbl_entity_place.locality AND Listing.zip_code = tbl_entity_place.postal_code AND tbl_entity_place.types LIKE "[""locality%") WHERE tbl_entity_organization.import_error < 4 AND listing.address LIKE "%PO BOX%" AND listing.address2 = ""'; // AND tbl_entity_organization.status = "A" AND Listing.importID != 10003
$sql .= ' ORDER BY Listing.id DESC';
$sql .= ' LIMIT '.$limit;
$query = $entity_organization->query($sql);

if ($query === false)
{
    $ajax_result['success'] = false;
    $ajax_result['error_message'][] = 'Fail to get listing from database';
}
else
{
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $result_row_index=>&$result_row)
    {
        $address_additional = [];
        $address_refined = trim($result_row['address']);
        preg_match_all('/((g|general |g\.|g\s)?p(ost|\.|\s)?o(ffice|\.|\s)? box)\s+([^\s|,|\/]+)/i', $address_refined, $matches);
        foreach($matches[0] as $match_index=>$match)
        {
            $match = trim($match);
            $address_additional[] = $match;
            $address_refined = preg_replace('/'.$match.'([\s|,|\/|\-]*)/i', '', $address_refined);
        }

        $address_refined = trim($address_refined,'-,/');
        if ($address_refined == '')
        {
            // address is empty, use suburb location for listing
            $entity_organization_obj = new entity_organization();
            $entity_organization_obj->set(['row'=>[['id'=>$result_row['id'],'place_id'=>$result_row['place_id'],'address_additional'=>implode(', ',$address_additional),'import_error'=>5]],'fields'=>['id','place_id','address_additional','import_error']]);
            $region_types = ['locality','postal_code','administrative_area_level_2','administrative_area_level_1','country'];
            $reverse_geo_coding_required = false;
            $organization_place = [$result_row['place_id']];
            foreach($region_types as $region_type_index=>$region_type)
            {
                if (!empty($result_row[$region_type]))
                {
                    $entity_place_region = new entity_place();
                    $entity_place_region->get(['where'=>['name = "'.$result_row[$region_type].'"','types LIKE "[""'.$region_type.'%"' ]]);

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
//                            print_r($entity_place_region->row);
                        }
                    }
                }
            }

            if ($reverse_geo_coding_required)
            {
                $request = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$result_row['location_latitude'].','.$result_row['location_longitude'].'&key='.$global_preference->google_api_credential_server;
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
                $entity_place = new entity_place();
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
                $entity_place->set();
            }

            $entity_organization_obj = new entity_organization();
            $entity_organization_obj->set(['row'=>[['id'=>$result_row['id'],'place'=>$organization_place]],'fields'=>['id','place']]);
            $ajax_result['updated_data'][] = ['status'=>'OK','message'=>$result_row['id'].' place set'.($reverse_geo_coding_required?' with reverse geo coding':'')];
        }
        else
        {
            $entity_organization_obj = new entity_organization();
            $entity_organization_obj->set(['row'=>[['id'=>$result_row['id'],'import_error'=>4]],'fields'=>['id','import_error']]);
            $ajax_result['updated_data'][] = ['status'=>'REQUEST_DENIED','message'=>$result_row['id'].' Manual input required'];
        }
    }
}

$ajax_result['execution_time'] = round(microtime(true) - $timestamp, 2);
if (!empty($_REQUEST['start_time'])) $ajax_result['start_time'] = $_REQUEST['start_time'];
print_r(json_encode($ajax_result));