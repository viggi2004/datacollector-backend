<?php

require_once("config.php");

use citibytes\utils\MysqlUtils;
use citibytes\LeaderBoard;
use citibytes\exceptions\DatabaseConnectionException;
use citibytes\exceptions\QueryFailedException;

$email_id = $_REQUEST["email_id"];

try{

    $connection = MysqlUtils::getConnection();
		$leader_board= new LeaderBoard($email_id);
    $result			 = $leader_board->getLeaderBoardData($connection);
		$result["status"] = "success";
    echo json_encode($result);

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
