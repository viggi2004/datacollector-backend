<?php

  require_once("../config.php");

	use citibytes\CityPincodes;
  use citibytes\UserProfile;
  use citibytes\utils\MysqlUtils;
  use citibytes\ApprovedPincodeRequests;
  use citibytes\PendingPincodeRequests;
  use citibytes\exceptions\QueryFailedException;
  use citibytes\exceptions\DatabaseConnectionException;
  
	$data = $_REQUEST["json"];
	$data = stripslashes($data);
	$data = json_decode($data,TRUE);

	$email_id = $data["email_id"];

	try{
    $connection = MysqlUtils::getConnection();

		//DELETE item from approved_pincode_requests
		$approved_pincode_requests = new ApprovedPincodeRequests();	
		$approved_pincode_requests->delete($connection,$data);

		$success_json = array("status" => "success");
		echo json_encode($success_json);

	}catch(DatabaseConnectionException $e){
    $failure_json = array("status"  => "error" ,
                          "error"   => "Database Unavailable");
    echo json_encode($failure_json);
    return;
  }catch(QueryFailedException $e){
 		error_log(mysqli_error($connection));
    $failure_json = array("status" => "error" ,
                          "error" => "Query Error");
    echo json_encode($failure_json);
    return;
  }

	mysqli_close($connection);
	
?>
