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
$entity_suburb_update = new entity(null,['table'=>'postcode_suburb']);
$result = $entity_suburb->get(['where'=>'match_type < 0','limit'=>$limit]);

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
    foreach($result as $result_row_index => $result_row)
    {
        $content_row = ['status'=>'OK'];
        $entity_place = new entity_place();

        $place_result = $entity_place->get(['where'=>'locality = "'.$result_row['suburb'].'" AND types LIKE "[""locality%"']);
        if (empty($place_result))
        {
            $entity_suburb_update->set(['row'=>[['id'=>$result_row['id'],'match_type'=>0]],'fields'=>['id','match_type']]);
        }
        else
        {
            if (count($place_result) > 1)
            {
                $set_row = [];
                foreach($place_result as $place_result_row)
                {
                    if ($place_result_row['administrative_area_level_1'] == $state_list[$result_row['state']])
                    {
                        if (!empty($set_row))
                        {
                            $set_row = ['id'=>$result_row['id'],'match_type'=>2];
                        }
                        else
                        {
                            $set_row = ['id'=>$result_row['id'],'place_id'=>$place_result_row['id'],'match_type'=>1];
                        }
                    }
                }
                if (empty($set_row))
                {
                    $set_row = ['id'=>$result_row['id'],'match_type'=>3];
                }
                $entity_suburb_update->set(['row'=>[$set_row],'fields'=>array_keys($set_row)]);
            }
            else
            {
                $place_result_row = end($place_result);
                if ($place_result_row['administrative_area_level_1'] != $state_list[$result_row['state']])
                {
                    $entity_suburb_update->set(['row'=>[['id'=>$result_row['id'],'match_type'=>3]],'fields'=>['id','place_id','match_type']]);
                }
                else
                {
                    $entity_suburb_update->set(['row'=>[['id'=>$result_row['id'],'place_id'=>$place_result_row['id'],'match_type'=>1]],'fields'=>['id','place_id','match_type']]);
                }
            }
        }
        $suburb_update_result_row = end($entity_suburb_update->row);
        switch ($suburb_update_result_row['match_type'])
        {
            case 0:
                $ajax_result['updated_data'][] = ['status'=>'ZERO_RESULT','message'=>'Suburb not Found '.$result_row['id'].' '.$result_row['suburb'].', '.$result_row['state']];
                break;
            case 1:
                $ajax_result['updated_data'][] = ['status'=>'OK','message'=>'Exact Match Found '.$result_row['id'].' '.$result_row['suburb'].', '.$result_row['state'].' - '.$place_result_row['id']];
                break;
            case 2:
                $message = 'Multiple Match Found '.$result_row['id'].' '.$result_row['suburb'].', '.$result_row['state'];
                foreach($place_result as $place_result_row)
                {
                    $message .= '<br>'.$place_result_row['id'].' - '.$place_result_row['formatted_address'];
                }
                $ajax_result['updated_data'][] = ['status'=>'AMBIGUOUS_RESULTS','message'=>$message];
                break;
            case 3:
                $ajax_result['updated_data'][] = ['status'=>'AMBIGUOUS_RESULTS','message'=>'Match Found '.$result_row['id'].' '.$result_row['suburb'].', '.$result_row['state'].' - '.$place_result_row['id'].' - '.$place_result_row['formatted_address']];
        }
    }
}

$ajax_result['execution_time'] = round(microtime(true) - $timestamp, 2);
if (!empty($_REQUEST['start_time'])) $ajax_result['start_time'] = $_REQUEST['start_time'];
print_r(json_encode($ajax_result));