<?php

 	require_once("../config.php");

  use citibytes\utils\MysqlUtils;
  use citibytes\exceptions\DatabaseConnectionException;
  use citibytes\exceptions\QueryFailedException;
  use citibytes\PendingPincodeRequests;
	use citibytes\CityPincodes;

	$city		= $_REQUEST["city"];

	if(empty($city) === TRUE)
	{
		$error_json = array("status"=>"error","error"=>"Empty Input Parameters");
		echo json_decode($error_json);
		return;
	}

	try{

    $connection = MysqlUtils::getConnection();
    $pending_pincode_requests = new PendingPincodeRequests();
		$unapproved_pincodes = $pending_pincode_requests->getAllUnApprovedPincodes(
																														$connection,$city);
		//No pincodes with approval pending
		if(empty($unapproved_pincodes) === TRUE)
		{
			$output_json = array("status"=>"success","count" => 0);
			echo json_encode($output_json);
			return;
		}

		echo json_encode(array("status"		=> "success", 
												 	 "count"		=> count($unapproved_pincodes),
												 	 "content"	=> $unapproved_pincodes));


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
