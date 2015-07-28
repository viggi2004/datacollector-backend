<?php

//Temporary code till SMS gets fixed
date_default_timezone_set("Asia/Calcutta");
//

require_once("config.php");

use citibytes\Environment;
use citibytes\utils\SimpleDbUtils;
use citibytes\persister\SimpleDbPersister;
use citibytes\CoreAttributesSchemaValidator;

$otp					= $_REQUEST["otp"];
$business_id	= $_REQUEST["business_id"];
$domain_name	= Environment::getCoreAttirbutesDomain();

$result 			= SimpleDbPersister::getAttributes($domain_name,
											$business_id,array("otp","last_updated_time"));



if(empty($result) === TRUE)
{
	$error_json = array("status" => "error" , 
											"error" => "$business_id business id doesn't exist");
	echo json_encode($error_json);
	return;
}

/* Temporary code till SMS gets fixed. 
 *	Loop to get value of otp and last_updated_time from simpledb
 */
	if($result[1]["Name"] === "otp")
	{
		$last_updated_time = $result[0]["Value"];
		$result[0]["Value"]= $result[1]["Value"];
	}
	else
		$last_updated_time = $result["1"]["Value"];
//

/* Temporary code till SMS gets fixed.
 * Have a default OTP of 00000 if SMS not delivered after 1 minute
 */
$now = new DateTime();
$last_updated_time = new DateTime($last_updated_time);
$time_interval		 = $last_updated_time->diff($now);
if($time_interval->i >= 1 && $otp == "00000")
{
	goto otp_verified;
}
//

if($result[0]["Value"] !== $otp)
{
	$error_json = array("status" => "error" , 
											"error" => "Invalid OTP");
	echo json_encode($error_json);
	return;
}

//Temporary code till SMS gets fixed
otp_verified:
//

/**
 * Validate whether status can take the below status code. If yes,
 * $is_validation_passed is true else it hold an error json
 */
$data = array("status" => "BUSINESS_VERIFIED_INCOMPLETE");
$validator = CoreAttributesSchemaValidator::getInstance();
$is_validation_passed = $validator->validateAllowedValuesCheck(array("status"),
																															 $data);
if($is_validation_passed !== TRUE)
{
	echo $is_validation_passed;
	return;
}

$attributes = SimpleDbUtils::genSimpleDbAttributesArray($data,TRUE);
SimpleDbPersister::save($domain_name,$business_id,$attributes);

$success_json = array("status" => "success");
echo json_encode($success_json);



?>
