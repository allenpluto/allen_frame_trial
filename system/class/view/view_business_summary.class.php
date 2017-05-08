<?php
// Class Object
// Name: view_business_summary
// Description: business summary block view, derived from view_organization

class view_business_summary extends view_organization
{
    function __construct($value = Null, $parameter = array())
    {
        $this->parameter['page_size'] = $GLOBALS['global_preference']->view_business_summary_page_size;
        $this->parameter['table_fields'] = [
            'id'=>'id',
            'friendly_uri'=>'friendly_uri',
            'name'=>'name',
            'description'=>'description',
            'street_address'=>'street_address',
            'suburb'=>'suburb',
            'state'=>'state',
            'post'=>'post',
            'image'=>'image',
            'logo'=>'logo',
            'schema_itemtype'=>'schema_itemtype'
        ];

        parent::__construct($value, $parameter);

        return $this;
    }
}
    
?>