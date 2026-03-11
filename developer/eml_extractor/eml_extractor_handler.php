<?php
error_reporting(0);
define('PATH_SITE_BASE', dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR);
include('../../system/config/config.php');

$result = ['success'=>true,'updated_data'=>[]];
$start_stamp = microtime(1);;

$row_limit = 100;
$step_list = [1,2,3,4,5,6,7,8];
$step_requested = [2,3,4,5,6];
if (!empty($_REQUEST['step'])) {
    if (is_string($_REQUEST['step'])) {
        $step_requested = array_values(array_intersect($step_list, explode(',',$_REQUEST['step'])));
    } else {
        if (is_array($_REQUEST['step'])) {
            $step_requested = array_values(array_intersect($step_list, $_REQUEST['step']));
        }
    }
    $result['step_requested'] = $step_requested;
}
if (!empty($_REQUEST['limit'])) {
    $row_limit = $_REQUEST['limit'];
    $result['row_limit'] = $row_limit;
}
if (!empty($_REQUEST['start_time'])) {
    $result['start_time'] = $_REQUEST['start_time'];
}

set_time_limit(240);
//    $source_folder = 'C:\1\jason.qian@halikos.com.au-2.pst';
$target_folder = 'C:\Peter\result\\';
//echo '<pre>';
if (in_array(1, $step_requested)) {
    $entity_file_obj = new entity_file();
//    $entity_file_obj->get(['where'=>['extension = "eml"','id NOT IN (SELECT id FROM tbl_entity_eml)'],'limit'=>$row_limit]);
    $entity_file_obj->get(['where'=>['extension = "eml"','id NOT IN (SELECT id FROM tbl_entity_eml)']]);

    $eml_list = [];
    $basic_fields = ['id','source_file','source_root','description'];
    foreach ($entity_file_obj->row as $file_row) {
        $file_row['description'] = 1;
        $eml_list[] = array_intersect_key($file_row, array_flip($basic_fields));
    }
    $entity_eml_obj = new entity_eml();
    $entity_eml_obj->set(['row'=>$eml_list, 'fields'=>$basic_fields]);
    $result['updated_data'] = ['status'=>'OK','message'=>'Insert '.count($eml_list).' eml files into tbl_entity_eml. '.count($entity_eml_obj->id_group).' Records Inserted.'];
}

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
if (in_array(2, $step_requested)) {
    $source_entity_eml_obj = new entity_eml();
    $source_entity_eml_obj->get(['where'=>['description = 1'],'limit'=>$row_limit]);
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
//    echo 'update row 2: '.json_encode($entity_eml_row).' | '.json_encode($entity_eml_obj->id_group).PHP_EOL;
        $result['updated_data'][] = ['status'=>'OK','message'=>'Get text content from ['.$entity_eml_row['id'].'], insert into tbl_entity_eml. '.count($entity_eml_obj->id_group).' Records Inserted.'];
    }
}

if (in_array(3, $step_requested)) {
    $source_entity_eml_obj = new entity_eml();
    $source_entity_eml_obj->get(['where'=>['description = 2'],'limit'=>$row_limit]);
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
//        echo 'attachments: '.$entity_eml_row['attachments'].PHP_EOL;
        }
        $entity_eml_row['description'] = 3;

        $entity_eml_obj->set_row($entity_eml_row);
        $result['updated_data'][] = ['status'=>'OK','message'=>'Get attachement list from ['.$entity_eml_row['id'].'], insert into tbl_entity_eml. '.count($entity_eml_obj->id_group).' Records Inserted.'];
//    echo 'update row 5: '.json_encode($entity_eml_row).' | '.json_encode($entity_eml_obj->id_group).PHP_EOL;
        if (!empty($entity_eml_obj->id_group)) {
            $entity_file_obj2 = new entity_file();
            $entity_file_obj2->get(['id_group'=>$entity_eml_obj->id_group]);
            $entity_file_obj2->update(['file_scanned'=>1]);
        }
    }
}

if (in_array(4, $step_requested)) {
    $source_entity_eml_obj = new entity_eml();
    $source_entity_eml_obj->get(['where'=>['description = 3'],'limit'=>$row_limit]);
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
//    echo 'update row 4: '.json_encode($entity_eml_row).' | '.json_encode($entity_eml_obj->id_group).PHP_EOL;
        $result['updated_data'][] = ['status'=>'OK','message'=>'Get header info from ['.$entity_eml_row['id'].'], insert into tbl_entity_eml. '.count($entity_eml_obj->id_group).' Records Inserted.'];

    }
}

if (in_array(5, $step_requested)) {
    $source_entity_eml_obj = new entity_eml();
    $source_entity_eml_obj->get(['where'=>['description = 4'],'limit'=>$row_limit]);
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
//    echo 'update row 3: '.json_encode($entity_eml_row).' | '.json_encode($entity_eml_obj->id_group).PHP_EOL;
        $result['updated_data'][] = ['status'=>'OK','message'=>'Get HTML content from ['.$entity_eml_row['id'].'], insert into tbl_entity_eml. '.count($entity_eml_obj->id_group).' Records Inserted.'];
    }
}

if (in_array(6, $step_requested)) {
    $target_root = 'C:\Peter\result\\';
    $target_folder = $target_root;
//    $target_folder = 'C:\temp\1\\';

    $entity_eml_obj = new entity_eml();
    $entity_eml_obj->get(['where'=>['description = "5"'],'limit'=>$row_limit]);
    print_r($entity_eml_obj);

//    $entity_eml_obj->update(['description' => "-5"]);

    $pdf_count = 0;
    $total_count = count($entity_eml_obj->row);
    foreach ($entity_eml_obj->row as $eml_row) {
        set_time_limit(300);
        $entity_eml_obj2 = new entity_eml();
        $entity_eml_obj2->set_row(['id'=>$eml_row['id'],'description'=>-5]);
//        $source_file = $eml_row['source_root'].$eml_row['source_file'];
//        if (!empty($eml_row['source_root'])) {
//            $source_root_parts = explode('\\', $eml_row['source_root']);
//            array_shift($source_root_parts);
//            $target_folder = $target_root.implode('\\', $source_root_parts).'\\';
//            if (!file_exists($target_folder)) mkdir($target_folder, 0755, true);
//        }
//
//        $target_file_name = '';
//        $source_file_parts = explode('\\', str_replace('/','\\',$source_file));
//        foreach ($source_file_parts as $source_file_part) {
//            if (strpos($source_file_part, '@') !== false) {
//                $target_file_name = $source_file_part;
//                break;
//            }
//        }
        $source_file = $eml_row['source_root'].$eml_row['source_file'];
//        $source_file = 'D:\HPL1\pst_eml\\'.$eml_row['source_file'];

        $target_file_name = '';
        $source_file_parts = explode('\\', str_replace('/','\\',$source_file));
        foreach ($source_file_parts as $source_file_part) {
            if (strpos($source_file_part, '@') !== false Or preg_match('/\.pst$/',$source_file_part)) {
                $target_file_name = $source_file_part;
                break;
            }
        }
        if (empty($target_file_name)) {
            $target_file_name = 'other';
        }


        if (!empty($eml_row['source_root'])) {
            $source_root_parts = explode('\\', $eml_row['source_root']);
            array_shift($source_root_parts);
//            $old_target_folder = $old_target_root.implode('\\', $source_root_parts).'\\';
            $target_folder = $target_root.implode('\\', $source_root_parts).'\\'.$target_file_name.'\\';
            if (!file_exists($target_folder)) mkdir($target_folder, 0755, true);
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

        $default_target_file_name = $target_file_name;
//        $entity_eml_obj3 = new entity_eml();
//        $entity_eml_obj3->get(['where'=>['target_file LIKE "'.$target_folder.$target_file_name.'%.pdf"','id <> '.$eml_row['id']]]);

        $counter = 0;
        while (file_exists($target_folder.$target_file_name.'.pdf')) {
            $counter++;
            $target_file_name = $default_target_file_name.'_x'.sprintf('%02d', $counter);
        }
//        $existing_file_name_list = [];
//        foreach ($entity_eml_obj3->row as $eml_row3) {
//            $existing_file_name_list[] = $eml_row3['target_file'];
//        }
//        if (!empty($eml_row['target_file'])) {
//            $target_file_name = preg_replace('/\.pdf$/','',$eml_row['target_file']);
//        }
//        while (in_array($target_folder.$target_file_name.'.pdf',$existing_file_name_list)) {
//            $counter++;
//            $target_file_name = $default_target_file_name.'_x'.sprintf('%02d', $counter);
//        }

//        if (file_exists($target_folder.$target_file_name.'.pdf')) {
//            unlink($target_folder.$target_file_name.'.pdf');
//            foreach (glob($target_folder.$target_file_name.'_ATTCH*.*') as $old_file) {
//                echo $old_file.PHP_EOL;
//                unlink($old_file);
//            }
//            if (file_exists($target_folder.$target_file_name.'_EML.eml')) {
//                unlink($target_folder.$target_file_name.'_EML.eml');
//            }
//        }


//        $mimeParser = new PhpMimeMailParser\Parser();
//        $mimeParser->setPath($source_file);

        $attached_images = [];
        if(!empty($eml_row['has_attachment'])) {
            $mimeParser = new PhpMimeMailParser\Parser();
            $mimeParser->setPath($source_file);


            $attachments = $mimeParser->getAttachments([false]);

            if (count($attachments) > 0) {
//                echo 'attachments count: '.count($attachments).PHP_EOL;
                $attachments_list = [];
                foreach ($attachments as $attachment_index => $attachment) {
//                    print_r($attachment);
//                    echo PHP_EOL.'-----------'.PHP_EOL;
                    $attachment_row = [
                        'content_id' => $attachment->getContentID(),
                        'content_disposition' => $attachment->getContentDisposition(),
                        'content_type' => $attachment->getContentType(),
                        'headers' => $attachment->getHeaders(),
                        'name' => $attachment->getFilename(),
                    ];
                    $attachment_extension = '';
//                    $attachment_name = $attachment->getFilename();
//                    echo $attachment->getContentDisposition().PHP_EOL;
//                    echo $attachment_name.PHP_EOL;
//                    echo 'attachment_name: '.$attachment_name.PHP_EOL;
                    if  (strpos($attachment_row['name'], '.')!==false){
                        $attachment_name_parts = explode('.',$attachment_row['name']);
                        $attachment_extension = end($attachment_name_parts);
                        if (strlen($attachment_extension) > 4 OR preg_match('/([^- \.a-zA-Z0-9]+)/',$attachment_extension)) {
                            $attachment_extension = 'eml';
                        }
                    }
                    $attachment_row['extension'] = $attachment_extension;

                    $attachment_row['new_name'] = $target_file_name.'_ATTCH_'.strtolower($attachment_extension).sprintf('%02d', $attachment_index+1).'.'.$attachment_extension;

                    $attachments_list[] = $attachment_row;


                    $attachment->save($target_folder,false,'CustomFileName',$attachment_row['new_name']);
                    // Following code also work, alternative way of saving attachment, replace line above, works without hacking PhpMimeMailParser plugin
//                    $attachment_data_stream = $attachment->getStream();
//                    echo $attachment->getContentID().PHP_EOL;
//                    if ($attachment_row['content_disposition'] === 'attachment') {
//                        file_put_contents($target_folder.$attachment_row['new_name'],$attachment_data_stream);
//                    }


//                    if (!empty($eml_row['content_html']) AND in_array($attachment_row['extension'],['jpg','jpeg','gif','png'])) {
//                        $attached_images[$attachment_row['content_id']] = $attachment->getContent();
//                    }
                }
                $eml_row['attachments'] = json_encode($attachments_list);
            }
        }


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
//            $eml_row['content_html'] = preg_replace('/font\-family\:"(.*?)"(.*?)([\;\\\'])/','font-family:$1$2$3',$eml_row['content_html']);
//            $eml_row['content_html'] = preg_replace('/font\-family\:\'(.*?)\'(.*?)([\;\"])/','font-family:$1$2$3',$eml_row['content_html']);
            $pdf->writeHTML($eml_row['content_html'], false, false, true, false, '',$attached_images);
//        $pdf->MultiCell(190, 5, $eml_row['content_html'], 0, 'L', 0, 0, '', '', true, 0, true, true, 0, 'T', false);
        } else {
            $pdf->MultiCell(190, 5, $eml_row['content_text'], 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'T', false);
        }


        $pdf->Output($target_folder.$target_file_name.'.pdf', 'F');



        if (file_exists($target_folder.$target_file_name.'.pdf')) {
            copy($source_file,$target_folder.$target_file_name.'_EML.eml');
            $pdf_count++;
            $entity_eml_obj2->set_row(['id'=>$eml_row['id'],'target_file'=>$target_file_name.'.pdf','attachments'=>$eml_row['attachments'],'description'=>6]);

//            $eml_row['attachments'] = '';

            $result['updated_data'][] = ['status'=>'OK','message'=>'PDF Generated from EML Record ('.$eml_row['id'].') Successfully. '.$target_folder.$target_file_name.'.pdf'];
        } else {
            $result['updated_data'][] = ['status'=>'Error','message'=>'Failed to generate PDF from eml record ('.$eml_row['id'].'). '.$target_folder.$target_file_name.'.pdf'];
        }
    }
}

if (in_array(7, $step_requested)) {
    $old_target_root = 'C:\Peter\result\\';
    $old_target_folder = $old_target_root;
    $target_root = 'C:\Peter\result2\\';
    $target_folder = $target_root;
//    $target_folder = 'C:\temp\1\\';

    $entity_eml_obj = new entity_eml();
    $entity_eml_obj->get(['where'=>['description = "6"'],'limit'=>$row_limit]);

//    $entity_eml_obj->update(['description' => "-5"]);

    $pdf_count = 0;
    $total_count = count($entity_eml_obj->row);
    foreach ($entity_eml_obj->row as $eml_row) {
        set_time_limit(300);
        $entity_eml_obj2 = new entity_eml();
        $entity_eml_obj2->set_row(['id'=>$eml_row['id'],'description'=>-6]);
        $source_file = $eml_row['source_root'].$eml_row['source_file'];

        $target_file_name = '';
        $source_file_parts = explode('\\', str_replace('/','\\',$source_file));
        foreach ($source_file_parts as $source_file_part) {
            if (strpos($source_file_part, '@') !== false Or preg_match('/\.pst$/',$source_file_part)) {
                $target_file_name = $source_file_part;
                break;
            }
        }
        if (empty($target_file_name)) {
            $target_file_name = 'other';
        }

        if (!empty($eml_row['source_root'])) {
            $source_root_parts = explode('\\', $eml_row['source_root']);
            array_shift($source_root_parts);
            $old_target_folder = $old_target_root.implode('\\', $source_root_parts).'\\';
            $target_folder = $target_root.implode('\\', $source_root_parts).'\\'.$target_file_name.'\\';
            if (!file_exists($target_folder)) mkdir($target_folder, 0755, true);
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

        $default_target_file_name = $target_file_name;
//        $entity_eml_obj3 = new entity_eml();
//        $entity_eml_obj3->get(['where'=>['target_file LIKE "'.$target_folder.$target_file_name.'%.pdf"','id <> '.$eml_row['id']]]);

        $counter = 0;
        while (file_exists($target_folder.$target_file_name.'.pdf')) {
            $counter++;
            $target_file_name = $default_target_file_name.'_x'.sprintf('%02d', $counter);
        }

        if (!empty($eml_row['target_file'])) {
            if (file_exists($old_target_folder.$eml_row['target_file'])) {
                rename($old_target_folder.$eml_row['target_file'], $target_folder.$target_file_name.'.pdf');
                $result['updated_data'][] = ['status'=>'OK','message'=>'Mail PDF file renamed ('.$eml_row['id'].') Successfully. '.$target_folder.$target_file_name.'.pdf'];
            } else {
                $result['updated_data'][] = ['status'=>'Error','message'=>'Mail PDF file rename Failure ('.$eml_row['id'].'). Source file does not exist '.$old_target_folder.$eml_row['target_file']];
            }

            $old_target_file_name = preg_replace('/\.pdf$/','',$eml_row['target_file']);
            if (file_exists($old_target_folder.$old_target_file_name.'_EML.eml')) {
                rename($old_target_folder.$old_target_file_name.'_EML.eml', $target_folder.$target_file_name.'_EML.eml');
            }
//            $old_file_names = glob($old_target_folder.$old_target_file_name.'_*.*');
//            foreach ($old_file_names as $old_file_name) {
//                $new_file_name = str_replace($old_target_folder.$old_target_file_name,$target_folder.$target_file_name,$old_file_name);
//                copy($old_file_name, $new_file_name);
//            }
            if(!empty($eml_row['has_attachment'])) {
                $attachment_rows = [];
                if (!empty($eml_row['attachments'])) {
                    $attachment_rows = json_decode($eml_row['attachments'], true);
                }
                if(is_array($attachment_rows) AND !empty($attachment_rows)) {
                    $attachments_list = [];
                    foreach ($attachment_rows as $attachment_index => $attachment_row) {
                        $attachment_extension = empty($attachment_row['extension'])?'':$attachment_row['extension'];
                        $attachment_row['new_name'] = $target_file_name.'_ATTCH_'.strtolower($attachment_extension).sprintf('%02d', $attachment_index+1).'.'.$attachment_extension;
                        if (file_exists($old_target_folder.$old_target_file_name.'_ATTCH_'.strtolower($attachment_extension).sprintf('%02d', $attachment_index+1).'.'.$attachment_extension)) {
                            rename($old_target_folder.$old_target_file_name.'_ATTCH_'.strtolower($attachment_extension).sprintf('%02d', $attachment_index+1).'.'.$attachment_extension, $target_folder.$attachment_row['new_name']);
                        }
                        $attachments_list[] = $attachment_row;
                    }
                    $eml_row['attachments'] = json_encode($attachments_list);
                    $result['updated_data'][] = ['status'=>'OK','message'=>'Attachments Renamed from Database Record ('.$eml_row['id'].') Successfully. '.$target_folder.$target_file_name.'.pdf'];
                } else {
                    $mimeParser = new PhpMimeMailParser\Parser();
                    $mimeParser->setPath($source_file);

                    $attachments = $mimeParser->getAttachments([false]);

                    if (count($attachments) > 0) {
//                echo 'attachments count: '.count($attachments).PHP_EOL;
                        $attachments_list = [];
                        foreach ($attachments as $attachment_index => $attachment) {
//                    print_r($attachment);
//                    echo PHP_EOL.'-----------'.PHP_EOL;
                            $attachment_row = [
                                'content_id' => $attachment->getContentID(),
                                'content_disposition' => $attachment->getContentDisposition(),
                                'content_type' => $attachment->getContentType(),
                                'headers' => $attachment->getHeaders(),
                                'name' => $attachment->getFilename(),
                            ];
                            $attachment_extension = '';
//                    $attachment_name = $attachment->getFilename();
//                    echo $attachment->getContentDisposition().PHP_EOL;
//                    echo $attachment_name.PHP_EOL;
//                    echo 'attachment_name: '.$attachment_name.PHP_EOL;
                            if  (strpos($attachment_row['name'], '.')!==false){
                                $attachment_name_parts = explode('.',$attachment_row['name']);
                                $attachment_extension = end($attachment_name_parts);
                                if (strlen($attachment_extension) > 4 OR preg_match('/([^- \.a-zA-Z0-9]+)/',$attachment_extension)) {
                                    $attachment_extension = 'eml';
                                }
                            }
                            $attachment_row['extension'] = $attachment_extension;

                            $attachment_row['new_name'] = $target_file_name.'_ATTCH_'.strtolower($attachment_extension).sprintf('%02d', $attachment_index+1).'.'.$attachment_extension;
                            if (file_exists($old_target_folder.$old_target_file_name.'_ATTCH_'.strtolower($attachment_extension).sprintf('%02d', $attachment_index+1).'.'.$attachment_extension)) {
                                rename($old_target_folder.$old_target_file_name.'_ATTCH_'.strtolower($attachment_extension).sprintf('%02d', $attachment_index+1).'.'.$attachment_extension, $target_folder.$attachment_row['new_name']);
                            }

                            $attachments_list[] = $attachment_row;


//                        $attachment->save($target_folder,false,'CustomFileName',$attachment_row['new_name']);

                        }
                        $eml_row['attachments'] = json_encode($attachments_list);
                        $result['updated_data'][] = ['status'=>'OK','message'=>'Attachments Renamed from EML Record ('.$eml_row['id'].') Successfully. '.$target_folder.$target_file_name.'.pdf'];
                    }
                }
            }
        }

        if (file_exists($target_folder.$target_file_name.'.pdf')) {
//            copy($source_file,$target_folder.$target_file_name.'_EML.eml');
//            $pdf_count++;
            $entity_eml_obj2->set_row(['id'=>$eml_row['id'],'target_file'=>$target_file_name.'.pdf','attachments'=>$eml_row['attachments'],'description'=>7]);

//            $eml_row['attachments'] = '';


        } else {
            $result['updated_data'][] = ['status'=>'Error','message'=>'Failed to generate PDF from eml record ('.$eml_row['id'].'). '.$target_folder.$target_file_name.'.pdf'];
        }
    }
}

if (in_array(8, $step_requested)) {
    $old_target_root = 'D:\HPL1\pst_eml\\';
    $old_target_folder = $old_target_root;
    $target_root = 'C:\Peter\result\Peter\E01\\';
    $target_folder = $target_root;
//    $target_folder = 'C:\temp\1\\';

    $entity_eml_obj = new entity_eml();
    $entity_eml_obj->get(['where'=>['description = "-6"'],'limit'=>$row_limit]);

//    $entity_eml_obj->update(['description' => "-5"]);

    $pdf_count = 0;
    $total_count = count($entity_eml_obj->row);
    foreach ($entity_eml_obj->row as $eml_row) {
        set_time_limit(300);
        $entity_eml_obj2 = new entity_eml();
//        $entity_eml_obj2->set_row(['id'=>$eml_row['id'],'description'=>-6]);
        $source_file = $eml_row['source_root'].$eml_row['source_file'];

        $old_target_file_name = preg_replace('/\.pdf$/','',$eml_row['target_file']);
        $target_file_name = $old_target_file_name;
        if (file_exists($old_target_folder.$old_target_file_name.'.pdf')) {
            copy($old_target_folder.$old_target_file_name.'.pdf', $target_folder.$target_file_name.'.pdf');
            $result['updated_data'][] = ['status'=>'OK','message'=>'Copy file ('.$eml_row['id'].'). '.$old_target_folder.$old_target_file_name.'.pdf => '.$target_folder.$target_file_name.'.pdf'];
        }
        if (file_exists($old_target_folder.$old_target_file_name.'_EML.eml')) {
            copy($old_target_folder.$old_target_file_name.'_EML.eml', $target_folder.$target_file_name.'_EML.eml');
        }
        if(!empty($eml_row['has_attachment'])) {
            $attachment_rows = [];
            if (!empty($eml_row['attachments'])) {
                $attachment_rows = json_decode($eml_row['attachments'], true);
            }
            if (is_array($attachment_rows) and !empty($attachment_rows)) {
                $attachments_list = [];
                foreach ($attachment_rows as $attachment_index => $attachment_row) {
                    $attachment_extension = empty($attachment_row['extension']) ? '' : $attachment_row['extension'];
                    $attachment_row['new_name'] = $target_file_name . '_ATTCH_' . strtolower($attachment_extension) . sprintf('%02d', $attachment_index + 1) . '.' . $attachment_extension;
                    if (file_exists($old_target_folder . $old_target_file_name . '_ATTCH_' . strtolower($attachment_extension) . sprintf('%02d', $attachment_index + 1) . '.' . $attachment_extension)) {
                        copy($old_target_folder . $old_target_file_name . '_ATTCH_' . strtolower($attachment_extension) . sprintf('%02d', $attachment_index + 1) . '.' . $attachment_extension, $target_folder . $attachment_row['new_name']);
                    }
                    $attachments_list[] = $attachment_row;
                }
            }
        }

        if (file_exists($target_folder.$target_file_name.'.pdf')) {
//            copy($source_file,$target_folder.$target_file_name.'_EML.eml');
//            $pdf_count++;
            $entity_eml_obj2->set_row(['id'=>$eml_row['id'],'description'=>6]);

//            $eml_row['attachments'] = '';


        } else {
            $result['updated_data'][] = ['status'=>'Error','message'=>'Failed to generate PDF from eml record ('.$eml_row['id'].'). '.$target_folder.$target_file_name.'.pdf'];
        }
    }
}

//print_r("\n".$pdf_count." out of ".$total_count." pdf files generated. Execution Time: ".(microtime(1)-$start_stamp)."\n");
$result['execution_time'] = microtime(1)-$start_stamp;
echo json_encode($result);