<?php

require_once("config.php");

use citibytes\BusinessDetails;
use citibytes\persister\S3Persister;

$business_id = $_REQUEST["business_id"];

$business_details = new BusinessDetails();
$output = $business_details->get($business_id);

/**
 * Hours of operation is stored as a JSON string in SimpleDB.
 * Coverting the JSON string into PHP Array
 */
$hours_of_operation = $output["hours_of_operation"];
$hours_of_operation = json_decode($hours_of_operation,TRUE);
$output["hours_of_operation"] = $hours_of_operation;

//Generate URLs for client to download photo data from S3
$photo_url_array = $output["photo_url"];
foreach($photo_url_array as $index => $photo_url)
{
	$url = S3Persister::getPresignedURL($photo_url,"+10 minutes");
	$photo_url_array[$index]	= $url;
}
$output["photo_url"]	= $photo_url_array;

echo json_encode($output);


?>
