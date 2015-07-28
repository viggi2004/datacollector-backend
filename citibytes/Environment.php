<?php

namespace citibytes;

class Environment
{

	private static function getSimpleDbConfig()
	{
		$simple_db_config_file = ROOT_DIRECTORY . "/config/prod/simpledb-config.json";		
		$simple_db_config = file_get_contents($simple_db_config_file);
		$simple_db_config = json_decode($simple_db_config,TRUE);
		return $simple_db_config;
	}

	public static function getCoreAttirbutesDomain()
	{
		$environment = Environment::getSimpleDbConfig();
		return $environment["domains"]["core_attributes_domain"];	
	}

	public static function getChainListDomain()
	{
		$environment = Environment::getSimpleDbConfig();
		return $environment["domains"]["chain_list_domain"];	
	}

	public static function getCoreAttributesBackupDomain()
	{	
		$environment = Environment::getSimpleDbConfig();
		return $environment["domains"]["core_attributes_backup_domain"];	
	}

	public static function getBusinessCategoryDomain()
	{	
		$environment = Environment::getSimpleDbConfig();
		return $environment["domains"]["business_category_domain"];	
	}



}



?>
