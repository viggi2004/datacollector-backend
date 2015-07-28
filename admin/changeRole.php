<?php

require_once("../config.php");

use citibytes\UserProfile;
use citibytes\utils\MysqlUtils;
use citibytes\exceptions\QueryFailedException;
use citibytes\exceptions\DatabaseConnectionException;
 

$is_admin = $_REQUEST['is_admin'];
$email_id	= $_REQUEST['email_id'];

try{

    $connection = MysqlUtils::getConnection();
		UserProfile::setRole($connection,$email_id,$is_admin);

    echo json_encode(array("status" => "success"));

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
