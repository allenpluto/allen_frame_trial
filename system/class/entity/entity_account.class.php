<?php
// Class Object
// Name: entity_account
// Description: account table, which stores all user related information

class entity_account extends entity
{
    function __construct($value = Null, $parameter = array())
    {
        $default_parameter = [
            'relational_fields'=>[
                'place'=>[]
            ]
        ];
        $parameter = array_merge($default_parameter, $parameter);
        return parent::__construct($value, $parameter);
    }

    function set($parameter = array())
    {
        if (isset($parameter['row']))
        {
            foreach ($parameter['row'] as $record_index=>&$record)
            {
                if (isset($record['password']))
                {
                    $record['password'] = hash('sha256',hash('md5',$record['password']));
                }
            }
        }
        parent::set($parameter);
    }

    function update($value = array(), $parameter = array())
    {
        if (isset($value['password']))
        {
            $value['password'] = hash('sha256',hash('md5',$value['password']));
        }
        return parent::update($value, $parameter);
    }

    function authenticate($parameter = array())
    {
        if (empty($parameter['username']) OR empty($parameter['password']))
        {
            // username and password cannot be empty
            $this->message->notice = 'Username and password cannot be empty';
            return false;
        }
        $param = array(
            'bind_param' => array(':name'=>$parameter['username'],':password'=>hash('sha256',hash('md5',$parameter['password']))),
            'where' => array('(`name` = :name OR `alternate_name` = :name)','`password` = :password')
        );
        $row = $this->get($param);
        if (empty($this->id_group))
        {
            // Error, Invalid login
            $this->message->notice = 'invalid login';
            return false;
        }
        if (count($this->id_group) > 1)
        {
            // Error, Multiple accounts match, should never happen
            $this->message->warning = 'multiple login matched';
        }
        return end($this->row);
    }

    function sync($parameter = array())
    {
        $sync_parameter = array();

        // set default sync parameters for view table
        $sync_parameter['sync_table'] = 'tbl_view_account';
        $sync_parameter['update_fields'] = array(
            'id' => 'tbl_entity_account.id',
            'friendly_uri' => 'tbl_entity_account.friendly_uri',
            'name' => 'tbl_entity_account.name',
            'alternate_name' => 'tbl_entity_account.alternate_name',
            'description' => 'tbl_entity_account.description',
            'image_id' => 'tbl_entity_account.image_id',
            'enter_time' => 'tbl_entity_account.enter_time',
            'update_time' => 'tbl_entity_account.update_time',
            'view_time' => '"'.date('Y-m-d H:i:s').'"',
            'banner_id' => 'tbl_entity_account.banner_id',
            'first_name' => 'tbl_entity_account.first_name',
            'last_name' => 'tbl_entity_account.last_name',
            'company' => 'tbl_entity_account.company',
            'telephone' => 'tbl_entity_account.telephone',
            'fax' => 'tbl_entity_account.fax_number',
            'website' => 'tbl_entity_account.website_uri',
            'number_views' => 'tbl_entity_account.number_views',
            'image' => 'image_view.file_uri',
            'banner' => 'banner_view.file_uri',
            'latitude' => 'tbl_entity_place.location_latitude',
            'longitude' => 'tbl_entity_place.location_longitude',
            'formatted_address' => 'tbl_entity_place.formatted_address',
            'organization' => 'GROUP_CONCAT(DISTINCT tbl_entity_organization.id ORDER BY tbl_entity_organization.update_time DESC)',
            'locked' => 'IF(CURDATE()>=tbl_entity_account.lock_time, 0, 1)'
        );

        $sync_parameter['join'] = array(
            'LEFT JOIN tbl_view_image image_view ON tbl_entity_account.image_id = image_view.id',
            'LEFT JOIN tbl_view_image banner_view ON tbl_entity_account.banner_id = banner_view.id',
            'LEFT JOIN tbl_entity_place ON tbl_entity_account.place_id = tbl_entity_place.id',
            'LEFT JOIN tbl_entity_organization ON tbl_entity_account.id = tbl_entity_organization.account_id'
        );

        $sync_parameter['group'] = array(
            'tbl_entity_account.id'
        );

        $sync_parameter['fulltext_key'] = array();

        $sync_parameter = array_merge($sync_parameter, $parameter);

        $result[] = parent::sync($sync_parameter);

        return $result;
    }
}

?>