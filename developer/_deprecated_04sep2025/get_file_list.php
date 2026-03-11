<?php
    define('PATH_SITE_BASE', dirname(__DIR__).DIRECTORY_SEPARATOR);
    include('../system/config/config.php');
    $start_stamp = microtime(1);

    $entity_file_obj = new entity_file();
//
//
//

    switch ($_GET['document_type']) {
        case 'mail':
            $entity_file_obj->get(['where'=>'extension IN (\'pst\',\'msg\',\'eml\')']);
            break;
        case 'image':
            $entity_file_obj->get(['where'=>'extension IN (\'jpg\',\'png\',\'gif\',\'bmp\',\'jpeg\',\'webp\')']);
            break;
        case 'document_powerpoint':
            $entity_file_obj->get(['where'=>'extension IN (\'pptx\',\'ppt\',\'ppsx\',\'ppsm\',\'ppam\',\'pptm\',\'potx\',\'potm\')']);
            break;
        case 'document_word':
            $entity_file_obj->get(['where'=>'extension IN (\'docx\', \'docm\', \'dotx\', \'dotm\', \'doc\', \'dot\', \'rtf\', \'xml\', \'odt\')']);
            break;
        case 'document_excel':
            $entity_file_obj->get(['where'=>'extension IN (\'xlsx\', \'xlsm\', \'xlsb\', \'xlam\', \'xltx\', \'xltm\', \'xls\', \'xlt\', \'xla\', \'xlm\', \'xlw\', \'odc\', \'ods\')']);
            break;
        case 'document_pdf':
            $entity_file_obj->get(['where'=>'extension IN (\'pdf\')']);
            break;
        case 'webpage':
            $entity_file_obj->get(['where'=>'extension IN (\'htm\', \'html\', \'mht\', \'mhtml\')']);
            break;
        default:
            echo 'Unknown document_type';
            exit();
    }

//    print_r(count($entity_file_obj->id_group));
//    echo '<html><head><base href="D:\EXPORT_FTK\"></head><body>';
//    $row = array_values($entity_file_obj->row);
//    for ($i=0;$i<100;$i++) {
//        $img_src = ltrim($row[$i]['source_file'],'\\');
//        echo '<a href="'.$img_src.'" target="_blank" style="display:inline-block;width:50px;height:50px;border:1px solid gray;border-radius:3px;margin:4px;"><img src="'.$img_src.'" style="display:block;max-width:50px;max-height:50px"></a>';
//    }
//    echo '</body></html>';
    header('Content-Type: text/csv');
//    header("Content-Disposition: attachment; filename=image.csv");
//    header("Content-Disposition: attachment; filename=document_word.csv");
//    header("Content-Disposition: attachment; filename=document_excel.csv");
//    header("Content-Disposition: attachment; filename=webpage.csv");
    header('Content-Disposition: attachment; filename='.$_GET['document_type'].'.csv');


    $data_set = array_values($entity_file_obj->row);
    echo '"E:\FTK Extracted","File Name","File Extension","File Size","Last Modified"'.PHP_EOL;
    foreach ($data_set as $row) {
        $target_file_name = '';
        $output_row = '"=HYPERLINK($A$1&""'.$row['source_file'].'"")","'.$row['name'].'","'.$row['extension'].'","'.number_format($row['file_size'] / 1048576, 2).'","'.date('j M y H:i',strtotime($row['source_time'])).'"';
        if ($_GET['document_type'] == 'document_pdf') {
            $target_file_name = date('YmdHis',strtotime($row['source_time'])).'_'.$row['name'].'.'.$row['extension'];
            if (file_exists('D:\FTK Extracted\PDF\\'.$target_file_name)) {
                $output_row .= ',"=HYPERLINK($A$1&""'.'\PDF\\'.$target_file_name.'"")"';
            }
        }
        $output_row .= PHP_EOL;
        echo $output_row;
    }
