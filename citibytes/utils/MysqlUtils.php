<?php

namespace citibytes\utils;

use citibytes\exceptions;

class MysqlUtils
{

	private static $CONFIG_FILE;

	public static function getConnection()
	{
		MysqlUtils::$CONFIG_FILE = ROOT_DIRECTORY . "/config/prod/mysql-config.json";
		$config_file = file_get_contents(MysqlUtils::$CONFIG_FILE);
		$config			 = json_decode($config_file,TRUE);

		$host			= $config["host"];
		$username = $config["username"];
		$password	= $config["password"];
		$database	= $config["database"];

		$connection =mysqli_connect($host,$username,$password,$database);
		$error = mysqli_connect_error($connection);

		if($error !== NULL)
			throw new exceptions\DatabaseConnectionException("Db connection failed");
 
		return $connection;
	}

}



?>
