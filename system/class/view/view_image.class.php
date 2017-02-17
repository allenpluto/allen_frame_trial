<?php
// Class Object
// Name: view_image
// Description: image view

class view_image extends view
{
    var $parameter = array(
        'namespace' => 'image/',
        'table' => 'tbl_view_image',
        'primary_key' => 'id',
        'image_size' => 'm',
        'page_size' => 1
    );

    function __construct($value = Null, $parameter = array())
    {
        if (!empty($value))
        {
            $entity_image_obj = new entity_image($value);
            $entity_image_obj->sync(['sync_type'=>'update_current']);
            unset($entity_image_obj);
        }

        $this->parameter['page_size'] = $GLOBALS['global_preference']->view_category_page_size;

        parent::__construct($value, $parameter);

        return $this;
    }

    function fetch_value($parameter = array())
    {
        $parameter = array_merge($this->parameter,$parameter);
        $result = parent::fetch_value($parameter);
        $sync_id_group = array();
        foreach ($result as $row_index=>$row)
        {
            if (!file_exists($row['file_path']))
            {
                $this->message->warning = 'View Image local source file does not exist'.$row['file_path'];
                $sync_id_group[] = $row['id'];
            }
        }
        if (!empty($sync_id_group))
        {
            $entity_image_obj = new entity_image($sync_id_group);
            $entity_image_obj->sync(['sync_type'=>'update_current']);
            $result = parent::fetch_value($parameter);
            unset($entity_image_obj);
        }
        return $result;
    }
}
    
?>