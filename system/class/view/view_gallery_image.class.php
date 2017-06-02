<?php
// Class Object
// Name: view_gallery_image
// Description: image view

class view_gallery_image extends view_image
{
    function fetch_value($parameter = array())
    {
        if (!isset($parameter['image_size'])) $parameter['image_size'] = 's';
        $result = parent::fetch_value($parameter);
        if ($result !== false AND is_array($this->row))
        {
            foreach ($this->row as $row_index=>$row_value)
            {
                $this->row[$row_index]['xs_file_uri'] = URI_IMAGE . $row_value['friendly_uri'] . '-' . $row_value['id'] . '.xs.' . $row_value['file_extension'];
                $this->row[$row_index]['s_file_uri'] = URI_IMAGE . $row_value['friendly_uri'] . '-' . $row_value['id'] . '.s.' . $row_value['file_extension'];
            }
            $result = $this->row;
        }
        return $result;
    }
}


?>