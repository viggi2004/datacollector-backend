<?php

require_once("config.php");

use citibytes\BusinessInAPincode;
use citibytes\utils\BusinessUtils;
use citibytes\persister\SimpleDbPersister;

$pincode = $_REQUEST["pincode"];
//Get All business collected till date for a pincode
$period	 = "tilldate";

$business_in_a_pincode = new BusinessInAPincode($pincode);
$business = $business_in_a_pincode->getBusinessCollectedTillDate();

$success_json = array("status" => "success",
                      "count" => count($business),
                      "businesses" => $business);
echo json_encode($success_json);

?>
