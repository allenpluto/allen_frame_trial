<?php
error_reporting(0);
define('PATH_SITE_BASE', dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR);
include('../../system/config/config.php');

$result = ['success'=>true,'updated_data'=>[]];
$start_stamp = microtime(1);



    $option = [
        'source_path'=>'D:\HPL1',
        'pdf_source_path'=>'D:\HPL1\eml_pdf',
        'target_path'=>'C:\temp\Sample_EML',
        'limit'=>100,
    ];

if (!empty($_REQUEST['limit'])) {
    $option['limit'] = $_REQUEST['limit'];
}
if (!empty($_REQUEST['start_time'])) {
    $result['start_time'] = $_REQUEST['start_time'];
}

    function csv_headline()
    {
        $output_row = '"=LEFT(CELL(""filename""),LEN(CELL(""filename""))-10)&""\\eml_pdf\\""","Source File","FROM","TO","REPLY_TO","CC","BCC","Date","ATTACHMENTS"';

        return $output_row;
    }

    function csv_row($file_row)
    {
        $output_row = '"=HYPERLINK($A$1 & ""'.$file_row['target_file'].'"",$A$1 & ""'.$file_row['target_file'].'"")","'.$file_row['source_file'].'"';
        foreach (['from','to','reply_to','cc','bcc'] as $key) {
            $output_row .= ',"'.str_replace('"','""',$file_row[$key]).'"';
        }
        $output_row .= ',"'.(empty($file_row['date'])?'':date('j M y H:i',strtotime($file_row['date']))).'"';
        if (!empty($file_row['attachments'])) {
            $attachments = json_decode($file_row['attachments'], true);
            if (is_array($attachments)) {
                foreach ($attachments as $attachment) {
                    $output_row .= ',"=HYPERLINK($A$1 & ""'.$attachment['new_name'].'"",""'.$attachment['name'].'"")"';
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

$csv_file_path = $option['target_path'].DIRECTORY_SEPARATOR.'index.csv';
$entity_eml_obj = new entity_eml(null, ['table'=>'tbl_entity_eml_halikos_group_1']);

echo '<pre>';
$output_row = csv_headline();
file_put_contents($csv_file_path,$output_row);

if (!file_exists($option['target_path'].DIRECTORY_SEPARATOR.'eml_pdf')) mkdir($option['target_path'].DIRECTORY_SEPARATOR.'eml_pdf', 0755, true);

$entity_eml_obj->get(['where'=>'description = 6 AND subject = "" AND attachments = ""','limit'=>Round($option['limit']/10,0),'order'=>['RAND()']]);
foreach ($entity_eml_obj->row as $file_row) {
    $output_row = csv_row($file_row);
    file_put_contents($csv_file_path,$output_row,FILE_APPEND);

    echo PHP_EOL.$option['pdf_source_path'].DIRECTORY_SEPARATOR.$file_row['target_file'].PHP_EOL.$option['target_path'].DIRECTORY_SEPARATOR.'eml_pdf'.DIRECTORY_SEPARATOR.$file_row['target_file'].PHP_EOL;

    copy($option['pdf_source_path'].DIRECTORY_SEPARATOR.$file_row['target_file'],$option['target_path'].DIRECTORY_SEPARATOR.'eml_pdf'.DIRECTORY_SEPARATOR.$file_row['target_file']);

}

$entity_eml_obj->get(['where'=>'description = 6 AND subject = "" AND attachments LIKE "%new_name%"','limit'=>Round($option['limit']/10,0),'order'=>['RAND()']]);
foreach ($entity_eml_obj->row as $file_row) {
    $output_row = csv_row($file_row);
    file_put_contents($csv_file_path,$output_row,FILE_APPEND);

    copy($option['pdf_source_path'].DIRECTORY_SEPARATOR.$file_row['target_file'],$option['target_path'].DIRECTORY_SEPARATOR.'eml_pdf'.DIRECTORY_SEPARATOR.$file_row['target_file']);
    if (!empty($file_row['attachments'])) {
        $attachments = json_decode($file_row['attachments'], true);
        if (is_array($attachments)) {
            foreach ($attachments as $attachment) {
                print_r($attachment);
                copy($option['pdf_source_path'].DIRECTORY_SEPARATOR.$attachment['new_name'],$option['target_path'].DIRECTORY_SEPARATOR.'eml_pdf'.DIRECTORY_SEPARATOR.$attachment['new_name']);
            }
        }
    }
}

$entity_eml_obj->get(['where'=>'description = 6 AND subject <> "" AND attachments LIKE "%new_name%"','limit'=>Round($option['limit']*8/10,0),'order'=>['RAND()']]);
foreach ($entity_eml_obj->row as $file_row) {
    $output_row = csv_row($file_row);
    file_put_contents($csv_file_path,$output_row,FILE_APPEND);

    copy($option['pdf_source_path'].DIRECTORY_SEPARATOR.$file_row['target_file'],$option['target_path'].DIRECTORY_SEPARATOR.'eml_pdf'.DIRECTORY_SEPARATOR.$file_row['target_file']);
    if (!empty($file_row['attachments'])) {
        $attachments = json_decode($file_row['attachments'], true);
        if (is_array($attachments)) {
            foreach ($attachments as $attachment) {
                print_r($attachment);
                copy($option['pdf_source_path'].DIRECTORY_SEPARATOR.$attachment['new_name'],$option['target_path'].DIRECTORY_SEPARATOR.'eml_pdf'.DIRECTORY_SEPARATOR.$attachment['new_name']);
            }
        }
    }
}

//print_r("\n".$pdf_count." out of ".$total_count." pdf files generated. Execution Time: ".(microtime(1)-$start_stamp)."\n");
$result['execution_time'] = microtime(1)-$start_stamp;
echo json_encode($result);