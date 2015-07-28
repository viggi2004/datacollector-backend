<?php

	require_once("config.php");

	use citibytes\ChainList;

	$chain_list		= new ChainList();
	$chain_array	= $chain_list->get();

  echo json_encode(array("status"		=>	"success",
												 "chain_list"		=>	$chain_array));

?>
