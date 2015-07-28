<?php

require_once("config.php");

use citibytes\UserProfile;
use citibytes\utils\MysqlUtils;
use citibytes\exceptions\QueryFailedException;
use citibytes\exceptions\DatabaseConnectionException;

$email_id = $_REQUEST["email_id"];

try{

    $connection		= MysqlUtils::getConnection();
		$user_profile = UserProfile::getProfile($connection,$email_id);
		$user_profile["status"]	= "success";
		
		echo json_encode($user_profile);

}catch(DatabaseConnectionException $e){

    $failure_json = array("status"  => "error" ,
                          "error"   => "Database Unavailable");
    echo json_encode($failure_json);
    return;

}catch(QueryFailedException $e){

    $failure_json = array("status" => "error" ,
                          "error" => "Unable to get user profile");
    echo json_encode($failure_json);
    return;

}


?>
