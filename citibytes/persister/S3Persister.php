<?php

namespace citibytes\persister;

use Aws\S3\S3Client;

class S3Persister
{

 	private static $AWS_KEY = "";

  private static $AWS_SECRET_KEY = "";

	private static $BUCKET_NAME = "passionflick-photos";
  
	const REGION = "ap-southeast-1";

	private static $CONFIG_FILE;

	private static function init()
	{
		S3Persister::$CONFIG_FILE = ROOT_DIRECTORY . "/config/prod/s3-config.json";

		$json = file_get_contents(S3Persister::$CONFIG_FILE);
    $json = json_decode($json,TRUE);
    S3Persister::$AWS_KEY      	 = $json["aws_access_key"];
    S3Persister::$AWS_SECRET_KEY = $json["aws_secret_key"];
    S3Persister::$BUCKET_NAME		 = $json["bucket_name"];
	}


	public static function get($file_path)
	{
		S3Persister::init();
		$client = S3Client::factory(array(
											'key'    => S3Persister::$AWS_KEY,
											'secret' => S3Persister::$AWS_SECRET_KEY));
		$result = $client->getObject(array("Bucket" => S3Persister::$BUCKET_NAME,
						     											 "Key"		=> $file_path));
		return $result["Body"];
	}

	public static function put($file_path,$file_name)
	{
		S3Persister::init();
		$client = S3Client::factory(array(
											'key'    => S3Persister::$AWS_KEY,
											'secret' => S3Persister::$AWS_SECRET_KEY));

		$result = $client->putObject(array('Bucket'=> S3Persister::$BUCKET_NAME,
    																	 'Key'	=> $file_name,
																			 'SourceFile' => $file_path));
		if(isset($result["ETag"]) === TRUE)
			return TRUE;
		else
			return FALSE;
	}

	/**
	 * Create a pre-signed URL for an object in S3.
	 *
	 *	@param $key		 The path to the object inside the bucket
	 *	@param $expires The time at which the URL should expire. This can be a 
	 *	Unix timestamp, a PHP DateTime object, or a string that can be evaluated
	 *	by strtotime
	 */
	public static function getPresignedURL($key,$expires)
	{

 		S3Persister::init();
    $client = S3Client::factory(array(
                      'key'    => S3Persister::$AWS_KEY,
                      'secret' => S3Persister::$AWS_SECRET_KEY,	
											'region' => S3Persister::REGION));
		$bucket = S3Persister::$BUCKET_NAME;
		$url = "{$bucket}/{$key}";

		// get() returns a Guzzle\Http\Message\Request object
		$request = $client->get($url);

		// Create a signed URL from a completely custom HTTP request that
		// will last for until the expiration time 
		$signed_url = $client->createPresignedUrl($request,$expires);

		return $signed_url;
	}

	/**
	 * Deletes a bunch of objects in S3
	 *
	 *	@param $object_array The paths of the objects inside the bucket
	 */	
	public static function batch_delete($object_array)
	{
 		S3Persister::init();
    $client = S3Client::factory(array(
                      'key'    => S3Persister::$AWS_KEY,
                      'secret' => S3Persister::$AWS_SECRET_KEY,
                      'region' => S3Persister::REGION));
    $bucket = S3Persister::$BUCKET_NAME;

		$delete_objects = array();
		foreach($object_array as $object)
		{
    	//$object_path = "{$bucket}/{$object}";
			array_push($delete_objects,array("Key" => $object));	
		}
		
    $request = $client->deleteObjects(array('Bucket' => "$bucket",
																						'Objects'=> $delete_objects));

    return TRUE;		
	}

}

?>
