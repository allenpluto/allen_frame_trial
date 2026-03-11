<?php
error_reporting(0);
define('PATH_SITE_BASE', dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR);
include('../../system/config/config.php');

$result = ['success'=>true,'updated_data'=>[]];
$start_stamp = microtime(1);


$entity_file_obj = new entity_file();
$step_list = array_keys($entity_file_obj->file_type_extension_map);
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
if (in_array('office_word', $step_requested)) {
    $result['office_word'] = [];
    $entity_file_obj2 = new entity_file();
    $entity_file_obj2->get(['where'=>['extension IN ("'.implode('","',$entity_file_obj->file_type_extension_map['office_word']).'")','file_scanned = 0'],'limit'=>$row_limit]);
    $entity_file_obj2->update(['file_scanned'=>-1]);
    foreach ($entity_file_obj2->row as $file_row) {
        set_time_limit(240);
        $source_file = $file_row['source_root'].$file_row['source_file'];

        $entity_file_obj3 = new entity_file();
//        $file_row['file_scanned'] = -1;
//        $entity_file_obj3->set_row($file_row);
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


        $entity_file_obj3->set_row($file_row);
        $result['office_word'] = array_merge($result['office_word'],$entity_file_obj3->row);
        $result['updated_data'][] = ['status'=>'OK','message'=>'office_word meta retrieved ('.end($entity_file_obj3->id_group).') Successfully. '.json_encode(end($entity_file_obj3->row))];
        //    echo 'update row 2: '.json_encode($entity_eml_row).' | '.json_encode($entity_eml_obj->id_group).PHP_EOL;
    }
}

if (in_array('office_excel', $step_requested)) {
    $result['office_excel'] = [];
    $entity_file_obj2 = new entity_file();
    $entity_file_obj2->get(['where'=>['extension IN ("'.implode('","',$entity_file_obj->file_type_extension_map['office_excel']).'")','file_scanned = 0'],'limit'=>$row_limit]);
    $entity_file_obj2->update(['file_scanned'=>-1]);
    foreach ($entity_file_obj2->row as $file_row) {
        set_time_limit(240);
        $source_file = $file_row['source_root'].$file_row['source_file'];

        $entity_file_obj3 = new entity_file();
//        $file_row['file_scanned'] = -1;
//        $entity_file_obj3->set_row($file_row);
        try {
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


        $entity_file_obj3->set_row($file_row);
        $result['office_excel'] = array_merge($result['office_excel'],$entity_file_obj3->row);
        $result['updated_data'][] = ['status'=>'OK','message'=>'office_excel meta retrieved ('.end($entity_file_obj3->id_group).') Successfully. '.json_encode(end($entity_file_obj3->row))];
        //    echo 'update row 2: '.json_encode($entity_eml_row).' | '.json_encode($entity_eml_obj->id_group).PHP_EOL;
    }
}

if (in_array('office_powerpoint', $step_requested)) {
    $result['office_powerpoint'] = [];
    $entity_file_obj2 = new entity_file();
    $entity_file_obj2->get(['where'=>['extension IN ("'.implode('","',$entity_file_obj->file_type_extension_map['office_powerpoint']).'")','file_scanned = 0'],'limit'=>$row_limit]);
    $entity_file_obj2->update(['file_scanned'=>-1]);
    foreach ($entity_file_obj2->row as $file_row) {
        set_time_limit(240);
        $source_file = $file_row['source_root'].$file_row['source_file'];

        $entity_file_obj3 = new entity_file();
//        $file_row['file_scanned'] = -1;
//        $entity_file_obj3->set_row($file_row);
        try {
            $phpPresentation = \PhpOffice\PhpPresentation\IOFactory::load($source_file);
            $properties = $phpPresentation->getProperties();
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


        $entity_file_obj3->set_row($file_row);
        $result['office_powerpoint'] = array_merge($result['office_powerpoint'],$entity_file_obj3->row);
        $result['updated_data'][] = ['status'=>'OK','message'=>'office_powerpoint meta retrieved ('.end($entity_file_obj3->id_group).') Successfully. '.json_encode(end($entity_file_obj3->row))];

        //    echo 'update row 2: '.json_encode($entity_eml_row).' | '.json_encode($entity_eml_obj->id_group).PHP_EOL;
    }
}


//print_r("\n".$pdf_count." out of ".$total_count." pdf files generated. Execution Time: ".(microtime(1)-$start_stamp)."\n");
$result['execution_time'] = microtime(1)-$start_stamp;
echo json_encode($result);