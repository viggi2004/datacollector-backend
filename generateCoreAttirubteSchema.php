<?php

ini_set("auto_detect_line_endings",true);

$file_name		= 'csv/core_attributes.csv';
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

	$attribute_name = $line[0];
	$is_mandatory		= $header_line[2];
	$is_multi_valued= $header_line[3];
	$is_required_for_sms_verification = $header_line[4];
	$dependencies		= $header_line[5];
	$allowed_values = $header_line[6];

	$schema_array[$attribute_name] = array(
																					 $is_mandatory => isTrue($line[2]),
																  			$is_multi_valued => isTrue($line[3]),
											 $is_required_for_sms_verification => isTrue($line[4]));

	if(empty($line[5]) === false)
		$schema_array[$attribute_name][$dependencies] = explode(",",$line[5]);
	
	if(empty($line[6]) === false)
		$schema_array[$attribute_name][$allowed_values] = explode(",",$line[6]);

}

file_put_contents("schema/core_attributes.json",json_encode($schema_array));

fclose($file_handle);

function isTrue($value)
{
	if(strcasecmp($value,"true") === 0)
		return true;
	else
		return false;
}

?>
