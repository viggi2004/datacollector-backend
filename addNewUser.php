<?php

require_once("config.php");

use citibytes\User;
use citibytes\utils\MysqlUtils;
use citibytes\exceptions\DatabaseConnectionException;
use citibytes\exceptions\QueryFailedException;
use citibytes\PendingPincodeRequests;
use citibytes\CityPincodes;


$data = $_REQUEST["json"];
$data = stripslashes($data);
$data = json_decode($data,TRUE);


try{

	$connection	= MysqlUtils::getConnection();
	User::addNewUser($connection,$data);

}catch(DatabaseConnectionException $e){

	$failure_json = array("status"  => "error" ,
												"error"   => "Database Unavailable");
	echo json_encode($failure_json);
	return;

}catch(QueryFailedException $e){

 	error_log(mysqli_error($connection));
	$failure_json = array("status" => "error",
												"error"	=>	"Query Failed");
	echo json_encode($failure_json);
	return;

}

$success_json = array("status" => "success");
echo json_encode($success_json);

mysqli_close($connection);

?>
