<?php
// Class Object
// Name: entity_image
// Description: Image Source File, store image in big size, for gallery details, source file to crop... All variation of images (different size thumbs) goes to image_variation table.

// image_id in image_object reference to source image. One source image may have zero to multiple thumbnail (cropped versions) for different scenario. Only source image may save exifData, any thumbnail can be regenerated using source image exifData and 
class entity_eml extends entity
{

    function __construct($value = Null, $parameter = array())
    {
        parent::__construct($value, $parameter);

        return $this;
    }

    function set($parameter = array())
    {
        $result = parent::set($parameter);
        return $result;
    }

    function sync($parameter = array())
    {
        $sync_parameter = $parameter;
        return parent::sync($sync_parameter);
    }

    function advanced_sync_update(&$source_row = array())
    {
        parent::advanced_sync_update($source_row);

        return $source_row;
    }

    function advanced_sync_delete($delete_id_group = array())
    {
        $row = $this->get();

        if ($row == false)
        {
            $this->message->warning = 'Invalid delete id(s) ['.implode(',',$delete_id_group).']. id provided might have been deleted already.';
            return false;
        }


        return true;
    }
}