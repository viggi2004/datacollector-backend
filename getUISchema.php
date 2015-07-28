<?php

require_once("config.php");

use citibytes\UISchema;
use citibytes\BusinessAttributesSchema;

$json = $_REQUEST["json"];
//$json = file_get_contents("testdata/business_categories.json");
$business_categories = json_decode($json,TRUE);
$business_categories = $business_categories["business_categories"];

foreach($business_categories as $business_category)
{
	$business_schema_name = BusinessAttributesSchema::getBusinessSchemaName(
																													$business_category);
	if(is_null($business_schema_name) === FALSE)
	{
		$ui_schema = new UISchema($business_schema_name);
		$ui_schema = $ui_schema->generate();	
		$output		 = array("status"			 => "success",
											 "schema"			 => $ui_schema,
											 "schema_name" => $business_schema_name);
		echo json_encode($output);	
		return;
	}
}

$output = array("status" => "failed");
echo json_encode($output);


?>

