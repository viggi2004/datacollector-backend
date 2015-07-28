<?php

require_once("config.php");

use citibytes\Watermark;
use citibytes\persister\S3Persister;
use citibytes\exceptions\WatermarkException;

$uploaded_file_path	= $_FILES['file']['tmp_name'];
$file_name					= $_POST['file_path'];

try{

	$watermark = new Watermark();
	$watermark->addWatermark($uploaded_file_path);

}catch(WatermarkException $e){
	error_log($e->getMessage());
	$result_json = array("status" => "error",
											 "error"	=> "Error occured while applying watermark");
	echo json_encode($result_json);
	exit;
}

$result = S3Persister::put($uploaded_file_path,$file_name);
if($result === TRUE)
	$result_json = array("status" => "ok","file_path" => $file_name);
else
	$result_json = array("status" => "error",
	
										 "error" => "Please Try Again Later");

echo json_encode($result_json); 

?>
