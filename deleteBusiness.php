<?php

require_once("config.php");

use citibytes\persister\SimpleDbPersister;

$business_id = $_REQUEST["business_id"];

SimpleDbPersister::delete("core_attributes",$business_id);
$success_json = array("status" => "success");

echo json_encode($success_json);

?>
