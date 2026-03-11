<?php
    define('PATH_SITE_BASE', dirname(__DIR__).DIRECTORY_SEPARATOR);
    include('../system/config/config.php');
    $start_stamp = microtime(1);

//
//
//
    $option = [
        'source_path'=>'C:\scan_files\halikos_group_1',
        'target_path'=>'C:\scan_files\halikos_group_1',
        'csv_root_path'=>'C:\scan_files\halikos_group_1',
//        'document_type_list'=>['full_list','mail','image','document_word','document_excel','document_pdf','webpage']
//        'document_type_list'=>['document_word','document_excel','document_pdf','webpage']
        'document_type_list'=>['image']
    ];

    foreach ($option['document_type_list'] as $document_type) {
        set_time_limit(0);
        $entity_file_obj = new entity_file();
        switch ($document_type) {
            case 'full_list':
                $entity_file_obj->get(['where'=>'file_size > 0 AND is_dir = 0']);
                break;
            case 'mail':
                $entity_file_obj->get(['where'=>'extension IN (\'ost\',\'pst\',\'msg\',\'eml\')']);
                break;
            case 'image':
//                $entity_file_obj->get(['where'=>'extension IN (\'jpg\',\'jpeg\',\'png\',\'pngx\',\'gif\',\'tif\',\'tiff\',\'bmp\',\'webp\')']);
                $entity_file_obj->get(['where'=>'mime LIKE "image%"']);
                break;
            case 'image_wrong_size':
                $entity_file_obj->get(['where'=>'mime LIKE "image%" AND image_width > 500000000']);
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
        $csv_file_path = $option['csv_root_path'].DIRECTORY_SEPARATOR.$document_type.'.csv';
        $data_set = array_values($entity_file_obj->row);
        $output_row = '"=LEFT(CELL(""filename""),FIND(""'.$document_type.'.csv'.'"",CELL(""filename""))-2)","File Name","File Extension","File Size","Last Modified"';
        switch ($document_type) {
            case 'image':
                $output_row .= ',"Image Width","Image Height"';
            case 'document_word':
            case 'document_excel':
            case 'document_pdf':
                $output_row .= ',"Organised File Path"';
                break;
        }
        $output_row .= PHP_EOL;
        file_put_contents($csv_file_path,$output_row);
        foreach ($data_set as $row) {
//            $output_row = '"=HYPERLINK($A$1 & ""'.$row['source_file'].'"")","'.$row['name'].'","'.$row['extension'].'","'.number_format($row['file_size'] / 1048576, 2).'","'.date('j M y H:i',strtotime($row['source_time'])).'"';
            $output_row = '"=$A$1 & ""'.$row['source_file'].'""","=HYPERLINK(OFFSET(INDIRECT(""R""&ROW()&""C""&COLUMN(),FALSE),,-1),""'.$row['name'].'"")","'.$row['extension'].'","'.number_format($row['file_size'] / 1048576, 2).'","'.date('j M y H:i',strtotime($row['source_time'])).'"';
            if (in_array($document_type,['image','image_wrong_size','document_word','document_excel','document_pdf'])) {
                $target_file_folder = $option['target_path'].DIRECTORY_SEPARATOR.$document_type;
                if ($document_type === 'image' || $document_type === 'image_wrong_size') {
                    $output_row .= ',"'.$row['image_width'].'","'.$row['image_height'].'"';
                    if ($row['image_width'] > 500 AND $row['image_height'] > 500) {
                        if ($row['image_width'] > 500000000) {
                            $target_file_folder .= '_size_undefined';
                        }
                    } else {
                        if ($row['image_width'] > 100 AND $row['image_height'] > 100) {
                            $target_file_folder .= '_100-500';
                        } else {
                            $target_file_folder .= '_lt100';
                        }
                    }
                    switch ($row['mime']) {
                        case 'image/png':
                            $row['extension'] = 'png';
                            break;
                        case 'image/jpeg':
                            $row['extension'] = 'jpg';
                            break;
                        case 'image/gif':
                            $row['extension'] = 'gif';
                            break;
                        case 'image/tiff':
                            $row['extension'] = 'tif';
                            break;
                        case 'image/x-ms-bmp':
                            $row['extension'] = 'bmp';
                            break;
//                        case 'image/jp2':
//                            $row['extension'] = 'jp2';
//                            break;
//                        case 'image/x-icon':
//                            $row['extension'] = 'ico';
//                            break;
//                        case 'vnd.adobe.photoshop':
//                            $row['extension'] = 'bin';
//                            break;
                        default:
                            continue 2;
                    }
                }
                if (!file_exists($target_file_folder)) mkdir($target_file_folder, 0755, true);
                $target_file_name = date('Ymd',strtotime($row['source_time'])).'_'.$row['name'].'.'.$row['extension'];
                $duplicate_counter = 0;
                while (file_exists($target_file_folder.DIRECTORY_SEPARATOR.$target_file_name)) {
                    $target_file_name = date('Ymd',strtotime($row['source_time'])).'_'.$row['name'].'_'.++$duplicate_counter.'.'.$row['extension'];
                }
//                if ($document_type === 'image') {
//                    $output_row .= ',"'.$row['image_width'].'","'.$row['image_height'].'"';
//                    if ($row['image_width'] > 500 AND $row['image_height'] > 500) {
//                        @copy($option['source_path'].$row['source_file'],$target_file_folder.DIRECTORY_SEPARATOR.$target_file_name);
//                    }
//                } else {
//                    @copy($option['source_path'].$row['source_file'],$target_file_folder.DIRECTORY_SEPARATOR.$target_file_name);
//                }
                @copy($option['source_path'].$row['source_file'],$target_file_folder.DIRECTORY_SEPARATOR.$target_file_name);
                if (file_exists($target_file_folder.DIRECTORY_SEPARATOR.$target_file_name)) {
                    $output_row .= ',"=HYPERLINK($A$1&""'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Result'.str_replace($option['target_path'],'',$target_file_folder).DIRECTORY_SEPARATOR.$target_file_name.'"")"';
                }
            }
            $output_row .= PHP_EOL;
            file_put_contents($csv_file_path,$output_row,FILE_APPEND);
        }
        print_r("\n".$csv_file_path." generated (".count($data_set)." rows). Execution Time: ".(microtime(1)-$start_stamp)."<br>\n");

    }
    print_r("\nAll Done. Execution Time: ".(microtime(1)-$start_stamp)."<br>\n");

