<?php

ini_set("auto_detect_line_endings",true);

$file_name		= 'csv/chain_list.csv';
$file_handle	= fopen($file_name,"r");

/**
 * chain_id,chain_name 
 */
$line_number = 0;
$header_line = null;
$schema_array= array();

while(($line = fgetcsv($file_handle,$file_name)) !== FALSE)
{
	$line_number++;

	//Skip first line as they are the column headers in excel sheet
	if($line_number == 1)
	{
		$header_line = $line;
		continue;
	}

	$attribute_name	= $line[0];
	$is_mandatory   = $header_line[2];

	$schema_array[$attribute_name] = array($is_mandatory => isTrue($line[2]));
}

$chain_list_schema_json = json_encode($schema_array);
file_put_contents("schema/chain_list.json",$chain_list_schema_json);

fclose($file_handle);

function isTrue($value)
{
  if(strcasecmp($value,"true") === 0)
    return true;
  else
    return false;
}

?>
