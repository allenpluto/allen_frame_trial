<?php
// Class Object
// Name: view_organization
// Description: entity_organization's main view table, display everything about organization

class view_organization extends view
{
    var $parameter = array(
        'entity' => 'entity_organization',
        'table' => 'tbl_view_organization',
        'primary_key' => 'id'
    );

    function __construct($value = Null, $parameter = array())
    {
        if (!isset($parameter['page_size'])) $this->parameter['page_size'] = $GLOBALS['global_preference']->view_organization_page_size;

        parent::__construct($value, $parameter);

        return $this;
    }
}
    
?>