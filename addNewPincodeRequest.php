<?php

	require_once("config.php");

	use citibytes\UserProfile;
	use citibytes\CityPincodes;
	use citibytes\utils\MysqlUtils;
	use citibytes\PendingPincodeRequests;
	use citibytes\exceptions\QueryFailedException;
	use citibytes\exceptions\DatabaseConnectionException;

	$data = $_REQUEST["json"];
	$data = stripslashes($data);
	$data = json_decode($data,TRUE);

	$email_id = $data["email_id"];

	try{

		$connection = MysqlUtils::getConnection();

		//Get the user's profile
		$user_profile		 =	UserProfile::getProfile($connection,$email_id);
		$personal_number = $user_profile["personal_number"];
		$data["personal_number"] = $personal_number;

		$pending_pincode_requests = new PendingPincodeRequests();
		$pending_pincode_requests->save($connection,$data);

	}catch(DatabaseConnectionException $e){

		$failure_json = array("status"  => "error" ,
													"error"   => "Database Unavailable");
		echo json_encode($failure_json);
		return;

	}catch(QueryFailedException $e){
		error_log(mysqli_error($connection));
		$failure_json = array("status" => "error" ,
													"error" => "Unable to add pincode request");
		echo json_encode($failure_json);
		return;
	}

	$success_json = array("status" => "success");
	echo json_encode($success_json);
	mysqli_close($connection);

?>
