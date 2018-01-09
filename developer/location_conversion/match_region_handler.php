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

$entity_suburb = new entity(null,['table'=>'postcode_suburb']);
$result = $entity_suburb->get(['where'=>'match_type = 1','limit'=>$limit]);

$state_list = [
    'QLD'=>'Queensland',
    'WA'=>'Western Australia',
    'SA'=>'South Australia',
    'NSW'=>'New South Wales',
    'NT'=>'Northern Territory',
    'JBT'=>'Jervis Bay Territory',
    'ACT'=>'Australian Capital Territory',
    'VIC'=>'Victoria',
    'TAS'=>'Tasmania'
];
if (empty($result))
{
    $ajax_result['success'] = false;
    $ajax_result['error_message'] = 'Fail to get organizations from database';
}
else
{
    $set_row_group = [];
    foreach($result as $result_row_index => $result_row)
    {
        $content_row = ['status'=>'OK'];
        $entity_post = new entity_place();
        $post_result = $entity_post->get_related_place(['id_group'=>[$result_row['place_id']],'type'=>'postal_code']);
        $entity_region = new entity_place();
        $region_result = $entity_region->get_related_place(['id_group'=>[$result_row['place_id']],'type'=>'administrative_area_level_2']);
        $entity_state = new entity_place();
        $state_result = $entity_state->get_related_place(['id_group'=>[$result_row['place_id']],'type'=>'administrative_area_level_1']);
        $first_result_row_flag =true;

        if (count($post_result) == 0)
        {
            $post_result = [['id'=>'','name'=>$result_row['post_code'],'alternate_name'=>'']];
            $result_row['match_type'] = 4;
        }
        if (count($region_result) == 0)
        {
            $region_result = [['id'=>'','name'=>'','alternate_name'=>'']];
            $result_row['match_type'] = 4;
        }
        if (count($state_result) == 0)
        {
            $state_result = [['id'=>'','name'=>'','alternate_name'=>$result_row['state']]];
            $result_row['match_type'] = 4;
        }


        foreach($post_result as $post_result_row)
        {
            $set_row = $result_row;
            if (intval($post_result_row['name']) != $result_row['post_code'])
            {
                $set_row['id'] = '';
            }
            if (count($region_result) > 1)
            {
                $entity_post_region = new entity_place();
                $post_region_result = $entity_post_region->get_related_place(['id_group'=>[$post_result_row['id']],'type'=>'administrative_area_level_2']);
                $post_region_result = array_intersect_key($post_region_result, $region_result);
            }
            else
            {
                $post_region_result = $region_result;
            }
            foreach($post_region_result as $region_result_row)
            {
                if (count($state_result) > 1)
                {
                    $post_entity_state = new entity_place();
                    $post_state_result = $post_entity_state->get_related_place(['id_group'=>[$region_result_row['id']],'type'=>'administrative_area_level_1']);
                    $post_state_result = array_intersect_key($post_state_result, $state_result);
                }
                else
                {
                    $post_state_result = $state_result;
                }
                foreach($post_state_result as $state_result_row)
                {
                    if ($state_result_row['alternate_name'] != $result_row['state'])
                    {
                        $set_row['id'] = '';
                    }
                    if (!$first_result_row_flag)
                    {
                        $set_row['id'] = '';
                    }
                    if ($set_row['id'] != '')
                    {
                        $first_result_row_flag = false;
                    }
                    $set_row['post_code'] = intval($post_result_row['name']);
                    $set_row['post_id'] = $post_result_row['id'];
                    $set_row['region'] = $region_result_row['alternate_name'];
                    $set_row['region_id'] = $region_result_row['id'];
                    $set_row['state'] = $state_result_row['alternate_name'];
                    $set_row['state_id'] = $state_result_row['id'];
                    if ($set_row['match_type'] == 1)
                    {
                        $set_row['match_type'] = 6;
                    }
                    $set_row_group[] = $set_row;
                }
            }
        }
        if ($first_result_row_flag)
        {
            $last_set_row = array_pop($set_row_group);
            $last_set_row['id'] = $result_row['id'];
            $last_set_row['match_type'] = 5;
            $set_row_group[] = $last_set_row;
        }
    }
    $entity_suburb_update = new entity(null,['table'=>'postcode_suburb']);
    $entity_suburb_update->set(['row'=>$set_row_group]);
    $update_result = $entity_suburb_update->get();

    if (empty($update_result))
    {
        $ajax_result['updated_data'][] = ['status'=>'ZERO_RESULT','message'=>'Nothing Updated'];
    }
    else
    {
        foreach($update_result as $update_result_row)
        {
            $ajax_result['updated_data'][] = ['status'=>($update_result_row['match_type'] == 6?'OK':'WARNING'),'message'=>'New Suburb Set '.$update_result_row['id'].' '.$update_result_row['suburb'].', '.$update_result_row['post_code'].', '.$update_result_row['region'].', '.$update_result_row['state']];
        }
    }
}

$ajax_result['execution_time'] = round(microtime(true) - $timestamp, 2);
if (!empty($_REQUEST['start_time'])) $ajax_result['start_time'] = $_REQUEST['start_time'];
print_r(json_encode($ajax_result));