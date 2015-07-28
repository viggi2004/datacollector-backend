<?php

require_once("config.php");

use citibytes\UserProfile;
use citibytes\utils\MysqlUtils;
use citibytes\exceptions\QueryFailedException;
use citibytes\exceptions\DatabaseConnectionException;


$json = $_REQUEST["data"];
$data = json_decode($json,TRUE);

$email_id = $data["email_id"];
$mobile_number = $data["personal_number"];
$business_number = $data["business_number"];

try{

    $connection		= MysqlUtils::getConnection();
		$result				= UserProfile::editProfile($connection,$email_id,
																						 $mobile_number,$business_number);
		$output	= array("status" => "success");
		echo json_encode($output);

}catch(DatabaseConnectionException $e){

    $failure_json = array("status"  => "error" ,
                          "error"   => "Database Unavailable");
    echo json_encode($failure_json);
    return;

}catch(QueryFailedException $e){

		error_log(mysqli_error($connection));

    $failure_json = array("status" => "error" ,
                          "error" => "Unable to get user profile");
    echo json_encode($failure_json); 
   return;

}

 mysqli_close($connection);

?>
