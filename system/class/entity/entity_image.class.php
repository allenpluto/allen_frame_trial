<?php
// Class Object
// Name: entity_image
// Description: Image Source File, store image in big size, for gallery details, source file to crop... All variation of images (different size thumbs) goes to image_variation table.

// image_id in image_object reference to source image. One source image may have zero to multiple thumbnail (cropped versions) for different scenario. Only source image may save exifData, any thumbnail can be regenerated using source image exifData and 
class entity_image extends entity
{
    function __construct($value = Null, $parameter = array())
    {
        include_once(PATH_PREFERENCE.'image'.FILE_EXTENSION_INCLUDE);

        $default_parameter = [
            'store_data'=>true
        ];
        $parameter = array_merge($default_parameter, $parameter);
        parent::__construct($value, $parameter);

        if (!$this->parameter['store_data'])
        {
            unset($this->parameter['table_fields']['data']);
        }

        return $this;
    }

    function get($parameter = array())
    {
        $format = format::get_obj();
        $preference = preference::get_instance();
        if(parent::get($parameter) === false) return false;

        if (!empty($this->row))
        {
            foreach($this->row as $record_index=>&$record)
            {
                $record['document'] = $this->format->file_name((!empty($record['friendly_url'])?$record['friendly_url']:$record['name']).'-'.$record['id']);
                switch($record['mime'])
                {
                    case 'image/gif':
                        $record['file_type'] = 'gif';
                        break;
                    case 'image/png':
                        $record['file_type'] = 'png';
                        break;
                    case 'image/jpeg':
                    case 'image/pjpeg';
                    default:
                        $record['file_type'] = 'jpg';
                }
                $record['sub_path'] = [];
                $sub_path_index = floor($record['id'] / 1000);
                while($sub_path_index > 0)
                {
                    $sub_path_remain = $sub_path_index % 1000;
                    $sub_path_index = floor($sub_path_index / 1000);
                    $record['sub_path'][] = $sub_path_remain;
                }
            }
        }
    }


    function set($parameter = array())
    {
        if (isset($parameter['row']))
        {
            $max_image_width = max($this->preference->image['size']);
            $image_quality = $this->preference->image['quality']['max'];

            foreach($parameter['row'] as $record_index => &$record)
            {
                if (isset($record['source_file']))
                {
                    $image_size = @getimagesize($record['source_file']);
                    if ($image_size !== false)
                    {
                        $record['width'] = $image_size[0];
                        $record['height'] = $image_size[1];
                        if (isset($image_size['mime'])) $record['mime'] = $image_size['mime'];
                        else
                        {
                            $record['mime'] = 'image/jpeg';
                            $this->message->notice = __FILE__ . '(line ' . __LINE__ . '): '.$parameter['table'].' failed to get image mime type for '.$record['source_file'];
                        }

                        $image_data = file_get_contents($record['source_file']);

                        if ($image_data !== false)
                        {
                            if ($record['width'] > $max_image_width AND ($record['mime'] == 'image/jpeg' OR $record['mime'] == 'image/png'))
                            {
                                $original_record = $record;
                                $record['height'] = $record['height'] / $record['width'] * $max_image_width;
                                $record['width'] = $max_image_width;

                                // Set default image quality as 'max'
                                //$record['quality'] = $this->preference->image['quality']['max'];
                                $source_image = imagecreatefromstring($image_data);
                                $target_image = imagecreatetruecolor($record['width'],  $record['height']);

                                imagecopyresampled($target_image,$source_image,0,0,0,0,$record['width'], $record['height'],$original_record['width'],$original_record['height']);
                                imageinterlace($target_image,true);

                                ob_start();
                                if ($record['mime'] == 'image/jpeg') imagejpeg($target_image, NULL, $image_quality['image/jpeg']);
                                if ($record['mime'] == 'image/png')
                                {
                                    imagesavealpha($target_image, true);
                                    imagepng($target_image, NULL, $image_quality['image/png'][0], $image_quality['image/png'][1]);
                                }
                                $image_data = ob_get_contents();
                                ob_get_clean();

                                imagedestroy($source_image);
                                imagedestroy($target_image);
                                unset($original_record);
                            }

                            $record['data'] = $image_data;
                        }
                        else
                        {
                            $this->message->warning = __FILE__ . '(line ' . __LINE__ . '): '.$parameter['table'].' failed to get image size for '.$record['source_file'];
                        }
                        unset($image_data);
                    }
                    else
                    {
                        $this->message->warning = __FILE__ . '(line ' . __LINE__ . '): '.$parameter['table'].' failed to get image size for '.$record['source_file'];
                    }
                    unset($image_size);
                }
            }
        }

        $result = parent::set($parameter);
        if ($result === false) return false;

        $this->generate_cache_file();
        //$listing_image = explode(',', $_POST['listing_logo_thumb']);

        //file_put_contents($file_path,  base64_decode($listing_image[count($listing_image)-1]));

    }

    function generate_cache_file($parameter = array())
    {
        if (!empty($this->row))
        {
            $format = format::get_obj();
            $preference = preference::get_instance();

            foreach($this->row as $record_index=>&$record)
            {
                if (isset($record['data']))
                {
                    $file_name = $format->file_name((!empty($record['friendly_url'])?$record['friendly_url']:$record['name']));
                    if (empty($file_name)) $file_name = 'default';
                    // Generate re-sized thumbnail
                    // if (!empty($parameter['size'])) $file_name .= '.'.$parameter['size'];
                    switch($record['mime'])
                    {
                        case 'image/gif':
                            $file_name .= '.gif';
                            break;
                        case 'image/png':
                            $file_name .= '.png';
                            break;
                        case 'image/jpeg':
                        case 'image/pjpeg';
                        default:
                            $file_name .= '.jpg';
                    }
                    // Create sub path in "Little Endian" structure
                    $sub_path = '';
                    $sub_path_index = $record['id'];
                    do
                    {
                        $sub_path_remain = $sub_path_index % 1000;
                        $sub_path_index = floor($sub_path_index / 1000);
                        if ($sub_path_index != 0)
                        {
                            $sub_path_remain = str_repeat('0', 3-strlen($sub_path_remain)).$sub_path_remain;
                        }
                        $sub_path = $sub_path_remain.DIRECTORY_SEPARATOR.$sub_path;
                    } while($sub_path_index > 0);
                    $record['file_path'] = PATH_IMAGE.$sub_path;
                    $record['file_name'] = $file_name;
                    $record['file'] = $record['file_path'].$record['file_name'];

                    if (!file_exists(PATH_IMAGE.$sub_path)) mkdir($record['file_path'], 0755, true);
                    file_put_contents($record['file'],  $record['data']);
                }
            }
        }
    }

    function sync($parameter = array())
    {
        $sync_parameter = array();

        // set default sync parameters for index table
        //$parameter['sync_table'] = str_replace('entity','index',$this->parameter['table']);
        $sync_parameter['sync_table'] = 'tbl_view_image';
        $sync_parameter['update_fields'] = array(
            'id' => 'tbl_entity_image.id',
            'friendly_uri' => 'tbl_entity_image.friendly_uri',
            'name' => 'tbl_entity_image.name',
            'alternate_name' => 'tbl_entity_image.alternate_name',
            'description' => 'tbl_entity_image.description',
            'enter_time' => 'tbl_entity_image.enter_time',
            'update_time' => 'tbl_entity_image.update_time',
            'view_time' => '"'.date('Y-m-d H:i:s').'"',
            'width' => 'tbl_entity_image.width',
            'height' => 'tbl_entity_image.height',
            'mime' => 'tbl_entity_image.mime',
            'data' => 'tbl_entity_image.data',
            'source_file' => 'tbl_entity_image.source_file'
        );
        $sync_parameter['advanced_sync'] = true;

        $sync_parameter = array_merge($sync_parameter, $parameter);

        if ($GLOBALS['db']) $db = $GLOBALS['db'];
        else $db = new db;

        if (!isset($sync_parameter['sync_type']))
        {
            $sync_parameter['sync_type'] = 'differential_sync';
        }
        if (!$db->db_table_exists($sync_parameter['sync_table']))
        {
            $sync_parameter['sync_type'] = 'init_sync';
        }
        if ($sync_parameter['sync_type'] == 'init_sync')
        {
            $init_sync_parameter = $sync_parameter;
            unset($init_sync_parameter['update_fields']['data']);
            $init_sync_parameter['update_fields']['file_extension'] = '"'.str_repeat(' ',20).'"';
            $init_sync_parameter['update_fields']['file_uri'] = '"'.str_repeat(' ',200).'"';
            $init_sync_parameter['update_fields']['file_path'] = '"'.str_repeat(' ',200).'"';
            $init_sync_parameter['update_fields']['file_size'] = 10^10;
            parent::sync($init_sync_parameter);

            $sync_parameter['sync_type'] = 'full_sync';
            return parent::sync($sync_parameter);
        }
        else
        {
            return parent::sync($sync_parameter);
        }
    }

    function advanced_sync_update(&$source_row = array())
    {
        parent::advanced_sync_update($source_row);

        foreach ($source_row as $index=>&$row)
        {
            if (!empty($row['source_file']) AND empty($row['data']))
            {

            }
            if (!empty($row['data']))
            {
                if (empty($row['id']))
                {
                    $this->message->error = __FILE__ . '(line ' . __LINE__ . '): image sync row id not set';
                }
                if (empty($row['friendly_uri']))
                {
                    $this->message->error = __FILE__ . '(line ' . __LINE__ . '): image sync row friendly uri not set';
                }
                $sub_path = '';
                $sub_path_index = $row['id'];
                do
                {
                    $sub_path_remain = $sub_path_index % 1000;
                    $sub_path_index = floor($sub_path_index / 1000);
                    if ($sub_path_index != 0)
                    {
                        $sub_path_remain = str_repeat('0', 3-strlen($sub_path_remain)).$sub_path_remain;
                    }
                    $sub_path = $sub_path_remain.DIRECTORY_SEPARATOR.$sub_path;
                } while($sub_path_index > 0);
                $file_dir = PATH_IMAGE.$sub_path;
                $file_name = $row['friendly_uri'];
                switch($row['mime'])
                {
                    case 'image/gif':
                        $row['file_extension'] = 'gif';
                        break;
                    case 'image/png':
                        $row['file_extension'] = 'png';
                        break;
                    case 'image/jpeg':
                    case 'image/pjpeg';
                    default:
                        $row['file_extension'] = 'jpg';
                }
                $row['file_uri'] = URI_IMAGE.$file_name.'-'.$row['id'].'.'.$row['file_extension'];
                $row['file_path'] = $file_dir.$file_name.'-'.$row['id'].'.'.$row['file_extension'];

                if (!file_exists($file_dir)) mkdir($file_dir, 0755, true);
                file_put_contents($row['file_path'],  $row['data']);
                unset($row['data']);

                $row['file_size'] = filesize($row['file_path']);
            }
        }

        return $source_row;
    }

    function advanced_sync_delete($delete_id_group = array())
    {
        $view_image_obj = new view_image($delete_id_group);
        $image_array = $view_image_obj->fetch_value();
        foreach ($image_array as $image_row_index=>$image_row)
        {
            $current_image_folder = dirname($image_row['file_path']);
            if (file_exists($current_image_folder))
            {
                $current_image_files = scandir($current_image_folder);
                foreach ($current_image_files as $current_image_file_index=>$current_image_file)
                {
                    if (is_file($current_image_file)) unlink($current_image_file);
                }
                while (@rmdir($current_image_folder))
                {
                    $current_image_folder = dirname($current_image_folder);
                }
            }
        }
        return true;
    }
}