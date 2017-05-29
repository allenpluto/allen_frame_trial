<?php
// Class Object
// Name: view_gallery
// Description: image gallery

class view_gallery extends view
{
    var $parameter = array(
        'entity' => 'entity_gallery',
        'table' => 'tbl_view_gallery',
        'primary_key' => 'id'
    );

    function __construct($value = null, $parameter = array())
    {
        parent::__construct(null, $parameter);

        if (!is_null($value))
        {
            $id_group = $this->format->id_group($value);
            if ($id_group !== false)
            {
                $this->id_group = $id_group;
                $this->get();
            }
        }
    }
}
    
?>