<?php

namespace citibytes;

use citibytes\Environment;
use citibytes\CoreAttributesSchema;
use citibytes\BusinessAttributesSchema;
use citibytes\persister\SimpleDbPersister;

class BusinessDetails
{

	private $_core_attributes_schema;

	private $_domain_name;

	public function __construct()
	{
		$this->_core_attributes_schema	= new CoreAttributesSchema();
    $this->_domain_name = Environment::getCoreAttirbutesDomain();
	}

	public function get($business_id)
	{
		
    $attributes = $this->getBusinessDetails($business_id);
		$core_attributes_schema = $this->_core_attributes_schema;

    $output = array();

    //Setting all Core Attributes
		$this->copyAttributesToArray($attributes,$core_attributes_schema,$output);
		
		//Setting all Business Specific Attributes
		$business_categories = $output["business_category"];
		foreach($business_categories as $index => $business_category)
		{
			$exists = BusinessAttributesSchema::doesBusinessCategorySchemaExist(
																											$business_category);			
			if($exists === FALSE)
				continue;

			$schema_file_name = BusinessAttributesSchema::getBusinessSchemaName(
																											$business_category); 
			$business_schema = new BusinessAttributesSchema($schema_file_name);
			$this->copyAttributesToArray($attributes,$business_schema,$output);
			
		}

    return $output;		

	}

	private function copyAttributesToArray($attributes,$schema,&$output)
	{
			foreach($attributes as $attribute)
			{
				$attribute_name = $attribute["Name"];
				$attribute_value= $attribute["Value"];

				if($schema->doesAttributeExist($attribute_name) === FALSE)
					continue;

				$is_multivalued = $schema->isMultiValued($attribute_name);
				$is_value_set   = isset($output[$attribute_name]);

				/* The value is not set in output array and the attribute is not 
				 * multivalued.
				 */
				if($is_value_set === FALSE && $is_multivalued === FALSE)
					$output[$attribute_name] = $attribute_value;
				//The value is not set in output array and attribute is multivalued
				elseif($is_value_set === FALSE && $is_multivalued === TRUE)
					$output[$attribute_name] = array($attribute_value);
				/* The attribute is multivalued and the value is already set in 
				 * the output array
				 */
				elseif($is_multivalued === TRUE)
					array_push($output[$attribute_name],$attribute_value);
			}

			return $output;
	}

 	private function getBusinessDetails($business_id)
  {
		$domain_name= $this->_domain_name;
    $attributes = SimpleDbPersister::getAttributes($domain_name,
                                                   $business_id);
    return $attributes;
  }

}

?>
