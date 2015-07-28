<?php

namespace citibytes;

use citibytes\Environment;
use citibytes\utils\DateUtils;
use citibytes\persister\SimpleDbPersister;

/**
 *	Class to get the list of business collected by an user.
 *
 *	The list of business which the user has collected/edited is
 *	available in mysql analytics. The list of business id edited/
 *	collected by an user is sent to this class. It will fetch the
 *	details of the business.
 */

class UserCollectedBusiness
{

	private $_business_id_array;

	private $_domain_name;

	public function __construct($business_id_array)
	{
		$this->_business_id_array = $business_id_array;	
		$this->_domain_name = Environment::getCoreAttirbutesDomain();
	}

	/**
	 * Get the details of the list of business passed in the construtor
	 *
	 */
	public function get()
	{
		$business_id_array = $this->_business_id_array;
		$domain_name			 = $this->_domain_name;

		$result = array();
		foreach($business_id_array as $business_id)
		{
			$attributes = SimpleDbPersister::getAttributes($domain_name,
										 $business_id,array("business_name","business_id"));
			
			if(empty($attributes) === TRUE)
				continue;

			$record = array();	
			foreach($attributes as $attribute)
			{
				$attribute_name = $attribute["Name"];
				$attribute_value= $attribute["Value"];
				$record[$attribute_name] = $attribute_value;
			}

			array_push($result,$record);
		}
		
		return $result;
	}

}

?>
