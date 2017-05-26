<?php
// Class Object
// Name: entity_gallery
// Description: gallery, image collection

class entity_gallery extends entity
{
    function __construct($value = Null, $parameter = array())
    {
        $default_parameter = [
            'relational_fields'=>[
                'image'=>[],
                'organization'=>[]
            ]
        ];
        $parameter = array_merge($default_parameter, $parameter);
        return parent::__construct($value, $parameter);
    }
}

?>