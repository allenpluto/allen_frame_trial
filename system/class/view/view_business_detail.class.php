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

    function fetch_value($parameter = array())
    {
        if (parent::fetch_value($parameter) === false)
        {
            return false;
        }
        foreach ($this->row as $row_index=>&$row_value)
        {
            if (!empty($row_value['keywords']))
            {
                $row_value['keywords'] = ['_value'=>explode("\n",$row_value['keywords']),'_parameter'=>['separator'=>', ']];
            }
        }
        return $this->row;
    }
}
    
?>