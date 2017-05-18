<?php
// Class Object
// Name: view_business_detail_logo
// Description: image view

class view_business_detail_logo extends view_image
{
    function fetch_value($parameter = array())
    {
        if (!isset($parameter['image_size'])) $parameter['image_size'] = 'xs';
        $result = parent::fetch_value($parameter);
        if ($result !== false AND is_array($this->row))
        {
            foreach ($this->row as $row_index=>$row_value)
            {
                $this->row[$row_index]['xs_file_uri'] = URI_IMAGE . $row_value['friendly_uri'] . '-' . $row_value['id'] . '.xs.' . $row_value['file_extension'];
            }
            $result = $this->row;
        }
        return $result;
    }
}


?>