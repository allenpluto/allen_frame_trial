<?php
error_reporting(0);
define('PATH_SITE_BASE', dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR);
include('../../system/config/config.php');

$result = ['success'=>true,'updated_data'=>[]];
$start_stamp = microtime(1);



    $option = [
        'target_path'=>'D:\HPL1',
        'limit'=>10,
    ];

if (!empty($_REQUEST['limit'])) {
    $option['limit'] = $_REQUEST['limit'];
}
if (!empty($_REQUEST['start_time'])) {
    $result['start_time'] = $_REQUEST['start_time'];
}

    function csv_headline($file_type)
    {
        $output_row = '"=LEFT(CELL(""filename""),LEN(CELL(""filename""))-4)&""\\""","File Name","File Extension","File Size","Last Modified"';

        switch ($file_type) {
            case 'office_word':
            case 'office_excel':
            case 'office_powerpoint':
                $output_row .= ',"Creator","Last Modified By","Created Date","Last Modified Date"';
                break;
            case 'image':
                $output_row .= ',"Width","Height"';
        }

        return $output_row;
    }

    function csv_row($file_row, $file_type)
    {
        $output_row = '"=HYPERLINK($A$1 & ""'.$file_row['new_name'].'.'.$file_row['extension'].'"",""'.$file_row['new_name'].'.'.$file_row['extension'].'"")","'.$file_row['name'].'","'.$file_row['extension'].'","'.number_format($file_row['file_size'] / 1048576, 2).'","'.date('j M y H:i',strtotime($file_row['source_time'])).'"';

        switch ($file_type) {
            case 'office_word':
            case 'office_excel':
            case 'office_powerpoint':
                $output_row .= ',"'.$file_row['creator'].'","'.$file_row['last_modified_by'].'","'.date('j M y H:i',strtotime($file_row['created_date'])).'","'.date('j M y H:i',strtotime($file_row['modified_date'])).'"';
                break;
            case 'image':
                $output_row .= ',"'.$file_row['image_width'].'","'.$file_row['image_height'].'"';
        }

        return PHP_EOL.$output_row;
    }
if (!file_exists($option['target_path'])) mkdir($option['target_path'], 0755, true);
$entity_file_obj = new entity_file();
$entity_file_obj->get(['where'=>'file_scanned = 0 AND is_dir = 0','limit'=>$option['limit']]);
$result['debug'][] = count($entity_file_obj->id_group).' records found';
$entity_file_obj->update(['file_scanned'=>-1]);
//echo '<pre>';
//print_r($entity_file_obj->row);
//exit;
foreach ($entity_file_obj->row as $file_row) {
    $file_row['extension'] = strtolower($file_row['extension']);
    set_time_limit(240);
    $result['debug'][] = 'Record '.$file_row['id'].': '.$file_row['extension'];
    foreach($entity_file_obj->file_type_extension_map as $file_type=>$extension_list) {        
        if (in_array($file_row['extension'], $extension_list)) {
            $result['debug'][] = 'Record '.$file_row['id'].': '.$file_row['extension'].' match file type "'.$file_type.'"';
            $csv_file_path = $option['target_path'].DIRECTORY_SEPARATOR.$file_type.'.csv';
            if (!file_exists($csv_file_path)) {
                if (!file_exists($option['target_path'].DIRECTORY_SEPARATOR.$file_type)){
                    mkdir($option['target_path'].DIRECTORY_SEPARATOR.$file_type, 0755, true);
                }
                $output_row = csv_headline($file_type);
                file_put_contents($csv_file_path,$output_row);
            }
            $new_file_name = $file_row['name'];
//            $pattern = '/(?:^|\s)(\d{4}-\d{2}-\d{2})(?:$|\s|_)/';
            if (strlen($file_row['name']) > 31) {
                $new_file_name =  substr($new_file_name, 0, 25).'_'.substr($new_file_name, -5);
            }
            $new_file_name .= 'xHPL1';
            $file_path_parts = explode(DIRECTORY_SEPARATOR, $file_row['source_file']);
            if (count($file_path_parts) > 2) {
                $new_file_name .= 'x'.substr($file_path_parts[1],0,20);
                if (count($file_path_parts) > 3) {
                    $new_file_name .= 'x'.substr($file_path_parts[count($file_path_parts)-2],0,20);
                }
            }
            $file_row['new_name'] = $new_file_name;
            $duplicate_file_name_counter = 1;
            while (file_exists($option['target_path'].DIRECTORY_SEPARATOR.$file_type.DIRECTORY_SEPARATOR.$file_row['new_name'].'.'.$file_row['extension'])) {
                $file_row['new_name'] = $new_file_name.'_x'.sprintf("%02d", $duplicate_file_name_counter);
                $duplicate_file_name_counter++;
            }
            $output_row = csv_row($file_row, $file_type);

            copy($file_row['source_root'].$file_row['source_file'],$option['target_path'].DIRECTORY_SEPARATOR.$file_type.DIRECTORY_SEPARATOR.$file_row['new_name'].'.'.$file_row['extension']);
            $result['updated_data'][] = ['status'=>'OK','message'=>$file_type.": ".$file_row['new_name'].'.'.$file_row['extension']];
            file_put_contents($csv_file_path,$output_row,FILE_APPEND);

            $new_file_row = [
                'id' => $file_row['id'],
                'file_scanned' => 1,
                'target_file' => $file_type.DIRECTORY_SEPARATOR.$file_row['new_name'].'.'.$file_row['extension'],
            ];

            $entity_file_obj2 = new entity_file();
            $entity_file_obj2->set_row($new_file_row);
        }
    }
}
//$entity_file_obj->update(['file_scanned'=>1]);



//print_r("\n".$pdf_count." out of ".$total_count." pdf files generated. Execution Time: ".(microtime(1)-$start_stamp)."\n");
$result['execution_time'] = microtime(1)-$start_stamp;
echo json_encode($result);