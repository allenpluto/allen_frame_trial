<?php
// Class Object
// Name: index_article
// Description: article's main index table, include all possible search fields

class index_article extends index
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
            'where' => 'category_id IN ('.implode(',',array_keys($category_id_group)).')',
        );

        $filter_parameter = array_merge($filter_parameter, $parameter);
        if (!isset($filter_parameter['bind_param'])) $filter_parameter['bind_param'] = array();
        $filter_parameter['bind_param'] = array_merge($filter_parameter['bind_param'], $category_id_group);

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
}

?>