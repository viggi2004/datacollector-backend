<?php

namespace citibytes;

use citibytes\Environment;
use citibytes\utils\SimpleDbUtils;
use citibytes\persister\SimpleDbPersister;

class ChainList
{

	//The domain name under simple db
	private $_domain_name;

	public function __construct()
	{
		//Get the name of the domain depending on the dev/prod environment
		$this->_domain_name = Environment::getChainListDomain();
	}

	public function save($chain_name)
	{
		$domain_name= $this->_domain_name;
		$item_name	= $chain_name;
		$attributes = SimpleDbUtils::genSimpleDbAttributesArray(
                                        array("chain_name" => $chain_name));
  	SimpleDbPersister::save($domain_name,$item_name,$attributes);
	}

	/** 
	 *	Get All the chain names
	 *
	 *	@return An array containing all the chain names
	 */
	public function get()
	{
		$output				= array();
    $domain_name	= $this->_domain_name;
		$query				= "SELECT * FROM $domain_name ";
		$next_token = "";
		do{
				$response = SimpleDbPersister::select($query,$next_token);
				$next_token = $response['NextToken'];
				$fetched_records = $response['Items'];
				foreach($fetched_records as $index => $record)
					array_push($output,$record["Attributes"][0]["Value"]);
			}while(empty($next_token) === FALSE);
		return $output;
	}
	
}

?>
