<?php
error_reporting(0);
define('PATH_SITE_BASE', dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR);
include('../../system/config/config.php');

$result = ['success'=>true,'updated_data'=>[]];
$start_stamp = microtime(1);



    $option = [
        'source_path'=>'D:\HPL2',
        'pdf_source_path'=>'D:\HPL2\eml_pdf',
        'target_path'=>'D:\HPL2',
//        'db_table'=>'tbl_entity_eml_halikos_group_1',
        'db_table'=>'tbl_entity_eml_halikos_group_2',
//        'csv_file_name'=>'index_eml',
        'csv_file_name'=>'index_eml_office_doc_to_pdf',
        'limit'=>100,
        'offset'=>0,
    ];

if (!empty($_REQUEST['limit'])) {
    $option['limit'] = $_REQUEST['limit'];
}
if (!empty($_REQUEST['start_time'])) {
    $result['start_time'] = $_REQUEST['start_time'];
}

    function csv_headline()
    {
        global $option;
        $output_row = '"=SUBSTITUTE(CELL(""filename""),""'.$option['csv_file_name'].'.csv"","""")","Status","PDF File","FROM","TO","REPLY_TO","CC","BCC","Date","ATTACHMENTS"';

        return $output_row;
    }

    function csv_row($file_row)
    {
        global $option;
        $output_row = '"=HYPERLINK($A$1 & ""pst_eml'.$file_row['source_file'].'"",$A$1 & ""pst_eml'.$file_row['source_file'].'"")","'.$file_row['status'].'"';
        if (!empty($file_row['target_file'])) {
            $output_row .= ',"=HYPERLINK($A$1 & ""'.$file_row['target_folder'].$file_row['target_file'].'"",$A$1 & ""'.$file_row['target_folder'].$file_row['target_file'].'"")"';
        } else {
            $output_row .= '," "';
        }

        foreach (['from','to','reply_to','cc','bcc'] as $key) {
            $output_row .= ',"'.str_replace('"','""',$file_row[$key]).'"';
        }
        $output_row .= ',"'.(empty($file_row['date'])?'':date('j M y H:i',strtotime($file_row['date']))).'"';
        if (!empty($file_row['target_file'])) {
            if (!empty($file_row['attachments'])) {
                $attachments = json_decode($file_row['attachments'], true);
                if (is_array($attachments)) {
                    $target_file_name = preg_replace('/^(.*?)(_x\d\d)?\.pdf$/','$1',$file_row['target_file']);

                    foreach ($attachments as $attachment_index=>$attachment) {
                        if ($option['csv_file_name'] == 'index_eml_office_doc_to_pdf') {
                            $attachment_extension = '';
                            if  (strpos($attachment['name'], '.')!==false){
                                $attachment_name_parts = explode('.',$attachment['name']);
                                $attachment_extension = end($attachment_name_parts);
                                if (strlen($attachment_extension) > 4 OR preg_match('/([^- \.a-zA-Z0-9]+)/',$attachment_extension)) {
                                    $attachment_extension = 'eml';
                                }
                            }
                            $attachment_extension = strtolower($attachment_extension);

                            if (file_exists($option['target_path'].DIRECTORY_SEPARATOR.$file_row['target_folder'].$target_file_name.'_ATTCH_'.strtolower($attachment_extension).sprintf('%02d', $attachment_index+1).'.pdf')) {
                                $attachment['new_name'] = $target_file_name.'_ATTCH_'.$attachment_extension.sprintf('%02d', $attachment_index+1).'.pdf';
                            }
                        }
                        if (!isset($attachment['new_name'])) {
                            $output_row .= ',"'.$attachment['name'].'"';
                        } else {
                            $output_row .= ',"=HYPERLINK($A$1 & ""'.$file_row['target_folder'].$attachment['new_name'].'"",""'.$attachment['name'].'"")"';
                        }
//
//                        if ($option['csv_file_name'] != 'index_eml_office_doc_to_pdf') {
//                            if (file_exists($option['target_path'].DIRECTORY_SEPARATOR.$file_row['target_folder'].$target_file_name.'_ATTCH_'.strtolower($attachment_extension).sprintf('%02d', $attachment_index+1).'.'.$attachment_extension)) {
//                                $attachment['new_name'] = $target_file_name.'_ATTCH_'.$attachment_extension.sprintf('%02d', $attachment_index+1).'.'.$attachment_extension;
//                                $output_row .= ',"=HYPERLINK($A$1 & ""'.$file_row['target_folder'].$attachment['new_name'].'"",""'.$attachment['name'].'"")"';
//                            } else {
//                                $output_row .= ',"'.$attachment['name'].'"';
//                            }
//                        } else {
//                            if (file_exists($option['target_path'].DIRECTORY_SEPARATOR.$file_row['target_folder'].$target_file_name.'_ATTCH_'.strtolower($attachment_extension).sprintf('%02d', $attachment_index+1).'.pdf')) {
//                                $attachment['new_name'] = $target_file_name.'_ATTCH_'.$attachment_extension.sprintf('%02d', $attachment_index+1).'.pdf';
//                                $output_row .= ',"=HYPERLINK($A$1 & ""'.$file_row['target_folder'].$attachment['new_name'].'"",""'.$attachment['name'].'"")"';
//                            } else {
//                                $output_row .= ',"'.$attachment['name'].'"';
//                            }
//                        }
                    }
//                    foreach ($attachments as $attachment) {
//                        if (!isset($attachment['new_name'])) {
//                            $output_row .= ',"'.$attachment['name'].'"';
//                        } else {
//                            $output_row .= ',"=HYPERLINK($A$1 & ""'.$file_row['target_folder'].$attachment['new_name'].'"",""'.$attachment['name'].'"")"';
//                        }
//
//                    }
                }
            }
        }


        return PHP_EOL.$output_row;
    }

//$ss = 'C:\Peter\result\Peter\2\excel_pdf\Copy of DRW EFA H ON SMIT_DALBYxHPL2xHOSx23122021.pdf';
//$st = 'C:\temp\Sample\excel_pdf\Copy of DRW EFA H ON SMIT_DALBYxHPL2xHOSx23122021.pdf';
//if (!file_exists(dirname($st))) mkdir(dirname($st), 0755, true);
//copy($ss,$st);
//exit;

$csv_file_path = $option['target_path'].DIRECTORY_SEPARATOR.$option['csv_file_name'].'.csv';
$entity_eml_obj = new entity_eml(null, ['table'=>$option['db_table']]);

echo '<pre>';
if (!file_exists($csv_file_path)) {
    $output_row = csv_headline();
    file_put_contents($csv_file_path,$output_row);
    $row_count = 1;
} else {
    $lines = file($csv_file_path);
    $row_count = count($lines);
    $option['offset'] = max(0,$row_count-1);
//    print_r($option);
}

$entity_eml_obj->get(['where'=>'description != 0','limit'=>$option['limit'],'offset'=>$option['offset']]);
foreach ($entity_eml_obj->row as $eml_row) {
    switch ($eml_row['description']) {
        case -1:
            $eml_row['status'] = 'Failed to retrieve mail text content';
            break;
        case -2:
            $eml_row['status'] = 'Failed to retrieve attachment list';
            break;
        case -3:
            $eml_row['status'] = 'Failed to retrieve mail header info';
            break;
        case -4:
            $eml_row['status'] = 'Failed to retrieve mail HTML content';
            break;
        case -5:
        case -6:
            $eml_row['status'] = 'Failed to generate PDF from eml record';
            break;
        case 6:
        case 7:
    }

    if (!empty($eml_row['target_file'])) {
        $file_name_parts = explode('\\', $eml_row['source_file']);
        $eml_row['target_folder'] = 'eml_pdf'.DIRECTORY_SEPARATOR;
        if (count($file_name_parts) > 1) {
            array_shift($file_name_parts);
            $eml_row['target_folder'] .= array_shift($file_name_parts).DIRECTORY_SEPARATOR;
        }
        if (!file_exists($option['target_path'].DIRECTORY_SEPARATOR.$eml_row['target_folder'].$eml_row['target_file'])) {
            // If target file cannot be found, it is probably renamed e.g. x -Jason Qian- => x Jason Qian

            $file_post_fix = preg_replace('/^(.*?)(_x\d\d)?\.pdf$/','$2.pdf',$eml_row['target_file']);

            // Go through the name build process again
            $target_file_name = '';
            $source_file_parts = explode('\\', str_replace('/','\\',$eml_row['source_file']));
            foreach ($source_file_parts as $source_file_part) {
                if (strpos($source_file_part, '@') !== false Or preg_match('/\.pst$/',$source_file_part)) {
                    $target_file_name = $source_file_part;
                    break;
                }
            }
            if (empty($target_file_name)) {
                $target_file_name = 'other';
            }

            if (!empty($target_file_name)) {
                $target_file_name = ' x ('.substr($target_file_name,0,12).')';
            }

            if (!empty($eml_row['subject'])) {
                $subject = preg_replace('/[-]+/','-',preg_replace('/([^- \.a-zA-Z0-9]+)/','-',$eml_row['subject']));
//        echo 'subject(without special chars): '.$subject.PHP_EOL;
                $subject = ' '.substr($subject,0,30);
                $target_file_name = $subject.$target_file_name;
            } else {
                $target_file_name = ' '.sprintf('%08d', $eml_row['id']).$target_file_name;
            }

//    echo '$target_file_name: '.$target_file_name.PHP_EOL;

            foreach (['to', 'from'] as $type) {
//            if (empty($eml_row[$type])) continue;
                $mail_list = explode(',',$eml_row[$type]);
//        echo '$mail_list('.$type.'): '.htmlentities(implode(', ',$mail_list)).PHP_EOL;
                if (count($mail_list)>1) {
                    $first_mail = $mail_list[0];
                } else {
                    $first_mail = $eml_row[$type];
                }
                if (preg_match('/(.*)\<(.*)\>/',$first_mail,$matches)) {
//            echo '$first_mail: '.$first_mail.PHP_EOL;
//            echo '$matches: '.htmlentities(print_r($matches,true)).PHP_EOL;
                    $first_mail = $matches[2];
                }

//        echo '$first_mail: '.$first_mail.PHP_EOL;
                if (strpos($first_mail, '@')!==false) {
                    $first_mail = trim($first_mail, ' \'"');
                    $target_file_name = str_pad(substr($first_mail,0,12),12,'-').$target_file_name;
                } else {
                    $target_file_name = str_pad(substr(preg_replace('/[ ]+/',' ',preg_replace('/([^ \.a-zA-Z0-9]+)/','',$eml_row[$type])),0,12),12,'-').$target_file_name;
                }
                $target_file_name = ' x '.$target_file_name;
                if ($type === 'to') {
                    $target_file_name = ' to '.$target_file_name;
                }
//        echo '$target_file_name: '.$target_file_name.PHP_EOL;
            }
            if (!empty($eml_row['date'])) {
                $target_file_name = date('Ymd Hi', strtotime($eml_row['date'])).$target_file_name;
            }

            $eml_row['target_file'] = $target_file_name.$file_post_fix;
        }

        if (file_exists($option['target_path'].DIRECTORY_SEPARATOR.$eml_row['target_folder'].$eml_row['target_file'])) {
            $eml_row['status'] = 'Successfully convert eml record to PDF';

            /*if (!empty($file_row['attachments'])) {
                $attachments = json_decode($file_row['attachments'], true);
                if (is_array($attachments)) {
                    foreach ($attachments as $attachment_index=>$attachment) {
                        $attachment_extension = '';
                        if  (strpos($attachment['name'], '.')!==false){
                            $attachment_name_parts = explode('.',$attachment['name']);
                            $attachment_extension = end($attachment_name_parts);
                            if (strlen($attachment_extension) > 4 OR preg_match('/([^- \.a-zA-Z0-9]+)/',$attachment_extension)) {
                                $attachment_extension = 'eml';
                            }
                        }
                        $attachment_extension = strtolower($attachment_extension);
                        $attachments[$attachment_index]['extension'] = $attachment_extension;
                        $target_file_name = preg_replace('/^(.*?)(_x\d\d)?\.pdf$/','$1',$eml_row['target_file']);
                        if ($option['csv_file_name'] != 'index_eml_office_doc_to_pdf') {
                            if (file_exists($target_file_name.'_ATTCH_'.strtolower($attachment_extension).sprintf('%02d', $attachment_index+1).'.'.$attachment_extension)) {
                                $attachments[$attachment_index]['new_name'] = $target_file_name.'_ATTCH_'.$attachment_extension.sprintf('%02d', $attachment_index+1).'.'.$attachment_extension;
                            }
                        } else {
                            if (file_exists($target_file_name.'_ATTCH_'.strtolower($attachment_extension).sprintf('%02d', $attachment_index+1).'.pdf')) {
                                $attachments[$attachment_index]['new_name'] = $target_file_name.'_ATTCH_'.$attachment_extension.sprintf('%02d', $attachment_index+1).'.pdf';
                            }
                        }

                    }
                }
                $file_row['attachments'] = json_encode($attachments);
            }*/

        } else {
            if (empty($eml_row['status'])) {
                $eml_row['status'] = 'Failed to generate PDF';
            }

            $eml_row['target_file'] = '';
        }
    }



    $output_row = csv_row($eml_row);
    file_put_contents($csv_file_path,$output_row,FILE_APPEND);

    echo PHP_EOL.$option['pdf_source_path'].DIRECTORY_SEPARATOR.$eml_row['target_file'].PHP_EOL.$option['target_path'].DIRECTORY_SEPARATOR.'eml_pdf'.DIRECTORY_SEPARATOR.$eml_row['target_file'].PHP_EOL;

//    copy($option['pdf_source_path'].DIRECTORY_SEPARATOR.$file_row['target_file'],$option['target_path'].DIRECTORY_SEPARATOR.'eml_pdf'.DIRECTORY_SEPARATOR.$file_row['target_file']);

}

//print_r("\n".$pdf_count." out of ".$total_count." pdf files generated. Execution Time: ".(microtime(1)-$start_stamp)."\n");
$result['execution_time'] = microtime(1)-$start_stamp;
echo json_encode($result);