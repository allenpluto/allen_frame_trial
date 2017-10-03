<?php
// Class Object
// Name: index_organization
// Description: organization's main index table, include all possible search fields

class index_organization extends index
{
    function __construct($value = Null, $parameter = array())
    {
        parent::__construct($value, $parameter);

        return $this;
    }

    function filter_by_account($parameter = array())
    {
        if (empty($parameter['account_id'])) return false;
        $filter_parameter = array(
            'where' => 'account = :account',
            'bind_param' => [':account'=>$parameter['account_id']]
        );

        $filter_parameter = array_merge($filter_parameter, $parameter);
//print_r($filter_parameter);
        return parent::get($filter_parameter);
    }

    function filter_by_active($parameter = array())
    {
        $filter_parameter = array(
            'where' => 'status = \'A\''
        );

        $filter_parameter = array_merge($filter_parameter, $parameter);
        return parent::get($filter_parameter);
    }

    function filter_by_featured($parameter = array())
    {
        $filter_parameter = array(
            'primary_key' => 'listing_id',
            'table' => 'ListingFeatured'
        );

        $filter_parameter = array_merge($filter_parameter, $parameter);
        return parent::get($filter_parameter);
    }

    function filter_by_service_area($parameter = array())
    {
        if (empty($parameter['postcode_suburb_id'])) return false;
        if (!is_array($parameter['postcode_suburb_id']))
        {
            $parameter['postcode_suburb_id'] = [$parameter['postcode_suburb_id']];
        }

        $filter_parameter = array(
            'primary_key' => 'listing_id',
            'table' => 'Listing_ServiceArea',
            'where' => [
                'date_end >= CURDATE()',
                'postcode_suburb_id IN ('.implode(',',$parameter['postcode_suburb_id']).')'
            ]
        );

        $filter_parameter = array_merge($filter_parameter, $parameter);

        return parent::get($filter_parameter);
    }

    // Exact Match Search
    function filter_by_category($value, $parameter = array())
    {
        $format = format::get_obj();
        $category_id_group = $format->id_group(array('value'=>$value,'key_prefix'=>':category_id_'));
        if ($category_id_group === false)
        {
            $GLOBALS['global_message']->error = __FILE__.'(line '.__LINE__.'): '.get_class($this).' invalid category id(s)';
            return false;
        }

        $filter_parameter = array(
            'primary_key' => 'listing_id',
            'table' => 'Listing_Category',
            'where' => 'category_id IN ('.implode(',',array_keys($category_id_group)).')',
        );

        $filter_parameter = array_merge($filter_parameter, $parameter);
        if (!isset($filter_parameter['bind_param'])) $filter_parameter['bind_param'] = array();
        $filter_parameter['bind_param'] = array_merge($filter_parameter['bind_param'], $category_id_group);

        return parent::get($filter_parameter);
    }

    function filter_by_place_uri($value)
    {
        // TODO: find listing place by given uri
        $bind_param = [];
        $join_table = [];

        if (!empty($value['state']))
        {
            $bind_param[':state'] = $value['state'];
            $join_table[] = 'JOIN tbl_entity_place tbl_locality ON tbl_locality.id = '.$this->parameter['table'].'.locality AND tbl_locality.friendly_uri = :state';
        }

        if (!empty($value['region']))
        {
            $bind_param[':region'] = $value['region'];
            $join_table[] = 'JOIN tbl_entity_place tbl_locality ON tbl_locality.id = '.$this->parameter['table'].'.locality AND tbl_locality.friendly_uri = :region';
        }

        if (!empty($value['suburb']))
        {
            $bind_param[':suburb'] = $value['suburb'];
            $join_table[] = 'JOIN tbl_entity_place tbl_locality ON tbl_locality.id = '.$this->parameter['table'].'.locality AND tbl_locality.friendly_uri = :suburb';
        }
    }

    function filter_by_place_id($value, $parameter = array())
    {
        if (empty($value))
        {
            $GLOBALS['global_message']->warning = __FILE__.'(line '.__LINE__.'): '.get_class($this).' filter place not set';
            return false;
        }

        if (is_string($value))
        {
            $value = explode(',',$value);
        }
        $value = array_values($value);

        $filter_parameter = [
            'bind_param' => []
        ];
        foreach ($value as $key=>$item)
        {
            $filter_parameter['bind_param'][':place_id_'.$key] = $item;
        }

        if (!isset($parameter['type']))
        {
            $filter_parameter['where'] = 'locality IN ('.implode(',',array_keys($filter_parameter['bind_param'])).') OR administrative_area_level_2 IN ('.implode(',',array_keys($filter_parameter['bind_param'])).') OR administrative_area_level_1 IN ('.implode(',',array_keys($filter_parameter['bind_param'])).')';
        }
        else
        {
            switch($parameter['type'])
            {
                case 'locality':
                    $filter_parameter['where'] = 'locality IN ('.implode(',',array_keys($filter_parameter['bind_param'])).')';
                    break;
                case 'administrative_area_level_2':
                    $filter_parameter['where'] = 'administrative_area_level_2 IN ('.implode(',',array_keys($filter_parameter['bind_param'])).')';
                    break;
                case 'administrative_area_level_1':
                    $filter_parameter['where'] = 'administrative_area_level_1 IN ('.implode(',',array_keys($filter_parameter['bind_param'])).')';
                    break;
                default:
                    $filter_parameter['where'] = 'locality IN ('.implode(',',array_keys($filter_parameter['bind_param'])).') OR administrative_area_level_2 IN ('.implode(',',array_keys($filter_parameter['bind_param'])).') OR administrative_area_level_1 IN ('.implode(',',array_keys($filter_parameter['bind_param'])).')';
            }
            unset($parameter['type']);
        }

        $filter_parameter = array_merge($filter_parameter, $parameter);

        return parent::get($filter_parameter);
    }


    // Fuzzy Search
    function filter_by_keyword($value, $parameter = array())
    {
        $original_id_group = $this->id_group;
        $original_initialized = $this->_initialized;
        $filter_parameter = array(
            'value'=> $value,
            'special_pattern'=>'',
            'fulltext_index_key'=>'fulltext_category'
        );
        $filter_parameter = array_merge($filter_parameter,$parameter);
        if (count($this->id_group) > 0)
        {
            return $this->full_text_search($filter_parameter);
        }
        else
        {
            $this->id_group = $original_id_group;
            $this->_initialized = $original_initialized;
        }
        $filter_parameter = array(
            'value'=> $value,
            'special_pattern'=>'\&',
            'fulltext_index_key'=>'fulltext_keyword'
        );
        $filter_parameter = array_merge($filter_parameter,$parameter);
        return $this->full_text_search($filter_parameter);
    }

    function filter_by_location($value, $parameter = array())
    {
        $filter_parameter = array(
            'value'=> $value,
            'fulltext_index_key'=>'fulltext_location'
        );
        $filter_parameter = array_merge($filter_parameter,$parameter);
        return $this->full_text_search($filter_parameter);
    }

    function filter_by_uri($value, $parameter = array())
    {
        $value = trim(preg_replace('/^(https?:\/\/)?(www\.)?/','',$value),'/');
        if (empty($value))
        {
            // Error Handling, empty uri string
            $this->message->notice = 'filter_by_uri, input string is not in correct URI format';
            return false;
        }

        return $this->get(['where'=>'url LIKE CONCAT("%",:url,"%")','bind_param'=>[':url'=>$value]]);
    }


}

?>