<?php

	require_once("config.php");

	use citibytes\BusinessCategory;

	$business_category = new BusinessCategory();
	$business_category->import();

?>
