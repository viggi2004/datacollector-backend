<?php

namespace citibytes\utils;

class BusinessUtils
{

	/**
	 * Converts an item from simpledb into an associative array
	 *
	 * @param $attributes Attributes of an item in simpledb
	 *
	 * @return An associative array containing the business data
	 */
	public static function toArray($attributes)
	{
		
		$output = array();

		$is_latitude_field_available = false;

		foreach($attributes as $attribute)
		{
			$attribute_name = $attribute["Name"];
			$attribute_value= $attribute["Value"];

			if($attribute_name === "latitude")
				$is_latitude_field_available = true;

			if(isset($output[$attribute_name]) === FALSE)
				$output[$attribute_name] = $attribute_value;
			elseif(is_array($output[$attribute_name]) === TRUE)
				array_push($output[$attribute_name],$attribute_value);
			else
				$output[$attribute_name] = array($output[$attribute_name],
																				$attribute_value);
		}

		//If the location data is not available, don't send this item
		if($is_latitude_field_available === FALSE)
			return NULL;

		return $output;

		} 
}


?>
