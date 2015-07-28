<?php

require_once("config.php");

use citibytes\User;
use citibytes\utils\MysqlUtils;
use citibytes\exceptions\QueryFailedException;
use citibytes\exceptions\DatabaseConnectionException;

$email_id = $_REQUEST["email"];
if(empty($email_id) === TRUE)
{
	$error_json = array("status"=> "error" ,
											"error"	=> "Email cannot be empty");	
	echo json_encode($error_json);
	return;
}

try{

	$connection	= MysqlUtils::getConnection();
	$user_info	= User::getUserInfo($connection,$email_id);	
	
	if($user_info === NULL)
		$success_json = array("status" => "success","is_new_user" => true);
	else
		$success_json = array("status" => "success" ,"is_new_user" => false);

	echo json_encode($success_json);

}catch(DatabaseConnectionException $e){

	$failure_json = array("status"  => "error" ,
												"error"   => "Database Unavailable");
	echo json_encode($failure_json);
	return;

}catch(QueryFailedException $e){

	$failure_json = array("status" => "error",
												"error"	=>	"Query Failed");
	echo json_encode($failure_json);
	return;

}

	mysqli_close($connection);

?>
