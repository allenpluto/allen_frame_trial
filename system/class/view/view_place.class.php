<?php
// Class Object
// Name: view_place
// Description: place view table, display street address, suburb info and show in map etc

class view_place extends view
{
    function __construct($value = null, $parameter = array())
    {
        parent::__construct(null, $parameter);

        if (!empty($value))
        {
            if (!is_array($value))
            {
                $value = [$value];
            }
            $counter = 0;
            foreach ($value as $id_index=>$id_value)
            {
                $this->id_group[':id_'.$counter] = $id_value;
                $counter++;
            }
            $this->get();
        }
    }

    function get($parameter = array())
    {
        // When id_group changes, reset the stored row value and rendered html
        $this->row = null;
        $this->rendered_html = null;

        $parameter = array_merge($this->parameter,$parameter);

        if (count($this->id_group) > 0)
        {
            $this->_initialized = true;
        }

        if (empty($parameter['bind_param']))
        {
            $parameter['bind_param'] = array();
        }

        $sql = 'SELECT '.$parameter['primary_key'].' FROM '.$parameter['table'];
        $where = array();
        if (!empty($parameter['where']))
        {
            if (is_array($parameter['where']))
            {
                $where = $parameter['where'];
            }
            else
            {
                $where[] = $parameter['where'];
            }
        }
        if ($this->_initialized)
        {
            if (!empty($this->id_group))
            {
                $where[] = $parameter['primary_key'].' IN ("'.implode('","',array_keys($this->id_group)).'")';
                $parameter['bind_param'] = array_merge($parameter['bind_param'],$this->id_group);
            }
        }

        if (!empty($where))
        {
            $sql .= ' WHERE '.implode(' AND ', $where);
        }
        else
        {
            $GLOBALS['global_message']->error = __FILE__.'(line '.__LINE__.'): '.get_class($this).' cannot retrieve records with none specific where conditions and empty id_group in view.';
            return false;
        }

        if (!empty($parameter['order']))
        {
            if (is_array($parameter['order']))
            {
                $parameter['order'] = implode(', ', $parameter['order']);
            }
            $sql .= ' ORDER BY '.$parameter['order'];
        }
        if (!empty($parameter['limit']))
        {
            $sql .= ' LIMIT '.$parameter['limit'];
        }
        if (!empty($parameter['offset']))
        {
            $sql .= ' OFFSET '.$parameter['offset'];
        }
        $result = $this->query($sql,$parameter['bind_param']);
        if ($result !== false)
        {
            $new_id_group = array();
            foreach ($result as $row_index=>$row_value)
            {
                $new_id_group[] = $row_value[$parameter['primary_key']];
            }
            // Keep the original id order if no specific "order by" is set
            if ($this->_initialized AND empty($parameter['order'])) $this->id_group = array_intersect($this->id_group, $new_id_group);
            else
            {
                $counter = 0;
                $this->id_group = [];
                foreach ($new_id_group as $id_index=>$id_value)
                {
                    $this->id_group[':id_'.$counter] = $id_value;
                    $counter++;
                }
            }

            $this->_initialized = true;
            $this->parameter['page_count'] = ceil(count($this->id_group)/$this->parameter['page_size']);
            return $this->id_group;
        }
        else
        {
            return false;
        }
    }
}
    
?>