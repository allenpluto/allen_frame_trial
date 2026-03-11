<?php
    define('PATH_SITE_BASE', dirname(__DIR__).DIRECTORY_SEPARATOR);
    include('../system/config/config.php');
    $start_stamp = microtime(1);



    function get_files_by_folder($folder_path, $option = []) {
        set_time_limit(240);
//        $folder_path = 'D:\EXPORT_FTK\PS3520165_006_LC_ToshibaSatellite';
//        $files = scandir($folder_path);
        if (!is_array($option)) {
            $option = [];
        }
        $default_option = [
            'start_stamp'=>microtime(1),
            'overall_result_count'=>0,
            'recursive'=>false,
            'require_time'=>true,
            'require_size'=>true,
            'require_extension'=>true,
            'require_mime'=>true,
            'root'=>'C:\1\\'
        ];
        $option = array_merge($default_option, $option);
        $file_data_set = [];
        $default_file_row = [
            'name'=>'',
            'source_time'=>'0000-00-00 00:00:00',
            'is_dir'=>0,
            'file_size'=>0,
            'image_width'=>0,
            'image_height'=>0,
            'extension'=>'',
            'mime'=>'',
            'source_file'=>'',
            'source_root'=>$option['root'],
            'parent_id'=>0
        ];
        $default_fields = array_keys($default_file_row);
        foreach (scandir($option['root'].$folder_path) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $file_path = $option['root'].$folder_path.DIRECTORY_SEPARATOR.$file;

            $file_row = $default_file_row;
            $file_row['name'] = $file;
            $file_row['is_dir'] = is_dir($file_path)?1:0;
            $file_row['source_file'] = $folder_path.DIRECTORY_SEPARATOR.$file;

            /*if (!empty($option['require_time'])) {
                $file_row['source_time'] = date("Y-m-d H:i:s", @filemtime($file_path));
            }
            if (!empty($option['require_size'])) {
                $file_row['file_size'] = @filesize($file_path);
            }

            if (!empty($option['require_extension'])) {
                $file_path_info = pathinfo($file_path);
                if (!empty($file_path_info)) {
//                echo json_encode($file_path_info).PHP_EOL;
                    if (!empty($file_path_info['filename'])) {
                        $file_row['name'] = $file_path_info['filename'];
                    }
                    if (!empty($file_path_info['extension'])) {
                        $file_row['extension'] = $file_path_info['extension'];
                    }
                }
            }
            if (!empty($option['require_mime'])) {
                $file_row['mime'] = @mime_content_type($file_path);
            }*/
            if (!empty($option['parent_id'])) {
                $file_row['parent_id'] = $option['parent_id'];
            }
            $file_data_set[] = $file_row;
        }
        $entity_file_obj = new entity_file();
        $entity_file_obj->set(['row'=>$file_data_set,'fields'=>$default_fields]);
//        $entity_file_obj->set(['row'=>$file_data_set,'fields'=>['name','source_time','is_dir','file_size','extension','mime','source_file','parent_id']]);
//        $entity_file_obj->set(['row'=>$file_data_set,'fields'=>['name','source_time','is_dir','file_size','extension','source_file','parent_id']]);
//        $entity_file_obj->set(['row'=>$file_data_set,'fields'=>['name','source_time','is_dir','extension','mime','source_file','parent_id']]);

        $result_id_group = $entity_file_obj->id_group;

        foreach ($entity_file_obj->row as $file_row) {
            set_time_limit(240);
            $file_data_set2 = [];
            $field2 = $default_fields;
            $file_path = $option['root'].$file_row['source_file'];
            if (!empty($option['require_time'])) {
                $file_row['source_time'] = date("Y-m-d H:i:s", @filemtime($file_path));
            }
            if (!empty($option['require_size'])) {
                $file_row['file_size'] = @filesize($file_path);
            }

            if (empty($file_row['is_dir']) AND !empty($option['require_extension'])) {
                $file_path_info = pathinfo($file_path);
                if (!empty($file_path_info)) {
//                echo json_encode($file_path_info).PHP_EOL;
                    if (!empty($file_path_info['filename'])) {
                        $file_row['name'] = $file_path_info['filename'];
                    }
                    if (!empty($file_path_info['extension'])) {
                        $file_row['extension'] = $file_path_info['extension'];
                    }
                }
                $matches = [];
                $flag_name_match_pattern = preg_match('/(.*)\s(\d{8})(_\d)?$/',$file_row['name'],$matches);
                if (!empty($flag_name_match_pattern)) {
                    $file_row['source_time'] = preg_replace('/(\d{2})(\d{2})(\d{4})/','$3-$2-$1 00:00:00',$matches[2]);
                    $file_row['name'] = preg_replace('/\.'.$file_row['extension'].'/','$1',$matches[1]);
                }
//                echo "\n".$flag_name_match_pattern.' ['.$file_row['name'].'] ['.$file_row['source_time'].'] '.json_encode($matches);
            }
            if (!empty($option['require_mime'])) {
                $file_row['mime'] = @mime_content_type($file_path);
                if (preg_match('/^image/',$file_row['mime'])) {
                    $size = getimagesize($file_path);
                    $file_row['image_width'] = $size[0];
                    $file_row['image_height'] = $size[1];

//                    switch ($file_row['mime']) {
//                        case 'image/png':
//                            $file_row['extension'] = 'png';
//                            break;
//                        case 'image/jpeg':
//                            $file_row['extension'] = 'jpg';
//                            break;
//                        case 'image/gif':
//                            $file_row['extension'] = 'gif';
//                            break;
//                        case 'image/tiff':
//                            $file_row['extension'] = 'tif';
//                            break;
//                        case 'image/x-ms-bmp':
//                            $file_row['extension'] = 'bmp';
//                            break;
//                        case 'image/jp2':
//                            $file_row['extension'] = 'jp2';
//                            break;
//                        case 'image/x-icon':
//                            $file_row['extension'] = 'ico';
//                            break;
//                        case 'vnd.adobe.photoshop':
//                            $file_row['extension'] = 'bin';
//                            break;
//                        default:
//                    }
                }
            }
            /*if (!empty($file_row['extension'])) {
                foreach($entity_file_obj->file_type_extension_map as $file_type=>$extension_list) {
                    if (in_array($file_row['extension'], $extension_list)) {
                        try {
                            set_time_limit(240);
                            switch ($file_type) {
                                case 'office_word':
                                    $phpWord = \PhpOffice\PhpWord\IOFactory::load($file_path);
                                    $properties = $phpWord->getDocInfo();
                                    $file_row['meta_property'] = json_encode((array)$properties);

                                    $file_row['creator'] = $properties->getCreator();
                                    $file_row['created_date'] = date ('Y-m-d H:i:s', $properties->getCreated());
                                    $file_row['last_modified_by'] = $properties->getLastModifiedBy();
                                    $file_row['modified_date'] = date ('Y-m-d H:i:s', $properties->getModified());
                                    break;
                                case 'office_excel':
                                    $phpSpreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
                                    $properties = $phpSpreadsheet->getProperties();
                                    $file_row['meta_property'] = json_encode($properties);

                                    $file_row['creator'] = $properties->getCreator();
                                    $file_row['created_date'] = date ('Y-m-d H:i:s', $properties->getCreated());
                                    $file_row['last_modified_by'] = $properties->getLastModifiedBy();
                                    $file_row['modified_date'] = date ('Y-m-d H:i:s', $properties->getModified());
                                    break;
                                case 'office_powerpoint':
                                    $phpPresentation = \PhpOffice\PhpPresentation\IOFactory::load($file_path);
                                    $properties = $phpPresentation->getProperties();
                                    $file_row['meta_property'] = json_encode($properties);

                                    $file_row['creator'] = $properties->getCreator();
                                    $file_row['created_date'] = date ('Y-m-d H:i:s', $properties->getCreated());
                                    $file_row['last_modified_by'] = $properties->getLastModifiedBy();
                                    $file_row['modified_date'] = date ('Y-m-d H:i:s', $properties->getModified());
                                    break;
                            }
                        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
                            $file_row['meta_property'] = json_encode($e);

                            $file_row['creator'] = '';
                            $file_row['created_date'] = '';
                            $file_row['last_modified_by'] = '';
                            $file_row['modified_date'] = '';
                        }

                    }


                }
            }*/
            $file_data_set2[] = $file_row;
            $field2 = array_keys($file_row);
//            print_r('$file_data_set2->'.json_encode($file_data_set2));echo PHP_EOL;
//            print_r('$field2->'.json_encode($field2));echo PHP_EOL;
            $entity_file_obj2 = new entity_file();
            $entity_file_obj2->set(['row'=>[$file_row], 'fields'=>array_keys($file_row)]);
//            echo 'update result: ';print_r($entity_file_obj2);echo PHP_EOL;
        }

        print_r("\n".count($result_id_group)." files provided. Execution Time: ".(microtime(1)-$option['start_stamp'])." (".$option['root'].$folder_path.")"."<br>\n");

//        $entity_file_obj->get(['id_group'=>$result_id_group]);
//        $result = $entity_file_obj->row;
//
//
//        print_r("\n".count($result)." files scanned. Execution Time: ".(microtime(1)-$option['start_stamp'])." (".$option['root'].$folder_path.")"."<br>\n");
        $result = array_values($result_id_group);

        if (!empty($option['recursive'])) {
            foreach ($entity_file_obj->row as $current_file_row) {
                if (!empty($current_file_row['is_dir'])) {
                    $sub_file_option = $option;
                    $sub_file_option['parent_id'] = $current_file_row['id'];

                    $sub_file_row = get_files_by_folder($current_file_row['source_file'], $sub_file_option);
                    $result = array_merge($result, $sub_file_row);
                    $current_entity_file_obj = new entity_file([$current_file_row['id']]);
                    $current_entity_file_obj->update(['file_scanned'=>1]);
                }
            }

        }
        return $result;
    }

    $folder_path = '';
//    $result = get_files_by_folder($folder_path,['start_stamp'=>$start_stamp,'recursive'=>true,'require_size'=>false,'require_time'=>false,'require_mime'=>false]);
    $result = get_files_by_folder($folder_path,['start_stamp'=>$start_stamp,'recursive'=>true,'root'=>'D:\HPL1\source_file\HAL-TEL-APNTFSG-DATA-DRIVE\data']);

//    $entity_file->set(['row'=>$file_data_set]);
//    print_r($result);
    print_r("\n".count($result)." files scanned. Execution Time: ".(microtime(1)-$start_stamp)."\n");