<?php

namespace citibytes;

use citibytes\exceptions\QueryFailedException;

date_default_timezone_set("Asia/Calcutta");

class LeaderBoard
{
	//Today's date is PHP DateTime Object
	private $_today;

	//Start of week in PHP DateTime Object
	private $_start_of_week;

	//End of week in PHP DateTime Object
	private $_end_of_week;

	//Start of month in PHP DateTime Object
	private $_start_of_month;

	//End of month in PHP DateTime Object
	private $_end_of_month;

	//Email Id of the user
	private $_email_id;

	public function __construct($email_id)
	{

		$this->_email_id	= $email_id;
		
		//ISO Week number of the current year
		$week_number = idate("W");
		$year	= idate("Y");	
		$month= idate("m");
		$last_day_of_month = idate('t');

		$today = new \DateTime();

		$start_of_month = clone $today;
		$start_of_month->setDate($year,$month,1);
		$start_of_month->setTime(0,0,1);

		$end_of_month		= clone $today;
		$end_of_month->setDate($year,$month,$last_day_of_month);
		$end_of_month->setTime(23,59,59);
		
		$start_of_week = clone $today;
		

		$start_of_week->setISODate($year,$week_number);
		$start_of_week->setTime(0,0,0);

		$end_of_week = clone $start_of_week; 

		$date_interval = new \DateInterval("P6D");
		$end_of_week->add($date_interval);
		$end_of_week->setTime(23,59,59);

		$this->_today					= $today;
		$this->_start_of_month	= $start_of_month;
		$this->_end_of_month		= $end_of_month;
		$this->_start_of_week = $start_of_week;
		$this->_end_of_week		= $end_of_week;

	}

	public function getLeaderBoardData($connection)
	{

		$email_id			 = $this->_email_id;
		$month_rank_array= $this->monthRank($connection);
		$week_rank_array = $this->weekRank($connection);

		$month_total_users = count($month_rank_array);
		$weekly_total_users= count($week_rank_array);

		$user_month_rank = $this->getUserRank($email_id,$month_rank_array);
		$user_week_rank	 = $this->getUserRank($email_id,$week_rank_array);	

		$week_rank_array = $this->filter($week_rank_array);
		$month_rank_array= $this->filter($month_rank_array);

		$output = array();

		if(is_null($user_month_rank) === FALSE)
			$output["user_month_rank"]=	$user_month_rank;

		if(is_null($user_week_rank) === FALSE)
			$output["user_week_rank"] = $user_week_rank;

		$output["month_total_users"] = $month_total_users;
		$output["weekly_total_users"]= $weekly_total_users;
		$output["month_rank"]			= $month_rank_array;
		$output["week_rank"]			= $week_rank_array;
		
		return $output;
	}

	private function monthRank($connection)
	{
		$start_of_month = $this->_start_of_month;
		$end_of_month	 	= $this->_end_of_month;
		$start_of_month = $start_of_month->format("Y-m-d");
		$end_of_month	 	= $end_of_month->format("Y-m-d");

		$query = "SELECT email_id,COUNT(*) AS business_count ".
						 "FROM analytics_employee ".
						 "WHERE date >= '$start_of_month' AND date <= '$end_of_month'". 
						 "GROUP BY email_id ".
						 "ORDER BY business_count DESC ";

		$result	= mysqli_query($connection,$query);

	  if($result === FALSE)
		{
			error_log($query);
			throw new QueryFailedException("Query failed");
		}

		$output = array();
		while($row = mysqli_fetch_assoc($result))
			array_push($output,$row);

		return $output;

	}

	private function weekRank($connection)
	{
		$start_of_week = $this->_start_of_week;
		$end_of_week	 = $this->_end_of_week;
		$start_of_week = $start_of_week->format("Y-m-d");
		$end_of_week	 = $end_of_week->format("Y-m-d");
		
		$query = "SELECT email_id,COUNT(*) AS business_count ".
						 "FROM analytics_employee ".
						 "WHERE date >= '$start_of_week' AND date <= '$end_of_week'". 
						 "GROUP BY email_id ".
						 "ORDER BY business_count DESC ";

		$result	= mysqli_query($connection,$query);

	  if($result === FALSE)
		{
			error_log($query);
			throw new QueryFailedException("Query failed");
		}

		$output = array();
		while($row = mysqli_fetch_assoc($result))
			array_push($output,$row);

		return $output;
	}

	private function getUserRank($email_id,$rank_array)
	{
		foreach($rank_array as $rank => $info)
		{
			if($info["email_id"] == $email_id)
			{
				$business_count = $info["business_count"];
				return array("rank" => $rank + 1 , "business_count" => $business_count);
			}
		}
		//User has collected no data, so no rank
		return NULL;
	}

	private function filter($rank_array)
	{
		$output = array();
		foreach($rank_array as $rank => $info)
			array_push($output,$info);	
		return $output;
	}
	
}


?>
