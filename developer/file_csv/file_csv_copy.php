<?php
error_reporting(0);
define('PATH_SITE_BASE', dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR);
include('../../system/config/config.php');

$result = ['success'=>true,'updated_data'=>[]];
$start_stamp = microtime(1);



    $option = [
        'source_path'=>'C:\Peter\2',
        'pdf_source_path'=>'C:\Peter\result\Peter\2',
        'target_path'=>'C:\temp\Sample',
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
        $output_row = '"=LEFT(CELL(""filename""),LEN(CELL(""filename""))-10)&""\\""","PDF File","Source File","File Extension","File Size","Last Modified","Width","Height","Creator","Last Modified By","Created Date","Last Modified Date"';

        return $output_row;
    }

    function csv_row($file_row)
    {
        $output_row = '"=HYPERLINK($A$1 & ""'.$file_row['target_file'].'"",$A$1 & ""'.$file_row['target_file'].'"")","'.(empty($file_row['pdf_file'])?'':'=HYPERLINK($A$1 & ""'.$file_row['pdf_file'].'"",$A$1 & ""'.$file_row['pdf_file'].'"")').'","'.$file_row['source_file'].'","'.$file_row['extension'].'","'.number_format($file_row['file_size'] / 1048576, 2).'","'.date('j M y H:i',strtotime($file_row['source_time'])).'","'.$file_row['image_width'].'","'.$file_row['image_height'].'","'.$file_row['creator'].'","'.$file_row['last_modified_by'].'","'.date('j M y H:i',strtotime($file_row['created_date'])).'","'.date('j M y H:i',strtotime($file_row['modified_date'])).'"';

        return PHP_EOL.$output_row;
    }

//$ss = 'C:\Peter\result\Peter\2\excel_pdf\Copy of DRW EFA H ON SMIT_DALBYxHPL2xHOSx23122021.pdf';
//$st = 'C:\temp\Sample\excel_pdf\Copy of DRW EFA H ON SMIT_DALBYxHPL2xHOSx23122021.pdf';
//if (!file_exists(dirname($st))) mkdir(dirname($st), 0755, true);
//copy($ss,$st);
//exit;

$csv_file_path = $option['target_path'].DIRECTORY_SEPARATOR.'index.csv';
$entity_file_obj = new entity_file();
echo '<pre>';
$output_row = csv_headline();
file_put_contents($csv_file_path,$output_row);
foreach($entity_file_obj->file_type_extension_map as $file_type=>$extension_list) {
    $entity_file_obj2 = new entity_file(null, ['table'=>'tbl_entity_file_halikos_group_2']);
    $entity_file_obj2->get(['where'=>'target_file <> "" AND extension IN ("'.implode('", "',$extension_list).'")','limit'=>$option['limit'],'order'=>['RAND()']]);

    echo $file_type.PHP_EOL;

    foreach ($entity_file_obj2->row as $file_row) {
        if (file_exists($option['source_path'].DIRECTORY_SEPARATOR.$file_row['target_file'])) {
            if (!file_exists(dirname($option['target_path'].DIRECTORY_SEPARATOR.$file_row['target_file']))) mkdir(dirname($option['target_path'].DIRECTORY_SEPARATOR.$file_row['target_file']), 0755, true);
            copy($option['source_path'].DIRECTORY_SEPARATOR.$file_row['target_file'],$option['target_path'].DIRECTORY_SEPARATOR.$file_row['target_file']);
        }
        $file_row['pdf_file'] = '';
        if (in_array($file_type,['office_word','office_excel','office_powerpoint'])) {
            $file_name_parts = explode('.',$file_row['target_file']);
            array_pop($file_name_parts);
            $file_name_parts[] = 'pdf';
            array_unshift($file_name_parts, str_replace(['office_word','office_excel','office_powerpoint'],['word_pdf','excel_pdf','powerpoint_pdf'],array_shift($file_name_parts)));
            $file_row['pdf_file'] = implode('.', $file_name_parts);

            $sf = $option['pdf_source_path'].DIRECTORY_SEPARATOR.$file_row['pdf_file'];
            $tf = $option['target_path'].DIRECTORY_SEPARATOR.$file_row['pdf_file'];
            if (!empty($file_row['pdf_file']) AND file_exists($sf)) {
                if (!file_exists(dirname($tf))) mkdir(dirname($tf), 0755, true);
                copy($sf,$tf);
            }
        }
        if ($file_type === 'pdf') {
            $file_row['pdf_file'] = $file_row['target_file'];
        }

        if ($file_row['modified_date'] === '0000-00-00 00:00:00' OR $file_row['modified_date'] === '1970-01-01 00:00:00') {
            $file_row['modified_date'] = '';
        }
        if ($file_row['created_date'] === '0000-00-00 00:00:00' OR $file_row['created_date'] === '1970-01-01 00:00:00') {
            $file_row['created_date'] = '';
        }

        print_r($file_row);

        $output_row = csv_row($file_row);
        file_put_contents($csv_file_path,$output_row,FILE_APPEND);
    }

//    echo $file_type.PHP_EOL;
//    print_r($entity_file_obj2->row);
}

//print_r("\n".$pdf_count." out of ".$total_count." pdf files generated. Execution Time: ".(microtime(1)-$start_stamp)."\n");
$result['execution_time'] = microtime(1)-$start_stamp;
echo json_encode($result);