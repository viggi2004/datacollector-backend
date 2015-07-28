<?php

require_once("config.php");

use citibytes\UserProfile;
use citibytes\utils\MysqlUtils;
use citibytes\PincodeAnalytics;
use citibytes\EmployeeAnalytics;
use citibytes\exceptions\QueryFailedException;
use citibytes\exceptions\DatabaseConnectionException;

date_default_timezone_set("Asia/Calcutta");

$json = $_REQUEST["data"];
$json_data = json_decode($json,TRUE);

//TODO: Remove the if condition after three to 4 days untill all android clients
//get updated
if(isset($json_data["is_admin"]) === FALSE)
	$is_admin = FALSE;
else
	$is_admin = $json_data["is_admin"];
/**
 * If the user is an admin, no need to save analytics
 */
if($is_admin == TRUE)
{
	$success_json = array("status" => "success");
	echo json_encode($success_json);
	exit;
}

error_log('check '.$json);
$business_id = $json_data["business_id"];
$city				 = $json_data["city"];
$pincode		 = $json_data["pincode"];
$email_id		 = $json_data["email_id"];
$duration		 = $json_data["duration"];
$iso_date		 = new \DateTime($json_data["date"]);
$date				 = $iso_date->format("Y-m-d");
$date_time	 = $iso_date->format("Y-m-d H:i:s"); 

try{

    $connection = MysqlUtils::getConnection();
		$result 		= EmployeeAnalytics::recordTimeSpentOnABusiness($connection,
															 $email_id,$business_id,$city,$date,$duration);
		$result			=	PincodeAnalytics::addNewBusiness($connection,$business_id,
																									$pincode,$city,$date_time);
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
                          "error" => "Query execution failed");
    echo json_encode($failure_json);
    return;

  }
  
	mysqli_close($connection);

?>
