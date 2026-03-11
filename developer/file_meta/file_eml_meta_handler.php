<?php
error_reporting(0);
define('PATH_SITE_BASE', dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR);
include('../../system/config/config.php');

$result = ['success'=>true,'updated_data'=>[]];
$start_stamp = microtime(1);


$entity_file_obj = new entity_file();
$step_list = array_keys($entity_file_obj->file_type_extension_map);
//$step_list = ['office_word', 'office_powerpoint', 'office_excel'];
//        'mail'=>['pst','ost','msg','eml'],
//        'image'=>['jpg','jpeg','gif','png','bmp','webp'],
//        'office_word'=>['doc','docx','docm','dot','dotx','dotm','rtf','odt'],
//        'office_excel'=>['xls','xlsb','xlsm','xlsx','xlt','xltx','xltm','xltf','xla','xlm','xlw','odc','ods'],
//
//        'office_powerpoint'=>['ppt','pptm','pptx','ppsm','ppsx','ppam','potm','potx']
$row_limit = 10;

$step_requested = $step_list;
if (!empty($_REQUEST['step'])) {
    if (is_string($_REQUEST['step'])) {
        $step_requested = array_intersect($step_list, explode(',',$_REQUEST['step']));
    } else {
        if (is_array($_REQUEST['step'])) {
            $step_requested = array_intersect($step_list, $_REQUEST['step']);
        }
    }
}
if (!empty($_REQUEST['limit'])) {
    $row_limit = $_REQUEST['limit'];
}
if (!empty($_REQUEST['start_time'])) {
    $result['start_time'] = $_REQUEST['start_time'];
}

set_time_limit(240);
//    $source_folder = 'C:\1\jason.qian@halikos.com.au-2.pst';
//$target_folder = 'C:\Peter\result\\';
//echo '<pre>';

//
        //foreach ($entity_file_obj->row as $file_row) {
        //    set_time_limit(240);
        //
        //    $entity_eml_obj = new entity_eml();
        //    $entity_eml_row = [];
        //
        //    $entity_eml_row['id'] = $file_row['id'];
        //
        //    $entity_eml_row['source_file'] = $file_row['source_file'];
        //    $entity_eml_row['source_root'] = $file_row['source_root'];
        //    $entity_eml_row['description'] = 1;
        //
        //    echo json_encode($entity_eml_row).PHP_EOL;
        //    $entity_eml_obj->set_row($entity_eml_row);
        //    echo 'update row 1: '.json_encode($entity_eml_row).' | '.json_encode($entity_eml_obj->id_group).PHP_EOL;
        //}
function csv_headline($file_type)
{
    $index_file_length = strlen($file_type) + 5;    // 4 for .csv; 5 for .xlsm
    $output_row = '"=LEFT(CELL(""filename""),LEN(CELL(""filename""))-'.$index_file_length.')","PDF File","Original PST","Original File Extension","File Size"';

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
    $file_path_parts = explode('\\',$file_row['source_file']);
    array_pop($file_path_parts);
    array_shift($file_path_parts);
    $file_path = implode(DIRECTORY_SEPARATOR, $file_path_parts);


    $file_size = $file_row['file_size'];
    if ($file_size > 1024) {
        $file_size = $file_size / 1024;
        if ($file_size > 1024) {
            $file_size = number_format($file_size / 1024, 2).' MB';
        } else{
            $file_size = number_format($file_size, 2).' KB';
        }
    } else {
        $file_size = $file_size.' Bytes';
    }

    $output_row = '"=HYPERLINK($A$1 & ""'.$file_path.DIRECTORY_SEPARATOR.$file_row['name'].'.'.$file_row['extension'].'"",""'.$file_row['name'].'.'.$file_row['extension'].'"")","=HYPERLINK($A$1 & ""'.$file_path.DIRECTORY_SEPARATOR.$file_row['name'].'.pdf"",""'.$file_row['name'].'.pdf"")","'.$file_path.'","'.$file_row['extension'].'","'.$file_size.'"';

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

$remaining_row_limit = $row_limit;
foreach ($step_list as $file_type) {
    if (in_array($file_type, $step_requested)) {
        $result[$file_type] = [];
        $entity_file_obj2 = new entity_file();
        $entity_file_obj2->get(['where'=>['extension IN ("'.implode('","',$entity_file_obj->file_type_extension_map[$file_type]).'")','file_scanned = 0'],'limit'=>$remaining_row_limit]);
//        $entity_file_obj2->update(['file_scanned'=>-1]);

        foreach ($entity_file_obj2->row as $file_row) {
            set_time_limit(240);
            $source_file = $file_row['source_root'].$file_row['source_file'];
//            if (!file_exists($file_row['source_root'].DIRECTORY_SEPARATOR.$file_type.'.csv')) {
//                $output_row = csv_headline($file_type);
//                file_put_contents($file_row['source_root'].DIRECTORY_SEPARATOR.$file_type.'.csv',$output_row);
//            }

            $entity_file_obj3 = new entity_file();
            $file_row['file_scanned'] = -1;
            $entity_file_obj3->set_row($file_row);
            $remaining_row_limit--;

            switch ($file_type) {
                case 'office_word':
                    try {
                        $phpWord = \PhpOffice\PhpWord\IOFactory::load($source_file);
                        $properties = $phpWord->getDocInfo();
                        $file_row['meta_property'] = json_encode((array)$properties);

                        $file_row['creator'] = $properties->getCreator();
                        $file_row['created_date'] = date ('Y-m-d H:i:s', $properties->getCreated());
                        $file_row['last_modified_by'] = $properties->getLastModifiedBy();
                        $file_row['modified_date'] = date ('Y-m-d H:i:s', $properties->getModified());
                        $file_row['file_scanned'] = 1;
                    } catch (Exception $e) {
                        $file_row['meta_property'] = json_encode($e);

                        $file_row['creator'] = '';
                        $file_row['created_date'] = '';
                        $file_row['last_modified_by'] = '';
                        $file_row['modified_date'] = '';
                    }
                    break;
                case 'office_excel':
                    try {
                        set_time_limit(20);
                        $phpSpreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($source_file);
                        $properties = $phpSpreadsheet->getProperties();
                        $file_row['meta_property'] = json_encode($properties);

                        $file_row['creator'] = $properties->getCreator();
                        $file_row['created_date'] = date ('Y-m-d H:i:s', $properties->getCreated());
                        $file_row['last_modified_by'] = $properties->getLastModifiedBy();
                        $file_row['modified_date'] = date ('Y-m-d H:i:s', $properties->getModified());
                        $file_row['file_scanned'] = 1;
                    } catch (Exception $e) {
                        $file_row['meta_property'] = json_encode($e);

                        $file_row['creator'] = '';
                        $file_row['created_date'] = '';
                        $file_row['last_modified_by'] = '';
                        $file_row['modified_date'] = '';
                    }
                    break;
                case 'office_powerpoint':
                    try {
                        $phpPresentation = \PhpOffice\PhpPresentation\IOFactory::load($source_file);
                        $properties = $phpPresentation->getDocumentProperties();
                        $file_row['meta_property'] = json_encode($properties);

                        $file_row['creator'] = $properties->getCreator();
                        $file_row['created_date'] = date ('Y-m-d H:i:s', $properties->getCreated());
                        $file_row['last_modified_by'] = $properties->getLastModifiedBy();
                        $file_row['modified_date'] = date ('Y-m-d H:i:s', $properties->getModified());
                        $file_row['file_scanned'] = 1;
                    } catch (Exception $e) {
                        $file_row['meta_property'] = json_encode($e);

                        $file_row['creator'] = '';
                        $file_row['created_date'] = '';
                        $file_row['last_modified_by'] = '';
                        $file_row['modified_date'] = '';
                    }
                    break;
                default:
                    $file_row['file_scanned'] = 1;
            }



            $entity_file_obj3->set_row($file_row);

//            $output_row = csv_row($file_row, $file_type);
//            file_put_contents($file_row['source_root'].DIRECTORY_SEPARATOR.$file_type.'.csv',$output_row,FILE_APPEND);

            $result[$file_type] = array_merge($result[$file_type],$entity_file_obj3->row);
            $result['updated_data'][] = ['status'=>'OK','message'=>$file_type.' meta retrieved ('.end($entity_file_obj3->id_group).') Successfully. '.json_encode(end($entity_file_obj3->row))];
            //    echo 'update row 2: '.json_encode($entity_eml_row).' | '.json_encode($entity_eml_obj->id_group).PHP_EOL;
        }

        if ($remaining_row_limit <= 0) {
            break;
        }
    }
}

/*foreach ($step_list as $file_type) {
    if (in_array($file_type, $step_requested)) {
        $entity_file_obj2 = new entity_file();
        $entity_file_obj2->get(['where'=>['extension IN ("'.implode('","',$entity_file_obj->file_type_extension_map[$file_type]).'")']]);

        foreach ($entity_file_obj2->row as $file_row) {
            set_time_limit(240);
            if (!file_exists($file_row['source_root'].DIRECTORY_SEPARATOR.$file_type.'.csv')) {
                $output_row = csv_headline($file_type);
                file_put_contents($file_row['source_root'].DIRECTORY_SEPARATOR.$file_type.'.csv',$output_row);
            }

            if (empty($result[$file_type])){
                $result[$file_type] = [];
            }

            $output_row = csv_row($file_row, $file_type);
            file_put_contents($file_row['source_root'].DIRECTORY_SEPARATOR.$file_type.'.csv',$output_row,FILE_APPEND);

            $result[$file_type] = array_merge($result[$file_type],$file_row);
            $result['updated_data'][] = ['status'=>'OK','message'=>$file_type.' csv row written successfully. '.json_encode(end($file_row))];
            //    echo 'update row 2: '.json_encode($entity_eml_row).' | '.json_encode($entity_eml_obj->id_group).PHP_EOL;
        }

    }
}*/


//print_r("\n".$pdf_count." out of ".$total_count." pdf files generated. Execution Time: ".(microtime(1)-$start_stamp)."\n");
$result['execution_time'] = microtime(1)-$start_stamp;
echo json_encode($result);