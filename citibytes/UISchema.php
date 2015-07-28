<?php

namespace citibytes;

class UISchema
{

	private $_category;

	private $_business_schema;

	private $_business_schema_keys;

	private $_generated_ui_schema_keys;

	const FILE_PATH = "schema/%s.json";

	public function __construct($business_category)
	{
		$absolute_path		= ROOT_DIRECTORY . "/" . UISchema::FILE_PATH;
		$file_path				=	sprintf($absolute_path,$business_category);	
		$contents					=	file_get_contents($file_path);
		$business_schema	=	json_decode($contents,TRUE);

		$this->_category							=	$business_category;
		$this->_business_schema				=	$business_schema;
		$this->_business_schema_keys	= array_keys($business_schema);
		$this->_generated_ui_schema_keys = array();
	}
	
	public function generate()
	{
		$business_schema			= $this->_business_schema;
		$business_schema_keys = $this->_business_schema_keys;
		$ui_schema = 	array();
		foreach($business_schema_keys as $key)
		{
			//If UI schema is generated for the current key, continue
			if(in_array($key,$this->_generated_ui_schema_keys) === TRUE)
				continue;

			$attributes	= $business_schema[$key];
			$element		=	$this->createUISchemaForKey($key,$business_schema,
																								$attributes);
			array_push($ui_schema,$element);
	
		}
		return $ui_schema;
	}

	private function createUISchemaForKey($key,$schema,$attribute)
	{
		$ui_element	=	$this->getUIType($attribute);

			
		$element											= array();
		$element["ui"]								= $ui_element;
		$element["mandatory"]					= $attribute["MANDATORY"];
		$element["mulitvalued"]				= $attribute["MULTIVALUED"];
		$element["attribute_name"]		= $key;
		$element["form_display_text"] = $attribute["FORM DISPLAY"];

		//Set the allowed values for the field
		if(isset($attribute["ALLOWED VALUES"]) === TRUE)
			$element["allowed_values"]	= $attribute["ALLOWED VALUES"];

		//Set the default value for the field
		if(isset($attribute["DEFAULT VALUE"]) ===  TRUE)
			$element["default_value"]	= $attribute["DEFAULT VALUE"];

		//Set the dependency field
		if(isset($attribute["DEPENDENCIES"]) ===  TRUE)
		{
			$dependencies = $attribute["DEPENDENCIES"];
			$dependency_schema = $this->createDependencies($schema,$dependencies);
			$element["dependencies"] = $dependency_schema ;
		}

		//Set the activate dependency on field
		if(isset($attribute["ACTIVATE DEPENDENCY ON"]) ===  TRUE)
			$element["activate_dependency_on"]	= 
																	$attribute["ACTIVATE DEPENDENCY ON"];


		if(isset($attribute["UI ELEMENT PREFIX"]) === TRUE)
			$element["ui_element_prefix"] = $attribute["UI ELEMENT PREFIX"];
		
		array_push($this->_generated_ui_schema_keys,$key);
		
		return $element;
	}

	private function createDependencies($schema,$dependencies)
	{
		$ui_schema = array();
		foreach($dependencies as $dependency)	
		{
			$attributes = $schema[$dependency];
			$element  = $this->createUISchemaForKey($dependency,$schema,$attributes);	
			array_push($ui_schema,$element);
		}
		return $ui_schema;
	}

	private function getUIType($attributes)
	{
		/* If text type is TEXT and
		 * is MULTIVALUED and
		 * has ALLOWED VALUES set and
		 * count of ALLOWED VALUES greater than 3
		 * it is multi_selection_listview
		 */
		if($attributes["TYPE"] === "TEXT" && 
			 $attributes["MULTIVALUED"] === TRUE &&
			 isset($attributes["ALLOWED VALUES"]) === TRUE &&
			 count($attributes["ALLOWED VALUES"]) >= 3)
		{
			return "multi_selection_listview";
		}
	
		/* If text type is TEXT and
		 * is MULTIVALUED and
		 * has ALLOWED VALUES set and
		 * count of ALLOWED VALUES <= 3
		 * it is checkbox
		 */
		if($attributes["TYPE"] === "TEXT" &&
    	 $attributes["MULTIVALUED"] === TRUE &&
     	 isset($attributes["ALLOWED VALUES"]) === TRUE &&
     	 count($attributes["ALLOWED VALUES"]) < 3 )
		{
			return "checkbox";
		}

		/* If text type is TEXT and
		 * is NOT MULTIVALUED and
		 * has ALLOWED VALUES set and
		 * count of ALLOWED VALUES > 3
		 * it is single_selection_listview
		 */
		if($attributes["TYPE"] === "TEXT" &&
    	 $attributes["MULTIVALUED"] === FALSE &&
     	 isset($attributes["ALLOWED VALUES"]) === TRUE &&
     	 count($attributes["ALLOWED VALUES"]) >= 3)
		{
			return "single_selection_listview";
		}
	
		/* If text type is TEXT and
		 * is NOT MULTIVALUED and
		 * has ALLOWED VALUES set and
		 * count of ALLOWED VALUES <= 3
		 * it is radiobutton
		 */
		if($attributes["TYPE"] === "TEXT" &&
    	 $attributes["MULTIVALUED"] === FALSE &&
     	 isset($attributes["ALLOWED VALUES"]) === TRUE &&
     	 count($attributes["ALLOWED VALUES"]) < 3)
		{
			return "radiobutton";
		}

		/* If text type is TEXT and
		 * is NOT MULTIVALUED and
		 * has ALLOWED VALUES NOT set and
		 * it is textbox
		 */
		if($attributes["TYPE"] === "TEXT" &&
    		 $attributes["MULTIVALUED"] === FALSE &&
     		 isset($attributes["ALLOWED VALUES"]) === FALSE)
		{
			return "textbox";
		}

		if($attributes["TYPE"] === "PHOTO")
			return "photo";

	}

}

?>
