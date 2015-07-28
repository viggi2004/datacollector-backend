<?php

	require_once("../config.php");

	use citibytes\PincodeAnalytics;
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
			$analytics = PincodeAnalytics::getTodayAnalytics($connection,$city);
		elseif($period === "weekly")
			$analytics = PincodeAnalytics::getWeeklyAnalytics($connection,$city);
		elseif($period === "monthly")
			$analytics = PincodeAnalytics::getMonthlyAnalytics($connection,$city);
		elseif($period === "tilldate")
			$analytics = PincodeAnalytics::getTillDateAnalytics($connection,$city);

		//No pincodes collected
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
		echo json_encode(array("status"		=> "success", 
													 "date"			=> getFormattedDate($period),
												 	 "count"		=> count($analytics),
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

 /**
   *  Format date to be displayed in android client
   *
   *  Depending on the value period takes the date has to be formatted
   *  according to the rules given below.
   *
   *  If $period is "today" - Date format is 12th June 2014
   *  If $period is "weekly"- Date format is 17th March - 23rd March,2014
   *  If $period is "monthly" Date format is March 2014
   *
   *  @param $period
   *  
   *  @returns A formatted date
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
			$monday		 = $monday->format("jS M");
			$sunday		 = $sunday->format("jS M,Y");
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
