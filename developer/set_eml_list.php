<?php
define('PATH_SITE_BASE', dirname(__DIR__).DIRECTORY_SEPARATOR);
include('../system/config/config.php');
$start_stamp = microtime(1);

set_time_limit(240);
//    $source_folder = 'C:\1\jason.qian@halikos.com.au-2.pst';
//$target_folder = 'C:\Peter\result\\';
//echo '<pre>';
//$entity_file_obj = new entity_file();
//$entity_file_obj->get(['where'=>['extension = "eml"','id NOT IN (SELECT id FROM tbl_entity_eml)'],'limit'=>300000]);
//
//$eml_list = [];
//$basic_fields = ['id','source_file','source_root','description'];
//foreach ($entity_file_obj->row as $file_row) {
//    $file_row['description'] = 1;
//    $eml_list[] = array_intersect_key($file_row, array_flip($basic_fields));
//}
//$entity_eml_obj = new entity_eml();
//$entity_eml_obj->set(['row'=>$eml_list, 'fields'=>$basic_fields]);
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
$source_entity_eml_obj = new entity_eml();
$source_entity_eml_obj->get(['where'=>['description = 1'],'limit'=>300000]);
foreach ($source_entity_eml_obj->row as $file_row) {
    set_time_limit(240);
    $source_file = $file_row['source_root'].$file_row['source_file'];

    $mimeParser = new PhpMimeMailParser\Parser();
    $mimeParser->setPath($source_file);

    $entity_eml_obj = new entity_eml();
    $entity_eml_row = [];

    $entity_eml_row['id'] = $file_row['id'];
    $entity_eml_row['description'] = -1;
    $entity_eml_obj->set_row($entity_eml_row);

    $entity_eml_row['content_text'] = $mimeParser->getMessageBody('text');
    $entity_eml_row['description'] = 2;

    $entity_eml_obj->set_row($entity_eml_row);
    echo 'update row 2: '.json_encode($entity_eml_row).' | '.json_encode($entity_eml_obj->id_group).PHP_EOL;
}

$source_entity_eml_obj = new entity_eml();
$source_entity_eml_obj->get(['where'=>['description = 2'],'limit'=>300000]);
foreach ($source_entity_eml_obj->row as $file_row) {
    set_time_limit(240);
    $source_file = $file_row['source_root'].$file_row['source_file'];

    $mimeParser = new PhpMimeMailParser\Parser();
    $mimeParser->setPath($source_file);

    $entity_eml_obj = new entity_eml();
    $entity_eml_row = [];

    $entity_eml_row['id'] = $file_row['id'];
    $entity_eml_row['description'] = -2;
    $entity_eml_obj->set_row($entity_eml_row);


    $attachments = $mimeParser->getAttachments([false]);
    if (count($attachments) > 0) {
        $entity_eml_row['has_attachment'] = count($attachments);

        $attachments_list = [];
        foreach ($attachments as $attachment_index => $attachment) {
            $attachment_extension = '';
            $attachment_name = $attachment->getFilename();
//                echo 'attachment_name: '.$attachment_name.PHP_EOL;
            if  (strpos($attachment_name, '.')!==false){
                $attachment_name_parts = explode('.',$attachment_name);
                $attachment_extension = end($attachment_name_parts);
            }
            $attachments_list[] = ['name'=>$attachment_name,'extension'=>$attachment_extension];
        }
        $entity_eml_row['attachments'] = json_encode($attachments_list);
        echo 'attachments: '.$entity_eml_row['attachments'].PHP_EOL;
    }
    $entity_eml_row['description'] = 3;

    $entity_eml_obj->set_row($entity_eml_row);
    echo 'update row 5: '.json_encode($entity_eml_row).' | '.json_encode($entity_eml_obj->id_group).PHP_EOL;
    if (!empty($entity_eml_obj->id_group)) {
        $entity_file_obj2 = new entity_file();
        $entity_file_obj2->get(['id_group'=>$entity_eml_obj->id_group]);
        $entity_file_obj2->update(['file_scanned'=>1]);
    }
}

$source_entity_eml_obj = new entity_eml();
$source_entity_eml_obj->get(['where'=>['description = 3'],'limit'=>300000]);
foreach ($source_entity_eml_obj->row as $file_row) {
    set_time_limit(240);
    $source_file = $file_row['source_root'].$file_row['source_file'];

    $mimeParser = new PhpMimeMailParser\Parser();
    $mimeParser->setPath($source_file);

    $entity_eml_obj = new entity_eml();
    $entity_eml_row = [];
    $entity_eml_row['id'] = $file_row['id'];
    $entity_eml_row['description'] = -3;
    $entity_eml_obj->set_row($entity_eml_row);

    $table_fields = $entity_eml_obj->parameter['table_fields'];
    $header = $mimeParser->getHeaders();
    if (!empty($header)) {
        $entity_eml_row['header'] = json_encode($header);
        foreach ($table_fields as $table_field) {
            $header_attr = str_replace('_','-',$table_field);
            if (isset($header[$header_attr])) {
                $entity_eml_row[$table_field] = $header[$header_attr];
            }
        }
    }

    $entity_eml_row['description'] = 4;

    if (!empty($entity_eml_row['date'])) {
        $entity_eml_row['date'] = date('Y-m-d H:i:s', strtotime($entity_eml_row['date']));
    }

    $entity_eml_obj->set_row($entity_eml_row);
    echo 'update row 4: '.json_encode($entity_eml_row).' | '.json_encode($entity_eml_obj->id_group).PHP_EOL;

}

$source_entity_eml_obj = new entity_eml();
$source_entity_eml_obj->get(['where'=>['description = 4'],'limit'=>300000]);
foreach ($source_entity_eml_obj->row as $file_row) {
    set_time_limit(240);
    $source_file = $file_row['source_root'].$file_row['source_file'];

    $mimeParser = new PhpMimeMailParser\Parser();
    $mimeParser->setPath($source_file);

    $entity_eml_obj = new entity_eml();
    $entity_eml_row = [];

    $entity_eml_row['id'] = $file_row['id'];
    $entity_eml_row['description'] = -4;
    $entity_eml_obj->set_row($entity_eml_row);

    $html_content = $mimeParser->getMessageBody('html');
    $entity_eml_row['content_html'] = $html_content;
    if (!empty($html_content)) {
        $entity_eml_row['has_html'] = 1;
    }
    $entity_eml_row['description'] = 5;

    $entity_eml_obj->set_row($entity_eml_row);
    echo 'update row 3: '.json_encode($entity_eml_row).' | '.json_encode($entity_eml_obj->id_group).PHP_EOL;
}
exit;

//    $entity_file->set(['row'=>$file_data_set]);
//    print_r($result);
print_r("\n".count($result)." files scanned. Execution Time: ".(microtime(1)-$start_stamp)."\n");