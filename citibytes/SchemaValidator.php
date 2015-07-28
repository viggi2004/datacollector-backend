<?php

namespace citibytes;

class SchemaValidator
{

	/**
	 *	Performs mandatory validity check	
	 *
	 *	@param $attributes Attributes on which mandatory check should be done
	 *	@param $schema		 JSON decoded representation(array) of the table schema
	 *	@param $data			 JSON decoded representation(array) of data to save
	 *
	 *	@return boolean Indicating whether all attributes pass the mandatory
	 *									check
	 */

	public static function validateMandatoryCheck($attributes,$schema,$data)
	{
		foreach($attributes as $index => $attribute)
		{
			$is_mandatory = $schema[$attribute]["MANDATORY"] ? TRUE : FALSE;
			if($is_mandatory === FALSE)
				continue;

			if(isset($data[$attribute]) === FALSE)
				return SchemaValidator::errorJSON("$attribute is mandatory");
		}
		return TRUE;		
	}

	/**
	 *	Performs multiple values allowed check
	 *
	 *	@param $attributes Attributes on which multiple values check to be done
	 *	@param $schema		 JSON decoded representation(array) of the table schema
	 *	@param $data			 JSON decoded representation(array) of data to save
	 */

	public static function validateMultipleValuesAllowedCheck($attributes,
																													$schema,$data)
	{
		foreach($attributes as $index => $attribute)
		{
			$is_multivalued = $schema[$attribute]["MULTIVALUED"] ? TRUE : FALSE;
			if($is_multivalued === FALSE)
				continue;

			if(is_array($data[$attribute]) === FALSE)
			{
				$error = "$attribute is multi-valued. "
								."Pass JSON Array as value";
				return SchemaValidator::errorJSON($error);
			}
		}
		return TRUE;
	}


	public static function validateDataNonEmptyCheck($attributes,$data)
	{
		foreach($attributes as $index => $attribute)
		{
			if(is_null($data[$attribute]) === TRUE || $data[$attribute] === "")
				return SchemaValidator::errorJSON("$attribute cannot be empty");
		}
		return TRUE;
	}

	public static function validateDependencyCheck($attributes,$schema,$data)
	{
		foreach($attributes as $index => $attribute)
		{
			$dependencies = isset($schema[$attribute]["DEPENDENCIES"]) 
													? $schema[$attribute]["DEPENDENCIES"] : FALSE;
			if($dependencies === FALSE)
				continue;

			foreach($dependencies as $index => $dependency)
			{
				if(isset($data[$dependency]) === FALSE)
				{
					$error = "$dependency attribute is missing as "
									."$attribute requires $dependency";
					return SchemaValidator::errorJSON($error);
				}
			}
		}
		return TRUE;		
	}

	public static function validateAllowedValuesCheck($attributes,$schema,$data)
	{
		foreach($attributes as $index => $attribute)
		{
			$allowed_values = isset($schema[$attribute]["ALLOWED VALUES"]) 
														? $schema[$attribute]["ALLOWED VALUES"] : FALSE;
			if($allowed_values === FALSE)
				continue;

			$is_matched = FALSE;
			foreach($allowed_values as $index => $value)
			{
				if($data[$attribute] === $value)
				{
					$is_matched = TRUE;
					break;
				}
			}
	
			if($is_matched === FALSE)
			{
				$allowed_values_string = implode(",",$allowed_values);
				$error = "Allowed values for $attribute are $allowed_values_string. "
								."Cannot hold {$data[$attribute]} as content for $attribute";
				return SchemaValidator::errorJSON($error);
			}
		}
		return TRUE;		
	}



	public static function errorJSON($error_message)
	{
		$error_json = array("status" => "error" , "error" => $error_message);
		return json_encode($error_json);
	}

	
}


?>
