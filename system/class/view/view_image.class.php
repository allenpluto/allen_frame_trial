<?php
// Class Object
// Name: view_image
// Description: image view

class view_image extends view
{
    var $parameter = array(
        'namespace' => 'image/',
        'table' => '`tbl_view_image`',
        'primary_key' => 'id',
        'image_size' => 'm',
        'page_size' => 1
    );

    function fetch_value($parameter = array())
    {
        $parameter = array_merge($this->parameter,$parameter);
        $result = parent::fetch_value($parameter);
        return $result;
    }

}
    
?>