<?php
error_reporting(0);
define('PATH_SITE_BASE', dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR);
include('../../system/config/config.php');

$result = ['success'=>true,'updated_data'=>[]];
$start_stamp = microtime(1);



    $option = [
        'target_path'=>'D:\HPL2',
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
        $output_row = '"=SUBSTITUTE(CELL(""filename""),""index.csv"","""")","Source File Link","Renamed File","PDF File","Source File Extension","File Size","Last Modified","Width","Height","Creator","Last Modified By","Created Date","Last Modified Date"';

        return $output_row;
    }

    function csv_row($file_row, $file_row_index)
    {
        $output_row = '"=$A$1 & ""source_file\"" & ""'.$file_row['source_file'].'""","=HYPERLINK($A'.$file_row_index.',""Link"")","'.(empty($file_row['target_file'])?'':'=HYPERLINK($A$1 & ""'.$file_row['target_file'].'"",$A$1 & ""'.$file_row['target_file'].'"")').'","'.(empty($file_row['pdf_file'])?'':'=HYPERLINK($A$1 & ""'.$file_row['pdf_file'].'"",$A$1 & ""'.$file_row['pdf_file'].'"")').'","'.$file_row['extension'].'","'.number_format($file_row['file_size'] / 1048576, 2).'","'.date('j M y H:i',strtotime($file_row['source_time'])).'","'.$file_row['image_width'].'","'.$file_row['image_height'].'","'.$file_row['creator'].'","'.$file_row['last_modified_by'].'","'.date('j M y H:i',strtotime($file_row['created_date'])).'","'.date('j M y H:i',strtotime($file_row['modified_date'])).'"';

        return PHP_EOL.$output_row;
    }

//$ss = 'C:\Peter\result\Peter\2\excel_pdf\Copy of DRW EFA H ON SMIT_DALBYxHPL2xHOSx23122021.pdf';
//$st = 'C:\temp\Sample\excel_pdf\Copy of DRW EFA H ON SMIT_DALBYxHPL2xHOSx23122021.pdf';
//if (!file_exists(dirname($st))) mkdir(dirname($st), 0755, true);
//copy($ss,$st);
//exit;

$csv_file_path = $option['target_path'].DIRECTORY_SEPARATOR.'index.csv';
//$entity_file_obj = new entity_file();
$entity_file_obj = new entity_file(null, ['table'=>'tbl_entity_file_halikos_group_2']);
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


$entity_file_obj->get(['where'=>'is_dir = 0','limit'=>$option['limit'],'offset'=>$option['offset']]);

$file_type_extension_map = $entity_file_obj->file_type_extension_map;

foreach ($entity_file_obj->row as $file_row) {
    $file_row['pdf_file'] = '';
    foreach($file_type_extension_map as $file_type=>$extension_list) {
//        print_r([strtolower($file_row['extension']),$extension_list,in_array(strtolower($file_row['extension']),$extension_list)]);
        if (in_array(strtolower($file_row['extension']),$extension_list)) {
//            print_r([$file_type,['office_word','office_excel','office_powerpoint','mail'],in_array($file_type,['office_word','office_excel','office_powerpoint','mail'])]);
            if (in_array($file_type,['office_word','office_excel','office_powerpoint','mail'])) {
                $file_name_parts = explode('.',$file_row['target_file']);
                array_pop($file_name_parts);
                $file_name_parts[] = 'pdf';
                array_unshift($file_name_parts, str_replace(['office_word','office_excel','office_powerpoint','mail'],['word_pdf','excel_pdf','powerpoint_pdf','mail_pdf'],array_shift($file_name_parts)));
                $pdf_file_path = implode('.', $file_name_parts);
                if (file_exists($option['target_path'].DIRECTORY_SEPARATOR.$pdf_file_path)) {
                    $file_row['pdf_file'] = $pdf_file_path;
                }
            }
        }
    }


    if ($file_row['modified_date'] === '0000-00-00 00:00:00' OR $file_row['modified_date'] === '1970-01-01 00:00:00') {
        $file_row['modified_date'] = '';
    }
    if ($file_row['created_date'] === '0000-00-00 00:00:00' OR $file_row['created_date'] === '1970-01-01 00:00:00') {
        $file_row['created_date'] = '';
    }

    print_r($file_row);

    $output_row = csv_row($file_row, ++$row_count);
    file_put_contents($csv_file_path,$output_row,FILE_APPEND);
}


//print_r("\n".$pdf_count." out of ".$total_count." pdf files generated. Execution Time: ".(microtime(1)-$start_stamp)."\n");
$result['execution_time'] = microtime(1)-$start_stamp;
echo json_encode($result);