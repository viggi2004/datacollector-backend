<?php

require_once("config.php");

use citibytes\CityPincodes;
use citibytes\utils\MysqlUtils;
use citibytes\ApprovedPincodeRequests;
use citibytes\exceptions\QueryFailedException;
use citibytes\exceptions\DatabaseConnectionException;

	$city		= $_REQUEST["city"];
	$email	= $_REQUEST["email"];

	if(empty($city) === TRUE || empty($email) === TRUE)
	{
		$error_json = array("status"=>"error","error"=>"Empty Input Parameters");
		echo json_decode($error_json);
		return;
	}


  try{

    $connection = MysqlUtils::getConnection();
		$approved_pincode_requests	= new ApprovedPincodeRequests();	
		$approved_pincodes = $approved_pincode_requests->getApprovedPincodes(
																								$connection,$email,$city);

  	//No pincodes with approval pending
  	if(empty($approved_pincodes) === TRUE)
  	{
    	$output_json = array("status"=>"success","count" => 0);
    	echo json_encode($output_json);
    	return;
		}

		$city_pincodes = new CityPincodes($city);
		$pincode_info	 = $city_pincodes->batchGetPincodeInfo($connection,
																										$approved_pincodes);

  	echo json_encode(array("status"		=>	"success",
												 	"count"		=>	count($pincode_info),
												 	"pincodes"	=>	$pincode_info));

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
