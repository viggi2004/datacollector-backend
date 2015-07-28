<?php

namespace citibytes;

use citibytes\Environment;
use citibytes\utils\DateUtils;
use citibytes\utils\BusinessUtils;
use citibytes\persister\SimpleDbPersister;

/**
 * Class to get the list of business collected under a pincode
 * in a given time frame
 */

class BusinessInAPincode
{

	private $_pincode;

	private $_domain_name;

	public function __construct($pincode)
	{
		$this->_pincode = $pincode;
		$this->_domain_name = Environment::getCoreAttirbutesDomain(); 
	}

	//Get all business collected for a pincode today
	public function getBusinessCollectedToday()
	{
		$domain_name = $this->_domain_name;

		$start_of_day = DateUtils::getStartOfToday();
		$start_of_day = $start_of_day->format('c');

		$end_of_day		= DateUtils::getEndOfToday();
		$end_of_day		= $end_of_day->format('c');

		$pincode = $this->_pincode;

		$query   = "SELECT business_name,address_line_1,address_line_2, ".
							 "latitude,longitude,status ".
							 "FROM $domain_name ".
							 "WHERE pincode='$pincode' ".
							 "AND created_time >= '$start_of_day' ".
							 "AND created_time <= '$end_of_day' ";
		$result	 = $this->query($query);

		return $result;
	}

	//Get all business collected for a pincode this week
	public function getBusinessCollectedThisWeek()
	{
		$domain_name = $this->_domain_name;

		$first_day_of_week = DateUtils::getFirstDayOfWeek();
		$first_day_of_week = $first_day_of_week->format('c');

		$last_day_of_week = DateUtils::getLastDayOfWeek();
		$last_day_of_week = $last_day_of_week->format('c');

		$pincode = $this->_pincode;

		$query   = "SELECT business_name,address_line_1,address_line_2, ".
							 "latitude,longitude,status ".
							 "FROM $domain_name ".
							 "WHERE pincode='$pincode' ".
							 "AND created_time >= '$first_day_of_week' ".
							 "AND created_time <= '$last_day_of_week' ";
		$result	 = $this->query($query);

		return $result;
	}

	//Get All business collected for a pincode this month
	public function getBusinessCollectedThisMonth()
	{
		$domain_name = $this->_domain_name;

		$first_day_of_month = DateUtils::getFirstDayOfMonth();
		$first_day_of_month = $first_day_of_month->format('c');

		$last_day_of_month = DateUtils::getLastDayOfMonth();
		$last_day_of_month = $last_day_of_month->format('c');

		$pincode = $this->_pincode;

		$query   = "SELECT business_name,address_line_1,address_line_2, ".
							 "latitude,longitude,status ".
							 "FROM $domain_name ".
							 "WHERE pincode='$pincode' ".
							 "AND created_time >= '$first_day_of_month' ".
							 "AND created_time <= '$last_day_of_month' ";
		$result	 = $this->query($query);

		return $result;

	}

	//Get All business collected for a pincode till date
	public function getBusinessCollectedTillDate()
	{
		$domain_name = $this->_domain_name;

		$pincode = $this->_pincode;
		$query   = "SELECT business_name,address_line_1,address_line_2, ".
							 "latitude,longitude,status ".
							 "FROM $domain_name WHERE pincode='$pincode'";
		$result	 = $this->query($query);
		return $result;
	}

	/**
	 *	Query the simple DB
	 *
	 *	@param $query The simple db query to execute
	 *
	 *	@return $output Array of associative array. The keys in the
	 *					associative array are the columns that are extracted
	 *					from the query
	 */
	private function query($query)
	{
		$output = array();
		$next_token = "";
		do{
			$result = SimpleDbPersister::select($query,$next_token);
			$items  = $result["Items"];
			$next_token = $result["NextToken"];
			foreach($items as $item)
			{
				$business_id    = $item["Name"];
				$attributes     = $item["Attributes"];
				$business_item  = BusinessUtils::toArray($attributes);

				if(is_null($business_item) === TRUE)
					continue;

				$output[$business_id] = $business_item;
			}
		}while(isset($next_token) === TRUE);
		return $output;	
	}

}

?>
