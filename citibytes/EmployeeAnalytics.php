<?php

namespace citibytes;

use citibytes\exceptions\QueryFailedException;
use citibytes\utils\DateUtils;

class EmployeeAnalytics
{

	public static function recordTimeSpentOnABusiness($connection,
													$email_id,$business_id,$city,$date,$duration)
	{

		$query = "INSERT INTO analytics_employee ".
						 "VALUES ('$email_id','$business_id','$city','$date',$duration) ".
						 "ON DUPLICATE KEY UPDATE duration = duration + $duration ";

		$result = mysqli_query($connection,$query);

		if($result === FALSE)
		{
			error_log($query);
      throw new QueryFailedException("Query failed");
		}

    return TRUE;

	}

	public static function getTodayAnalytics($connection,$city)
  {

    //$today storing the datetime of start of today
    $today = new \DateTime();
    $today = $today->format("Y-m-d");

    $query = "SELECT email_id, ".
						 "SUM(duration) AS duration, ".
						 "COUNT(*) AS business_collected ".
             "FROM   analytics_employee ".
             "WHERE  city='$city' AND date = '$today' ".
             "GROUP BY email_id ".
             "ORDER BY business_collected DESC";
    $result = mysqli_query($connection,$query);

    if($result === FALSE)
    {
      error_log($query);
      throw new QueryFailedException("Query failed");
    }

    $employee_analytics = array();

    $row_count = mysqli_num_rows($result);
    if($row_count === 0)
      return $employee_analytics;

    while($row = mysqli_fetch_assoc($result))
      array_push($employee_analytics,$row);

    return $employee_analytics;

  }


	public static function getWeeklyAnalytics($connection,$city)
	{
		//$today storing the datetime of start of today
		$monday	= DateUtils::getFirstDayOfWeek();
		$monday	=	$monday->format("Y-m-d");

		$sunday	= DateUtils::getLastDayOfWeek();
		$sunday = $sunday->format("Y-m-d");


    $query = "SELECT email_id, ".
						 "SUM(duration) AS duration, ".
						 "COUNT(*) AS business_collected ".
             "FROM   analytics_employee ".
             "WHERE  city='$city' AND ".
						 "date >= '$monday' AND date <= '$sunday' ".
             "GROUP BY email_id ".
             "ORDER BY business_collected DESC";
    $result = mysqli_query($connection,$query);

    if($result === FALSE)
    {
      error_log($query);
      throw new QueryFailedException("Query failed");
    }

    $employee_analytics = array();

    $row_count = mysqli_num_rows($result);
    if($row_count === 0)
      return $employee_analytics;

    while($row = mysqli_fetch_assoc($result))
      array_push($employee_analytics,$row);

    return $employee_analytics;
	}

	public static function getMonthlyAnalytics($connection,$city)
	{
		//$today storing the datetime of start of today
		$first_day	= DateUtils::getFirstDayOfMonth();
		$first_day	=	$first_day->format("Y-m-d");

		$last_day	= DateUtils::getLastDayOfMonth();
		$last_day = $last_day->format("Y-m-d");

    $query = "SELECT email_id, ".
						 "SUM(duration) AS duration, ".
						 "COUNT(*) AS business_collected ".
             "FROM   analytics_employee ".
             "WHERE  city='$city' AND ".
						 "date >= '$first_day' AND date <= '$last_day' ".
             "GROUP BY email_id ".
             "ORDER BY business_collected DESC";
    $result = mysqli_query($connection,$query);

    if($result === FALSE)
    {
      error_log($query);
      throw new QueryFailedException("Query failed");
    }

    $employee_analytics = array();

    $row_count = mysqli_num_rows($result);
    if($row_count === 0)
      return $employee_analytics;

    while($row = mysqli_fetch_assoc($result))
      array_push($employee_analytics,$row);

    return $employee_analytics;
	}

	public static function getBusinessIdCollectedToday($connection,$city,
																										 $email_id)
	{
		//$today storing the datetime of start of today
    $today = new \DateTime();
    $today = $today->format("Y-m-d");

    $query = "SELECT business_id ".
             "FROM   analytics_employee ".
             "WHERE  city	='$city' ".
						 "AND		 date ='$today' ".
						 "AND email_id='$email_id' ";
    $result = mysqli_query($connection,$query);

    if($result === FALSE)
    {
      error_log($query);
      throw new QueryFailedException("Query failed");
    }

    $collected_business = array();

    while($row = mysqli_fetch_assoc($result))
      array_push($collected_business,$row["business_id"]);

    return $collected_business;
			
	}

	public static function getBusinessIdCollectedThisWeek($connection,$city,
																												$email_id)
	{
		$monday	= DateUtils::getFirstDayOfWeek();
		$monday	=	$monday->format("Y-m-d");

		$sunday	= DateUtils::getLastDayOfWeek();
		$sunday = $sunday->format("Y-m-d");


    $query = "SELECT business_id ".
             "FROM   analytics_employee ".
             "WHERE  city='$city' ".
						 "AND		 date >= '$monday' ".
						 "AND		 date <= '$sunday' ".
						 "AND		 email_id='$email_id' ";
    $result = mysqli_query($connection,$query);

    if($result === FALSE)
    {
      error_log($query);
      throw new QueryFailedException("Query failed");
    }

 		$collected_business = array();

    while($row = mysqli_fetch_assoc($result))
      array_push($collected_business,$row["business_id"]);

    return $collected_business;

	}

	public static function getBusinessIdCollectedThisMonth($connection,$city,
																												 $email_id)
	{
		$first_day	= DateUtils::getFirstDayOfMonth();
		$first_day	=	$first_day->format("Y-m-d");

		$last_day	= DateUtils::getLastDayOfMonth();
		$last_day = $last_day->format("Y-m-d");

    $query = "SELECT business_id ".
             "FROM   analytics_employee ".
             "WHERE  city='$city' ".
						 "AND		 date >= '$first_day' ".
						 "AND		 date <= '$last_day' ".
             "AND		 email_id = '$email_id' ";
    $result = mysqli_query($connection,$query);

    if($result === FALSE)
    {
      error_log($query);
      throw new QueryFailedException("Query failed");
    }

    $collected_business = array();

    while($row = mysqli_fetch_assoc($result))
      array_push($collected_business,$row["business_id"]);
    
    return $collected_business;

	}


}


?>
