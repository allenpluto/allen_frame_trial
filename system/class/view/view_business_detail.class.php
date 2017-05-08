<?php
// Class Object
// Name: view_business_detail
// Description: business detail block view, derived from view_organization

class view_business_detail extends view_organization
{
    function __construct($value = Null, $parameter = array())
    {
        $this->parameter['page_size'] = 1;
        
        parent::__construct($value, $parameter);

        return $this;
    }
}
    
?>