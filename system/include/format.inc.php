<?php
// Include Class Object
// Name: format
// Description: format functions

// Format Related Functions


class format
{
	function friendly_url($value)
	{
		$value = strtolower($value);
		$value = preg_replace('/[^a-z0-9]/', '-', $value);
		$value = preg_replace('/-+/', '-', $value);
		$result = trim($value,'-');

		return $result;
	}
}

?>