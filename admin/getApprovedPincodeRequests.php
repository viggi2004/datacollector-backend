<?php

	require_once("../config.php");

	use citibytes\utils\MysqlUtils;
	use citibytes\exceptions\DatabaseConnectionException;
	use citibytes\exceptions\QueryFailedException;
	use citibytes\ApprovedPincodeRequests;
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
    $approved_pincode_requests = new ApprovedPincodeRequests();
		$approved_pincodes 	 = $approved_pincode_requests->getAllApprovedPincodes(
																														$connection,$city);
		//No pincodes with approval pending
		if(empty($approved_pincodes) === TRUE)
		{
			$output_json = array("status"=>"success","count" => 0);
			echo json_encode($output_json);
			return;
		}

		echo json_encode(array("status"		=> "success", 
												 	 "count"		=> count($approved_pincodes),
												 	 "content"	=> $approved_pincodes));


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
