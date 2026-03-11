<?php
    define('PATH_SITE_BASE', dirname(__DIR__).DIRECTORY_SEPARATOR);
    include('../system/config/config.php');
    $start_stamp = microtime(1);

    set_time_limit(240);
$row_limit = 100;

if (!empty($_REQUEST['limit'])) {
    $row_limit = $_REQUEST['limit'];
}

$target_root = 'C:\Peter\result\\';
$target_folder = $target_root;
//    $target_folder = 'C:\temp\1\\';

$entity_eml_obj = new entity_eml();
$entity_eml_obj->get(['where'=>['description = "5"'],'limit'=>$row_limit]);

//$entity_eml_obj->update(['description' => "-5"]);

$pdf_count = 0;
$total_count = count($entity_eml_obj->row);
foreach ($entity_eml_obj->row as $eml_row) {
    set_time_limit(300);
    $entity_eml_obj2 = new entity_eml();
    $entity_eml_obj2->set_row(['id'=>$eml_row['id'],'description'=>-5]);
    $source_file = $eml_row['source_root'].$eml_row['source_file'];
    if (!empty($eml_row['source_root'])) {
        $source_root_parts = explode('\\', $eml_row['source_root']);
        array_shift($source_root_parts);
        $target_folder = $target_root.implode('\\', $source_root_parts).'\\';
        if (!file_exists($target_folder)) mkdir($target_folder, 0755, true);
    }

//    echo PHP_EOL.PHP_EOL.'id: '.htmlentities($eml_row['id']).PHP_EOL;
//    echo PHP_EOL.PHP_EOL.'subject: '.htmlentities($eml_row['subject']).PHP_EOL;
//        $target_file_name = $eml_row['source_root'];
//        while (strrpos($target_file_name,'\\') !== false) {
//            $target_file_name =  substr($target_file_name, strrpos($target_file_name,'\\')+1);
//        }
//        echo 'source_file: '.$target_file_name.PHP_EOL;

    $target_file_name = '';
    $source_file_parts = explode('\\', str_replace('/','\\',$source_file));
    foreach ($source_file_parts as $source_file_part) {
        if (strpos($source_file_part, '@') !== false) {
            $target_file_name = $source_file_part;
            break;
        }
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
            $target_file_name = str_pad(substr($first_mail,0,12),12,'-').$target_file_name;
        } else {
            $target_file_name = str_pad(substr(preg_replace('/[-]+/','-',preg_replace('/([^- \.a-zA-Z0-9]+)/','-',$eml_row[$type])),0,12),12,'-').$target_file_name;
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

    $default_target_file_name = $target_file_name;
    $counter = 0;
    while (file_exists($target_folder.$target_file_name.'.pdf')) {
        $counter++;
        $target_file_name = $default_target_file_name.'_x'.sprintf('%02d', $counter);
    }



//        $mimeParser = new PhpMimeMailParser\Parser();
//        $mimeParser->setPath($source_file);


    $pdf = new TCPDF('P', 'mm', 'A4', 'UTF-8', false);
    $pdf->SetCreator('MGS');
    $pdf->SetAuthor('MGS');
    $pdf->SetTitle($eml_row['subject']);
    $pdf->setFont('helvetica', '', 12);

    $pdf->addPage();

    $body_text = '';

    foreach (['from', 'reply_to', 'date', 'to', 'cc', 'bcc', 'subject', 'attachments'] as $header_attribute) {
        if (!empty($eml_row[$header_attribute])) {
            if ($header_attribute == 'date') {
                $body_text .= ucfirst($header_attribute).': '.date('Y-m-d H:i:s', strtotime($eml_row[$header_attribute])).PHP_EOL;
                continue;
            }
            if ($header_attribute == 'attachments') {
                $attachments_list = json_decode($eml_row[$header_attribute],true);
                $attachments_name_list = [];
                foreach ($attachments_list as $attachment_row) {
                    $attachments_name_list[] = $attachment_row['name'];
                }
                $body_text .= ucfirst($header_attribute).': '.implode(', ', $attachments_name_list).PHP_EOL;
                continue;
            }
            $body_text .= ucfirst(str_replace('_','',$header_attribute)).': '.$eml_row[$header_attribute].PHP_EOL;
        }
    }
    if(!empty($body_text)) {
        $body_text .= PHP_EOL;
    }

//        $line_style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 255));
//        $currentY = $pdf->GetY();
//        $pdf->Line(9, $currentY, $pdf->getPageWidth() - 9, $currentY,$line_style);

    $pdf->MultiCell(190, 5, $body_text, 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'T', false);

    $numberOfLines = $pdf->getNumLines($body_text, $pdf->getPageWidth());
    $currentY = $numberOfLines * 7.5;
    $pdf->setY($currentY);
    $line_style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
    $pdf->Line(10, $currentY, $pdf->getPageWidth() - 10, $currentY,$line_style);

    $pdf->Ln(15);


    if (!empty($eml_row['content_html'])) {
        $pdf->writeHTML($eml_row['content_html'], false, false, true, false, '');
//        $pdf->MultiCell(190, 5, $eml_row['content_html'], 0, 'L', 0, 0, '', '', true, 0, true, true, 0, 'T', false);
    } else {
        $pdf->MultiCell(190, 5, $eml_row['content_text'], 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'T', false);
    }


    $pdf->Output($target_folder.$target_file_name.'.pdf', 'F');



    if (file_exists($target_folder.$target_file_name.'.pdf')) {
        $pdf_count++;


//            $eml_row['attachments'] = '';
        if(!empty($eml_row['attachments'])) {
            $mimeParser = new PhpMimeMailParser\Parser();
            $mimeParser->setPath($source_file);


            $attachments = $mimeParser->getAttachments([false]);

            if (count($attachments) > 0) {
//                echo 'attachments count: '.count($attachments).PHP_EOL;
                $attachments_list = [];
                foreach ($attachments as $attachment_index => $attachment) {
                    $attachment_extension = '';
                    $attachment_name = $attachment->getFilename();
//                    echo 'attachment_name: '.$attachment_name.PHP_EOL;
                    if  (strpos($attachment_name, '.')!==false){
                        $attachment_name_parts = explode('.',$attachment_name);
                        $attachment_extension = end($attachment_name_parts);
                    }
                    $attachment_new_name = $target_file_name.'_ATTCH_'.strtolower($attachment_extension).sprintf('%02d', $attachment_index+1).'.'.$attachment_extension;
                    $attachments_list[] = ['name'=>$attachment_name,'new_name'=>$attachment_new_name,'extension'=>$attachment_extension];
                    $attachment->save($target_folder,false,'CustomFileName',$attachment_new_name);
                }
                $eml_row['attachments'] = json_encode($attachments_list);
            }
        }

        $entity_eml_obj2->set_row(['id'=>$eml_row['id'],'target_file'=>$target_file_name.'.pdf','attachments'=>$eml_row['attachments'],'description'=>6]);
        $result['updated_data'][] = ['status'=>'OK','message'=>'PDF Generated from EML Record Successfully. '.$target_folder.$target_file_name.'.pdf'];
    } else {
        $result['updated_data'][] = ['status'=>'Error','message'=>'Failed to generate PDF from eml record. '.$target_folder.$target_file_name.'.pdf'];
    }
}
print_r("\n".$pdf_count." out of ".$total_count." pdf files generated. Execution Time: ".(microtime(1)-$start_stamp)."\n");