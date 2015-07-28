<?php

require_once("config.php");

use citibytes\Environment;
use citibytes\S3PhotoDeleter;
use citibytes\BusinessDetails;
use citibytes\utils\SimpleDbUtils;
use citibytes\persister\SimpleDbPersister;

date_default_timezone_set("Asia/Calcutta");



$data = $_REQUEST["json"];
$data = stripslashes($data);
$data = json_decode($data,TRUE);

$business_id = $_REQUEST["business_id"];
$schema_name = $_REQUEST["schema_name"];
$domain_name = Environment::getCoreAttirbutesDomain();
$backup_domain_name = Environment::getCoreAttributesBackupDomain();

//Set the last_updated_time
$last_updated_time	= date('c');
$data["last_updated_time"]= $last_updated_time;

/**
 * If an update operation, the old photos is S3 has to be
 * deleted. Fetching the old photos path in S3
 */
$business_details = new BusinessDetails();
$business_details = $business_details->get($business_id);

error_log('fucked up '.$business_details);

if(isset($business_details["photo_url"]) === TRUE)
	$photo_url_array = $business_details["photo_url"];
else
	$photo_url_array = array();

//TODO: Clean this code
if(isset($data["business_specific_attributes"]) === TRUE)
{
	$keys = array_keys($data["business_specific_attributes"]);
	$business_specific_attributes = array();
	foreach($data["business_specific_attributes"] as $key => $value)
		$business_specific_attributes[$key] = $value;				
	unset($data["business_specific_attributes"]);	
	$data = array_merge($data,$business_specific_attributes);
}

/* Encoding it as JSON because hours_of_operation should be saved as a JSON
 * in SimpleDb.
 */
$data["hours_of_operation"] = json_encode($data["hours_of_operation"]);
$data["status"]	= "BUSINESS_VERIFIED_COMPLETE";

$attributes = SimpleDbUtils::genSimpleDbAttributesArray($data,TRUE);
SimpleDbPersister::save($domain_name,$business_id,$attributes);

$success_json = array("status" => "success",
											"last_updated_time" => $last_updated_time);
echo json_encode($success_json);
fastcgi_finish_request();

//Backing up old data
$attributes = SimpleDbUtils::genSimpleDbAttributesArray($business_details,TRUE);
SimpleDbPersister::save($backup_domain_name,$business_id,$attributes);

//Delete old S3 Photos
if(empty($photo_url_array) === TRUE)
	return;
$new_photo_url_array = $data["photo_url"];

S3PhotoDeleter::delete($photo_url_array,$new_photo_url_array);
return;

?>
