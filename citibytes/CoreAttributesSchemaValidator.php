<?php

namespace citibytes;

use citibytes\SchemaValidator;

class CoreAttributesSchemaValidator
{

	const SCHEMA_FILE_PATH = "schema/core_attributes.json";

  private $schema = null;

  private static $this_instance = null;

	private $sms_verifiable_attributes = null;

	private $non_sms_verifiable_attributes = null;

  private function __construct()
  {
		$file_path = ROOT_DIRECTORY . "/" 
								 . CoreAttributesSchemaValidator::SCHEMA_FILE_PATH;
    $json = file_get_contents($file_path);
    $this->schema= json_decode($json,TRUE);
		$this->sms_verifiable_attributes = $this->getSMSVerifiableAttributes();
		$this->non_sms_verifiable_attributes = 
																		$this->getNonSMSVerifiableAttributes();
  }

  public static function getInstance()
  {
    if(CoreAttributesSchemaValidator::$this_instance == null)
      CoreAttributesSchemaValidator::$this_instance = 
																						new CoreAttributesSchemaValidator();
    return CoreAttributesSchemaValidator::$this_instance;
  }

  public function getSMSVerifiableAttributes()
  {
    $sms_verifiable_attributes = array();
    foreach($this->schema as $core_attribute_name => $properties)
    {
      if($properties["IS_REQUIRED_FOR_SMS_VERIFICATION"] === true)
        array_push($sms_verifiable_attributes,$core_attribute_name);
    }
    return $sms_verifiable_attributes;
  }

	private function getNonSMSVerifiableAttributes()
	{
		$non_sms_verifiable_attributes = array();
    foreach($this->schema as $core_attribute_name => $properties)
    {
      if($properties["IS_REQUIRED_FOR_SMS_VERIFICATION"] === false)
        array_push($non_sms_verifiable_attributes,$core_attribute_name);
    }
    return $non_sms_verifiable_attributes;
	}

	public function validateSMSVerifiableAttributes($data)
	{
		$sms_verifiable_attributes = $this->sms_verifiable_attributes;
		return $this->validate($sms_verifiable_attributes,$data);
	}

	public function validateNonSMSVerifiableAttributes($data)
	{
		$non_sms_verifiable_attributes = $this->non_sms_verifiable_attributes;
		return $this->validate($non_sms_verifiable_attributes,$data);
	}

	private function validate($to_validate_attributes,$data)
	{
		$schema						= $this->schema;
		$data_attributes	= array_keys($data);

		/* If mandatory check is passed, returns TRUE else returns the error in JSON 		 * format 
		 */
		$is_mandatory_check_passed = SchemaValidator::validateMandatoryCheck(
																		$to_validate_attributes,$schema,$data);
		if($is_mandatory_check_passed !== TRUE)
			return $is_mandatory_check_passed;

		/* If data non emptycheck is passed, returns TRUE else returns the error in
		 * JSON format 
		 */
		$is_data_non_empty_check_passed = 
									SchemaValidator::validateDataNonEmptyCheck($data_attributes,
																														 $data);
		if($is_data_non_empty_check_passed !== TRUE)
			return $is_data_non_empty_check_passed;

		/* if multiple values check is passed, returns TRUE else returns the error
		 * in JSON format
		 */
		$is_multiple_values_check_passed = 
		SchemaValidator::validateMultipleValuesAllowedCheck($data_attributes,
																												$schema,$data);
		if($is_multiple_values_check_passed !== TRUE)
			return $is_multiple_values_check_passed;

		/* If dependcy check is passed, returns TRUE else returns the error in
		 * JSON format 
		 */
		$are_dependencies_satisfied = SchemaValidator::validateDependencyCheck(
																							$data_attributes,$schema,$data);
		if($are_dependencies_satisfied !== TRUE)
			return $are_dependencies_satisfied;

		/* If allowed values check is passed, returns TRUE else returns the error in
		 * JSON format 
		 */
		$does_take_allowed_values	= SchemaValidator::validateAllowedValuesCheck(
																						 $data_attributes,$schema,$data);
		if($does_take_allowed_values !== TRUE)
			return $does_take_allowed_values;

		return TRUE;

	}

	public function validateAllowedValuesCheck($attributes,$data)
	{
		$schema			= $this->schema;
		$does_take_allowed_values	= SchemaValidator::validateAllowedValuesCheck(
																									$attributes,$schema,$data);
		if($does_take_allowed_values !== TRUE)
			return $does_take_allowed_values;

		return TRUE;

	}
	
}

?>
