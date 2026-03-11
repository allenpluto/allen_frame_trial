<?php
    define('PATH_SITE_BASE', dirname(__DIR__).DIRECTORY_SEPARATOR);
    include('../system/config/config.php');
    $start_stamp = microtime(1);

    $source_root_path = 'D:\FTK Extracted';
    $target_root_path = 'D:\FTK Extracted';
    $entity_file_obj = new entity_file();
//
//
//

    $entity_file_obj->get(['where'=>'extension IN (\'pdf\')']);


//    print_r(count($entity_file_obj->id_group));
//    echo '<html><head><base href="D:\EXPORT_FTK\"></head><body>';
//    $row = array_values($entity_file_obj->row);
//    for ($i=0;$i<100;$i++) {
//        $img_src = ltrim($row[$i]['source_file'],'\\');
//        echo '<a href="'.$img_src.'" target="_blank" style="display:inline-block;width:50px;height:50px;border:1px solid gray;border-radius:3px;margin:4px;"><img src="'.$img_src.'" style="display:block;max-width:50px;max-height:50px"></a>';
//    }
//    echo '</body></html>';
//    header('Content-Type: text/csv');
//    header("Content-Disposition: attachment; filename=image.csv");
//    header("Content-Disposition: attachment; filename=document_word.csv");
//    header("Content-Disposition: attachment; filename=document_excel.csv");
//    header("Content-Disposition: attachment; filename=webpage.csv");
//    header('Content-Disposition: attachment; filename='.$_GET['document_type'].'.csv');


    $data_set = array_values($entity_file_obj->row);
//    echo '"E:\FTK Extracted","File Name","File Extension","File Size","Last Modified"'.PHP_EOL;
    foreach ($data_set as $row) {
        set_time_limit(120);
        $target_file_name = '';
        $target_file_name = date('YmdHis',strtotime($row['source_time'])).'_'.$row['name'].'.'.$row['extension'];
        if (!file_exists($target_root_path.'\PDF\\'.$target_file_name)) {
            copy($source_root_path.$row['source_file'],$target_root_path.'\PDF\\'.$target_file_name);
        }
        echo $source_root_path.$row['source_file'].' ---> '.$target_root_path.'\pdf\\'.$target_file_name.' ('.(file_exists($target_root_path.'\pdf\\'.$target_file_name.PHP_EOL)?'Success':'Failed').')<br>'.PHP_EOL;
//        echo '"=HYPERLINK(A$1&""'.$row['source_file'].'"")","'.$row['name'].'","'.$row['extension'].'","'.number_format($row['file_size'] / 1048576, 2).'","'.date('j M y H:i',strtotime($row['source_time'])).'","'.$target_file_name.'"'.PHP_EOL;
    }
