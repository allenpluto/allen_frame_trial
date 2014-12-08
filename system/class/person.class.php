<?php
// Class Object
// Name: person
// Description: person, account table, which stores all user reltated information

class person extends thing
{
	var $parameters = array(
		'prefix' => 'person_',
		'insert_fields' => array(
			'name' => '',
			'friendly_url' => '',
			'alternate_name' => '',
			'description' => '',
			'image' => -1,
			'enter_time' => 'CURRENT_TIMESTAMP',
			'update_time' => 'CURRENT_TIMESTAMP',
			'address' => -1,
			'birth_date' => 'NULL',
			'email' => '',
			'family_name' => '',
			'additional_name' => '',
			'given_name' => '',
			'gender' => 'unspecified'
		),
		'select_fields' => array(
			'id' => 'id',
			'friendly_url' => 'friendly_url',
			'name' => 'name',
			'alternate_name' => 'alternate_name',
			'description' => 'description',
			'image' => 'image',
			'enter_time' => 'enter_time',
			'update_time' => 'update_time',
			'address' => 'address',
			'birth_date' => 'birth_date',
			'email' => 'email',
			'family_name' => 'family_name',
			'additional_name' => 'additional_name',
			'given_name' => 'given_name',
			'full_name' => 'CONCAT(given_name,\' \',additional_name,\' \',family_name)',
			'gender' => 'gender'
		),
		'update_fields' => array(
			'update_time' => 'CURRENT_TIMESTAMP'
		)
	);

	// class person is allowed to be constructed by 'friendly_url' or 'id'. However, if both provided, 'id' overwrite 'friendly_url'.
	// use other functions to select a group of people
	function person($parameters)
	{
		$get_parameters = array('bind_param'=>array());

		// filter out the $parameters for class construction
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
		// Call thing::get function
		parent::get($parameters);

		// Additional data format change code here
	}

	function set($parameters)
	{
		// Data format change code here


		// Call thing::set function
		parent::set($parameters);
	}
}

?>