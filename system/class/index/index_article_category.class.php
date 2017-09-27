<?php
// Class Object
// Name: index_article_category
// Description: organization's main index table, include all possible search fields

class index_article_category extends index
{
    function __construct($value = Null, $parameter = array())
    {
        parent::__construct($value, $parameter);

        return $this;
    }

    function filter_by_active($parameter = array())
    {
        $filer_parameter = array(
            'where' => 'status = \'A\''
        );

        $filer_parameter = array_merge($filer_parameter, $parameter);
        return parent::get($filer_parameter);
    }

    function filter_by_article_count($parameter = array())
    {
        $filer_parameter = array(
            'get_field' => array(
                'organization_count' => 'COUNT(*)'
            ),
            'primary_key' => 'category_id',
            'table' => 'tbl_entity_article',
            'group' => 'category_id',
            'order' => 'organization_count DESC'
        );

        $filer_parameter = array_merge($filer_parameter, $parameter);
        $result = parent::get($filer_parameter);
        return $result['organization_count'];
    }

    function filter_by_article($value, $parameter = array())
    {
        $format = format::get_obj();
        $article_id_group = $format->id_group(array('value'=>$value,'key_prefix'=>':article_id_'));
        if ($article_id_group === false)
        {
            $GLOBALS['global_message']->error = __FILE__.'(line '.__LINE__.'): '.get_class($this).' invalid article id(s)';
            return false;
        }

        $filter_parameter = array(
            'primary_key' => 'category_id',
            'table' => 'tbl_entity_article',
            'where' => 'id IN ('.implode(',',array_keys($article_id_group)).')',
        );

        $filter_parameter = array_merge($filter_parameter, $parameter);
        if (!isset($filter_parameter['bind_param'])) $filter_parameter['bind_param'] = array();
        $filter_parameter['bind_param'] = array_merge($filter_parameter['bind_param'], $article_id_group);

        return parent::get($filter_parameter);
    }

    // Fuzzy Search
    function filter_by_keyword($value, $parameter = array())
    {
        $filter_parameter = array(
            'value'=> $value,
            'special_pattern'=>'\&\'',
            'fulltext_index_key'=>'fulltext_keyword'
        );
        $filter_parameter = array_merge($filter_parameter,$parameter);
        return $this->full_text_search($filter_parameter);
    }

}

?>