<?php

	require_once("config.php");

	use citibytes\CityPincodes;

	$city_name = $_REQUEST["city"];

	$city_pincodes = new CityPincodes($city_name);
	$city_pincodes->import();

?>
