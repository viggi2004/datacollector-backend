<?php

	require_once("../config.php");

	use citibytes\EmployeeAnalytics;
	use citibytes\utils\MysqlUtils;
	use citibytes\exceptions\DatabaseConnectionException;
	use citibytes\exceptions\QueryFailedException;

	$period		= $_REQUEST["period"];
	$city			= $_REQUEST["city"];

	if(empty($city) === TRUE || empty($period) === TRUE)
	{
		$error_json = array("status"=>"error","error"=>"Empty Input Parameters");
		echo json_decode($error_json);
		return;
	}

	try{

    $connection = MysqlUtils::getConnection();

		if($period === "today")
			$analytics = EmployeeAnalytics::getTodayAnalytics($connection,$city);
		elseif($period === "weekly")
			$analytics = EmployeeAnalytics::getWeeklyAnalytics($connection,$city);
		elseif($period === "monthly")
			$analytics = EmployeeAnalytics::getMonthlyAnalytics($connection,$city);
		else
			$analytics = EmployeeAnalytics::getTodayAnalytics($connection,$city);

		//No business collected by any employee
		if(empty($analytics) === TRUE)
		{
			$output_json = array("status"=>"success",
													 "count" => 0,
													 "date"  => getFormattedDate($period),
				"total_business_collected" => 0);
			echo json_encode($output_json);
			return;
		}

		$total_business_collected = calculateTotalBusinessCollected($analytics);

		//Add formatted duration to each analytic record
		foreach($analytics as $index => $analytic_data)
		{
			$duration	= $analytic_data["duration"];
			$analytic_data["formatted_duration"]	= formatDuration($duration);
			$analytics[$index] = $analytic_data;
		}

		echo json_encode(array("status"		=> "success", 
												 	 "count"		=> count($analytics),
													 "date"			=> getFormattedDate($period),
												 	 "analytics"=> $analytics,
					 "total_business_collected" => $total_business_collected));


  }catch(DatabaseConnectionException $e){
    $failure_json = array("status"  => "error" ,
                          "error"   => "Database Unavailable");
    echo json_encode($failure_json);
    return;
  }catch(QueryFailedException $e){
		error_log(mysqli_error($connection));
    $failure_json = array("status" => "error" ,
                          "error" => "Query Failed");
    echo json_encode($failure_json);
    return;
  }

	mysqli_close($connection);

	function calculateTotalBusinessCollected($analytics)
	{
		$total = 0;
		foreach($analytics as $index => $data)
			$total += $data["business_collected"];
		return $total;
	}

	/*	Formats time duration spent in business
	 *
	 *	If 1 <= $duartion <= 60 , return duration in seconds
	 *	If 1min < $duration <= 60 minutes, return time in minutes
	 *	If 1hour< $duration <= 24 hours, return time in hours
	 *	If 1day < $duration , return time in days
	 *
	 *	$param $duration Time spent in business in seconds
	 *
	 */
	function formatDuration($duration)
	{

		if($duration <= 60)
			return "$duration seconds";

		$minutes = floor($duration / 60);
		if(1 == $minutes)
			return "1 minute";
		elseif($minutes <= 60)
			return "$minutes minutes";
		
		$hours	 = floor($duration / 3600);
		if(1 == $hours)
			return "1 hour";
		elseif($hours <= 24)
			return "$hours hours";

		$days		 = floor($duration / (3600 * 24));
		if(1 == $days)
			return "1 day";
		
		return "$days days";

	}

	/**
	 *	Format date to be displayed in android client
	 *
	 *	Depending on the value period takes the date has to be formatted
	 *	according to the rules given below.
	 *
	 *	If $period is "today" - Date format is 12th June 2014
	 *	If $period is "weekly"- Date format is 17th March - 23rd March,2014
	 *	If $period is "monthly" Date format is March 2014
	 *
	 *	@param $period
	 *	
	 *	@returns A formatted date
	 */

	function getFormattedDate($period)
  {
    if($period === "today")
    {
      $d = new DateTime();
      return $d->format("jS M Y");
    }
    elseif($period === "weekly")
    {
      $d = new DateTime();
      $week_number = $d->format("W");
      $year_number = $d->format("Y");
      $monday    = new DateTime("{$year_number}-W{$week_number}-1 00:00:01");
      $sunday    = new DateTime("{$year_number}-W{$week_number}-7 23:59:59");
      $monday    = $monday->format("jS M");
      $sunday    = $sunday->format("jS M,Y");
      return "$monday - $sunday";
    }
    elseif($period === "monthly")
    {
      $d = new DateTime();
      return $d->format("F Y");
    }
    else
      return "";
  }


?>
