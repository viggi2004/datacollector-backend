<?php

require_once("config.php");

use citibytes\CityPincodes;
use citibytes\utils\MysqlUtils;
use citibytes\PendingPincodeRequests;
use citibytes\ApprovedPincodeRequests;
use citibytes\exceptions\QueryFailedException;
use citibytes\exceptions\DatabaseConnectionException;


$city   = $_REQUEST["city"];
$email  = $_REQUEST["email"];

if(empty($city) === TRUE || empty($email) === TRUE)
{
	$error_json = array("status"=>"error","error"=>"Empty Input Parameters");
	echo json_decode($error_json);
	return;
}

try{

	$connection = MysqlUtils::getConnection();

	$approved_pincode_requests  = new ApprovedPincodeRequests();
	$approved_pincodes = $approved_pincode_requests->getApprovedPincodes(
																											$connection,$email,$city);

	$pending_pincode_requests = new PendingPincodeRequests();
	$unapproved_pincodes = $pending_pincode_requests->getUnApprovedPincodes(
																										$connection,$email,$city);

	$city_pincodes = new CityPincodes($city);
	$all_pincodes	=	$city_pincodes->getAllPincodes($connection);

	foreach($approved_pincodes as $approved_pincode)
		unset($all_pincodes[$approved_pincode]);

	foreach($unapproved_pincodes as $unapproved_pincode)
		unset($all_pincodes[$unapproved_pincode]);

	$count = count($all_pincodes);

	echo json_encode(array("status" => "success",
												 "count"	=> $count, 
												 "pincodes" => $all_pincodes));

}catch(DatabaseConnectionException $e){

    $failure_json = array("status"  => "error" ,
                          "error"   => "Database Unavailable");
    echo json_encode($failure_json);
    return;
  }catch(QueryFailedException $e){
    $failure_json = array("status" => "error" ,
                          "error" => "Unable to get pending pincode requests");
    echo json_encode($failure_json);
    return;
  }

  mysqli_close($connection);



?>
