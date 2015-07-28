<?php

 	require_once("../config.php");

  use citibytes\utils\MysqlUtils;
  use citibytes\exceptions\DatabaseConnectionException;
  use citibytes\exceptions\QueryFailedException;
  use citibytes\ApprovedPincodeRequests;
  use citibytes\PendingPincodeRequests;
  use citibytes\CityPincodes;
  use citibytes\UserProfile;

	$data = $_REQUEST["json"];
	$data = stripslashes($data);
	$data = json_decode($data,TRUE);

	$is_approved = $_REQUEST["is_approved"] == 1 ? TRUE : FALSE;
  
	$email_id = $data["email_id"];

	try{
    $connection = MysqlUtils::getConnection();

		//Get the user's profile
    $user_profile    =  UserProfile::getProfile($connection,$email_id);
    $personal_number = $user_profile["personal_number"];
    $data["personal_number"] = $personal_number;

		//DELETE item from pending_pincode_requests
		$pending_pincode_requests = new PendingPincodeRequests();	
		$pending_pincode_requests->delete($connection,$data);

		//If approved,save it in approved_pincode_requests domain
		if($is_approved === TRUE)
		{
  		$approved_pincode_requests = new ApprovedPincodeRequests();
  		$approved_pincode_requests->save($connection,$data);
		}

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

	
?>
