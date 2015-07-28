<?php

ini_set("auto_detect_line_endings",true);

$business_category = $argv[1];

$file_name		= "csv/$business_category.csv";
$file_handle	= fopen($file_name,"r");

/**
 * FIELD NAME,DESCRIPTION,MANDATORY,MULTIVALUED,
 * IS_REQUIRED_FOR_SMS_VERIFICATION,DEEPENDENCIES,ALLOWED VALUES 
 */
$line_number = 0;
$header_line = null;
$schema_array = array();
$line_array		= array();
while(($line = fgetcsv($file_handle,$file_name)) !== FALSE)
{
	$line_number++;
	array_push($line_array,$line);

	//Skip first line as they are the column headers in excel sheet
	if($line_number == 1)
	{
		$header_line = $line;
		continue;
	}

	$attribute_name					= $line[0];
	$form_display_text			= $header_line[1];
	$data_type							= $header_line[3];
	$is_multi_valued				= $header_line[4];
	$is_mandatory						= $header_line[5];
	$allowed_values 				= $header_line[6];
	$default_value					= $header_line[7];
	$dependencies						= $header_line[8];
	$activate_dependency_on = $header_line[9];
	$ui_element_prefix			= $header_line[10];

	$schema_array[$attribute_name] = array(
																			$form_display_text => trim($line[1]),
																			$data_type				 =>	$line[3],
																					 $is_mandatory => isTrue($line[5]),
																  			$is_multi_valued => isTrue($line[4]));

	if(empty($line[6]) === false)
	{
		$values = explode(",",$line[6]);
		$schema_array[$attribute_name][$allowed_values]	= cleanData($values);
	}
	
	if(empty($line[7]) === false)
		$schema_array[$attribute_name][$default_value]	= trim($line[7]);

	if(empty($line[8]) === false)
	{
		$values = explode(",",$line[8]);
		$schema_array[$attribute_name][$dependencies]	= cleanData($values);
	}
	
	if(empty($line[9]) === false)
		$schema_array[$attribute_name][$activate_dependency_on]	= trim($line[9]);

	if(empty($line[10]) === false)
		$schema_array[$attribute_name][$ui_element_prefix]	= trim($line[10]);

}

file_put_contents("schema/$business_category.json",json_encode($schema_array));

fclose($file_handle);

function isTrue($value)
{
	if(strcasecmp($value,"true") === 0)
		return true;
	else
		return false;
}

function cleanData($data)
{
	if(is_array($data) === TRUE)
	{
		foreach($data as $index=> $value)
			$data[$index] = trim($value);
	}
	return $data;
}

?>
