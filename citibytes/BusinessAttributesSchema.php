<?php

namespace citibytes;

class BusinessAttributesSchema
{

	const SCHEMA_FILE_PATH = "schema/%s.json";

	private $_schema;

	public function __construct($schema_file_name)
	{
		$schema_file_path=ROOT_DIRECTORY."/"
											.BusinessAttributesSchema::SCHEMA_FILE_PATH;
		$file_path = sprintf($schema_file_path,$schema_file_name);
		$json = file_get_contents($file_path);
    $this->_schema= json_decode($json,TRUE);
	}

	public static function doesSchemaExist($schema_file_name)
	{
    $schema_file_path=ROOT_DIRECTORY."/"
											.BusinessAttributesSchema::SCHEMA_FILE_PATH;
		$file_path = sprintf($schema_file_path,$schema_file_name);
		return file_exists($file_path);
	}

	public static function doesBusinessCategorySchemaExist($business_category)
	{
		$schema_name = BusinessAttributesSchema::getBusinessSchemaName(
																												$business_category);
		if($schema_name === NULL)
			return FALSE;

		return TRUE;
	}

/* Finds whether a UI schema file is available for the given business
 * category. If yes, the ui schema file_name is returned else NULL
 */
	public static function getBusinessSchemaName($business_category)
	{
		//Social > Entertainment > Psychics and Astrologers
  	$components = explode(">",$business_category);
  	$components = array_reverse($components);
  	foreach($components as $index => $value)
    	$components[$index] = trim($value);

  	foreach($components as $index => $value)
  	{
    	$file_name  = strtolower($value);
			$file_exists= BusinessAttributesSchema::doesSchemaExist($file_name);
    	if($file_exists === TRUE)
      	return $file_name;
  	}
  	return NULL;
  }

 	public function doesAttributeExist($attribute)
  {
    return isset($this->_schema[$attribute]);
  }


	public function isMultiValued($attribute)
	{
		if(isset($this->_schema[$attribute]) === FALSE)
			return FALSE;

		if($this->_schema[$attribute]["MULTIVALUED"] === TRUE)
			return TRUE;

		return FALSE;
	}

}

?>
