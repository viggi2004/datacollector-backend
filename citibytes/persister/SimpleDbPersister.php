<?php

namespace citibytes\persister;

use Aws\SimpleDb\SimpleDbClient;
use Aws\Common\Enum\Region;

class SimpleDbPersister
{

	private static $AWS_KEY = "";

	private static $AWS_SECRET_KEY = "";

	const REGION = "us-east-1";
	
	private static $CONFIG_FILE;

	private static function init()
	{
		SimpleDbPersister::$CONFIG_FILE = 
																ROOT_DIRECTORY . "/config/prod/simpledb-config.json";
		$json = file_get_contents(SimpleDbPersister::$CONFIG_FILE);
		$json	=	json_decode($json,TRUE);
		SimpleDbPersister::$AWS_KEY				= $json["aws_access_key"];
		SimpleDbPersister::$AWS_SECRET_KEY = $json["aws_secret_key"];
	}

	public static function save($domain_name,$item_name,$attributes)
	{
		SimpleDbPersister::init();
		$client = SimpleDbClient::factory(array('key'	=>SimpleDbPersister::$AWS_KEY,
    															'secret' =>SimpleDbPersister::$AWS_SECRET_KEY,
    															'region' => SimpleDbPersister::REGION));
		
		$result = $client->putAttributes(array('DomainName' => $domain_name,
    																			 'ItemName'   => $item_name,
    																			 'Attributes' => $attributes));
		if($result)
			return TRUE;
		else
			return FALSE;
	}

	public static function batch_save($domain_name,$items)
	{
		SimpleDbPersister::init();		
		$client = SimpleDbClient::factory(array('key'	=>SimpleDbPersister::$AWS_KEY,
    															'secret' =>SimpleDbPersister::$AWS_SECRET_KEY,
    															'region' => SimpleDbPersister::REGION));
			
		$result = $client->batchPutAttributes(array('DomainName' => $domain_name,
    																			 			'Items'			 => $items));
		if($result)
			return TRUE;
		else
			return FALSE;

	}

	public static function getAttributes($domain_name,$item_name,
																			 $attributes = array())
	{
		SimpleDbPersister::init();
		$client = SimpleDbClient::factory(array('key'	=>SimpleDbPersister::$AWS_KEY,
    															'secret' =>SimpleDbPersister::$AWS_SECRET_KEY,
    															'region' => SimpleDbPersister::REGION));

		$result = $client->getAttributes(array('DomainName' => $domain_name,
    																			 'ItemName'   => $item_name,
    																			 'AttributeNames' => $attributes,
																					 'ConsistentRead' => true));

		return $result['Attributes'];
	}

	public static function select($query,$next_token="",$consistent_read = false)
	{
		SimpleDbPersister::init();
		$client = SimpleDbClient::factory(array('key'	=>SimpleDbPersister::$AWS_KEY,
    															'secret' =>SimpleDbPersister::$AWS_SECRET_KEY,
    															'region' => SimpleDbPersister::REGION));

		$result = $client->select(array('SelectExpression'	=> $query,
    																			 'NextToken'	=> $next_token,
																			'ConsistentRead'	=> $consistent_read));

		return $result;
	}

	public static function delete($domain_name,$item_name)
	{
		SimpleDbPersister::init();
		$client = SimpleDbClient::factory(array('key'	=>SimpleDbPersister::$AWS_KEY,
    															'secret' =>SimpleDbPersister::$AWS_SECRET_KEY,
    															'region' => SimpleDbPersister::REGION));

		$result = $client->deleteAttributes(array('DomainName'=> $domain_name,
    																					'ItemName'	=> $item_name));
		return $result;
	}

}

?>
