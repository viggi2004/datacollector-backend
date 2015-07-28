<?php

	require_once("config.php");

	use citibytes\BusinessCategory;
	use citibytes\persister\SimpleDbPersister;

	$business_category		= new BusinessCategory();
	$business_categories	= $business_category->getAllCategories();

  echo json_encode(array("status"							=>	"success",
												 "business_categories"=>	$business_categories));

?>
