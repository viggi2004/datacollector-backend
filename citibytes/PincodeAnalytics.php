<?php

namespace citibytes;

use citibytes\utils\DateUtils;
use citibytes\exceptions\QueryFailedException;

date_default_timezone_set("Asia/Calcutta");

class PincodeAnalytics
{

 public static function addNewBusiness($connection,
                          $business_id,$pincode,$city,$date)
  {

    $is_business_added = PincodeAnalytics::isBusinessAdded($connection,
                                                          $business_id);
    if($is_business_added === TRUE)
      return TRUE;

    $query = "INSERT INTO analytics_pincode ".
             "VALUES ('$business_id','$pincode','$city','$date') ";

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
		$today->setTime(0,0,1);
		$today = $today->format("Y-m-d H:i:s");

    $query = "SELECT pincode,COUNT(*) as business_collected ".
						 "FROM	 analytics_pincode ".
             "WHERE	 city='$city' AND create_ts >= '$today' ".
             "GROUP BY pincode ".
						 "ORDER BY business_collected DESC";
    $result = mysqli_query($connection,$query);

    if($result === FALSE)
		{
			error_log($query);
      throw new QueryFailedException("Query failed");
		}

		$pincode_analytics = array();
    
		$row_count = mysqli_num_rows($result);
    if($row_count === 0)
      return $pincode_analytics;

    while($row = mysqli_fetch_assoc($result))
      array_push($pincode_analytics,$row);

    return $pincode_analytics;
	
	}

	public static function getWeeklyAnalytics($connection,$city)
	{
		
		//$today storing the datetime of start of today
		$monday = DateUtils::getFirstDayOfWeek();
		$monday	= $monday->format("Y-m-d H:i:s");

    $sunday = DateUtils::getLastDayOfWeek();
		$sunday	= $sunday->format("Y-m-d H:i:s");


    $query = "SELECT pincode,COUNT(*) as business_collected ".
						 "FROM	 analytics_pincode ".
             "WHERE	 city='$city' AND ".
						 "create_ts >= '$monday' AND create_ts <= '$sunday' ".
             "GROUP BY pincode ".
						 "ORDER BY business_collected DESC";
    $result = mysqli_query($connection,$query);

    if($result === FALSE)
		{
			error_log($query);
      throw new QueryFailedException("Query failed");
		}

		$pincode_analytics = array();
    
		$row_count = mysqli_num_rows($result);
    if($row_count === 0)
      return $pincode_analytics;

    while($row = mysqli_fetch_assoc($result))
      array_push($pincode_analytics,$row);

    return $pincode_analytics;
	
	}

	public static function getMonthlyAnalytics($connection,$city)
	{
		
		$first_day  = DateUtils::getFirstDayOfMonth();
		$first_day	= $first_day->format("Y-m-d H:i:s");

    $last_day = DateUtils::getLastDayOfMonth();
		$last_day	= $last_day->format("Y-m-d H:i:s");

    $query = "SELECT pincode,COUNT(*) as business_collected ".
						 "FROM	 analytics_pincode ".
             "WHERE	 city='$city' AND ".
						 "create_ts >= '$first_day' AND create_ts <= '$last_day' ".
             "GROUP BY pincode ".
						 "ORDER BY business_collected DESC";
    $result = mysqli_query($connection,$query);

    if($result === FALSE)
		{
			error_log($query);
      throw new QueryFailedException("Query failed");
		}

		$pincode_analytics = array();
    
		$row_count = mysqli_num_rows($result);
    if($row_count === 0)
      return $pincode_analytics;

    while($row = mysqli_fetch_assoc($result))
      array_push($pincode_analytics,$row);

    return $pincode_analytics;
	
	}


	public static function getTillDateAnalytics($connection,$city)
	{
		
    $query = "SELECT pincode,COUNT(*) as business_collected ".
						 "FROM	 analytics_pincode ".
             "WHERE	 city='$city' ".
             "GROUP BY pincode ".
						 "ORDER BY business_collected DESC";
    $result = mysqli_query($connection,$query);

    if($result === FALSE)
		{
			error_log($query);
      throw new QueryFailedException("Query failed");
		}

		$pincode_analytics = array();
    
		$row_count = mysqli_num_rows($result);
    if($row_count === 0)
      return $pincode_analytics;

    while($row = mysqli_fetch_assoc($result))
      array_push($pincode_analytics,$row);

    return $pincode_analytics;
	
	}


  private static function isBusinessAdded($connection,$business_id)
  {
    $query="SELECT * FROM analytics_pincode WHERE business_id ='$business_id'";
    $result = mysqli_query($connection,$query);

    if($result === FALSE)
    {
      error_log($query);
      throw new QueryFailedException("Query failed");
    }

    $count = mysqli_num_rows($result);

    if($count === 0)
      return FALSE;

    return TRUE;

  }


}

?>
