<?php

require_once("config.php");

use citibytes\ChainList;
use citibytes\Environment;
use citibytes\utils\SimpleDbUtils;
use citibytes\persister\SimpleDbPersister;

$chain_name = $_REQUEST["chain_name"];
$domain_name= Environment::getChainListDomain();

if(empty($chain_name) === TRUE)
{
	$error_json = array("status" => "error" , 
											"error" => "chain_name parameter cannot be empty");
	echo json_encode($error_json);
	return;
}

$chain_list	= new ChainList();
$item_name	= $chain_name;
$result			= SimpleDbPersister::getAttributes($domain_name,$item_name);
if(empty($result) === TRUE)
{
	$chain_list->save($chain_name);
	$success_json = array("status" => "success");
	echo json_encode($success_json);
}
else
{
	$error_json = array("status" => "error" , 
											"error" => "Chain name already exists");
	echo json_encode($error_json);
}


?>
