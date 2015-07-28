<?php

require_once("../config.php");

use citibytes\BusinessInAPincode;
use citibytes\utils\BusinessUtils;
use citibytes\persister\SimpleDbPersister;

$pincode = $_REQUEST["pincode"];
$period	 = $_REQUEST["period"];

$business = getBusinessInAPincode($pincode,$period);

$success_json = array("status" => "success",
											"count" => count($business),
											"businesses" => $business);
echo json_encode($success_json);

/**
 *	Get business collected for a pincode under a given time frame 
 */
function getBusinessInAPincode($pincode,$period)
{
	$business_in_a_pincode = new BusinessInAPincode($pincode);

 	if($period === "today")
		$business = $business_in_a_pincode->getBusinessCollectedToday();
  elseif($period === "weekly")
  	$business = $business_in_a_pincode->getBusinessCollectedThisWeek();
  elseif($period === "monthly")
  	$business = $business_in_a_pincode->getBusinessCollectedThisMonth();
  elseif($period === "tilldate")
  	$business = $business_in_a_pincode->getBusinessCollectedTillDate();
	else
    $business = $business_in_a_pincode->getBusinessCollectedToday();			

	return $business;
}

?>
