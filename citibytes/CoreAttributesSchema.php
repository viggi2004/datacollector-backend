<?php

namespace citibytes;

class CoreAttributesSchema
{

	const SCHEMA_FILE_PATH = "schema/core_attributes.json";

	private $_schema;

	public function __construct()
	{
		$schema_file_path=ROOT_DIRECTORY."/".CoreAttributesSchema::SCHEMA_FILE_PATH;
		$json = file_get_contents($schema_file_path);
    $this->_schema= json_decode($json,TRUE);
	}

	public function doesAttributeExist($attribute)
	{
		return isset($this->_schema[$attribute]);
	}

	public function isMultiValued($attribute)
	{
		if($this->doesAttributeExist($attribute) === FALSE)
			return FALSE;

		if($this->_schema[$attribute]["MULTIVALUED"] === TRUE)
			return TRUE;

		return FALSE;
	}

}


?>
