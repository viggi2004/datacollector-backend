<?php

date_default_timezone_set("Asia/Calcutta");
require_once("config.php");

use citibytes\SMS;
use citibytes\Environment;
use citibytes\BusinessIdGenerator;
use citibytes\utils\SimpleDbUtils;
use citibytes\utils\CoreAttributesUtils;
use citibytes\persister\SimpleDbPersister;
use citibytes\CoreAttributesSchemaValidator;


$data = $_REQUEST["json"];
$data = stripslashes($data);
$data = json_decode($data,TRUE);

$email_id		= $_REQUEST["email_id"];
$sms_number	= $_REQUEST["sms_number"];

$domain_name = Environment::getCoreAttirbutesDomain();

$is_update_operation = isset($data["business_id"]) === TRUE ? TRUE : FALSE;
//Saving new Business Core Attributes
if($is_update_operation === FALSE)
{
	$business_id = BusinessIdGenerator::generate($data["pincode"]);
	$data["business_id"]			= $business_id;
	$data["created_time"]			= date('c');
	$data["created_by_user"]	= $email_id;
}

//Generate a random 5 digit OTP	
$data["otp"]							= mt_rand(10000,99999);
$data["status"]						= "TRANSIENT";
$data["last_updated_time"]= date('c');
$data["last_updated_user"]= $email_id;


/**
 * Validate the SMSVerifiableCore Attributes. On success it
 * returns TRUE. On some error, it returns the error JSON
 */
$validator = CoreAttributesSchemaValidator::getInstance();
$is_validation_passed = $validator->validateSMSVerifiableAttributes($data);

if($is_validation_passed !== TRUE)
{
	echo $is_validation_passed;
	return;
}

//If a new business entry, check whether it exists already
if($is_update_operation === FALSE && 
	 CoreAttributesUtils::exists($data) === TRUE)
{
	$error_json = array("status" => "error" , 
											"error" => "Business already exists");
	echo json_encode($error_json);
	return;
}

//If an update operation, don't re-generate business_id and set $replace to true
if($is_update_operation === TRUE)
	$attributes = SimpleDbUtils::genSimpleDbAttributesArray($data,true);
else
{
	$is_busines_id_already_taken = false;
	/* Check whether the unique business id that is generated is already not
	 * taken.
	 */
	do{
			$business_id					= BusinessIdGenerator::generate($data["pincode"]);
			$data["business_id"]	= $business_id;
			$result = SimpleDbPersister::getAttributes($domain_name,$business_id,
																						 		 array("business_id"));
			$is_business_id_already_taken = empty($result) ? false : true; 
		}while($is_business_id_already_taken === TRUE);
	$attributes = SimpleDbUtils::genSimpleDbAttributesArray($data);
}

$message = CoreAttributesUtils::generateSMSMessage($data);
SMS::send($sms_number,$message);
$business_id = $data["business_id"];
SimpleDbPersister::save($domain_name,$business_id,$attributes);
$success_json = array("status"					=> "success" , 
											"business_id"			=> $business_id ,
											"created_by_user"	=> $data["created_by_user"],
											"created_time"		=> $data["created_time"]);
echo json_encode($success_json);
return;


?>
