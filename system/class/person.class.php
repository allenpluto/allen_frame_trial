<?php
// Class Object
// Name: person
// Description: person, account table, which stores all user reltated information

class person extends thing
{
	var $parameters = array(
		'name' => 'person',
		'prefix' => 'person_',
		'select_fields' => array(
			'id',
			'name',
			'alternate_name',
			'description',
			'image',
			'enter_time',
			'update_time',
			'address',
			'birth_date',
			'email',
			'family_name',
			'additional_name',
			'given_name',
			'gender'
		)
	);

	function person($parameters)
	{
		$get_parameters = array('bind_param'=>array());

		// filter out the $parameters for class construction
		// class person is allowed to be constructed by 'friendly_url' or 'id'. However, if both provided, 'id' overwrite 'friendly_url'.
		// use other functions to select a group of people

		if (is_array($parameters))
		{
			foreach ($parameters as $parameter_index => $parameter_value)
			{
				switch ($parameter_index)
				{
					case 'friendly_url':
						$get_parameters['where'] = '`friendly_url` = :friendly_url';
						$get_parameters['bind_param'][':friendly_url'] = $parameter_value;
						unset($parameters[$parameter_index]);
						break;
					case 'id':
						$get_parameters['where'] = '`id` = :id';
						$get_parameters['bind_param'][':id'] = $parameter_value;
						unset($parameters[$parameter_index]);
						break;
					/*case 'order_by':
						$get_parameters['order_by'] = $parameter_value;
						unset($parameters[$parameter_index]);
						break;
					case 'limit':
						$get_parameters['limit'] = ':limit';
						$get_parameters['bind_param'][':limit'] = $parameter_value;
						unset($parameters[$parameter_index]);
						break;
					case 'offset':
						$get_parameters['offset'] = ':offset';
						$get_parameters['bind_param'][':offset'] = $parameter_value;
						unset($parameters[$parameter_index]);
						break;*/
					default:
						break;
				}
			}
		}

		$this->setParameters($parameters);

		$this->get($get_parameters);

		return $this;
	}

	function setParameters($parameters)
	{
		if(!is_array($parameters))
		{
			$parameters = array();
		}
		$this->parameters = array_merge($this->parameters, $parameters);
	}

	function get($parameters)
	{
		$select_parameters = array_merge(array('table'=>$this->parameters['name'],'prefix'=>$this->parameters['prefix'],'columns'=>$this->parameters['select_fields']), $parameters);
		return $this->select($select_parameters);
	}

	function set($parameters)
	{
		$select_parameters = array_merge(array('table'=>$this->parameters['name'],'prefix'=>$this->parameters['prefix'],'insert_columns'=>$this->parameters['insert_fields'],'update_columns'=>$this->parameters['update_fields']), $parameters);
		$this->row = $this->select($select_parameters);

	}
}

?>