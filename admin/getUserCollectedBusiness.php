<?php

	require_once("../config.php");

	use citibytes\UserProfile;
	use citibytes\utils\MysqlUtils;
	use citibytes\EmployeeAnalytics;
	use citibytes\UserCollectedBusiness;
  use citibytes\exceptions\QueryFailedException;
  use citibytes\exceptions\DatabaseConnectionException;

	$period		= $_REQUEST["period"];
	$email_id	= $_REQUEST["email_id"];

	/**
	 * Can remove the if below after the app has been released.
	 * Previously client doesn't send city. Now city is required. To support
	 * previous builds, this is there.
	 */
	if(isset($_REQUEST["city"]) === FALSE)
		$city = "Bangalore";
	else
		$city = $_REQUEST["city"];
	
	try{

   	$connection = MysqlUtils::getConnection();

		if($period === "today")
			$collected_business = EmployeeAnalytics::getBusinessIdCollectedToday(
																								$connection,$city,$email_id);
		elseif($period === "weekly")
			$collected_business = EmployeeAnalytics::getBusinessIdCollectedThisWeek(
																								$connection,$city,$email_id);
		elseif($period === "monthly")
			$collected_business = EmployeeAnalytics::getBusinessIdCollectedThisMonth(
																								$connection,$city,$email_id);
		else
			$collected_business = EmployeeAnalytics::getBusinessIdCollectedToday(
                                                $connection,$city,$email_id);

		$user_profile	= UserProfile::getProfile($connection,$email_id);
		$user_collected_business = new UserCollectedBusiness($collected_business);
		$user_collected_business = $user_collected_business->get();

		echo json_encode(array("status"		=> "success",
												"user_profile"=> $user_profile, 
												 	 "count"		=> count($user_collected_business),
									"collected_business"=> $user_collected_business));

	}catch(DatabaseConnectionException $e){
    $failure_json = array("status"  => "error" ,
                          "error"   => "Database Unavailable");
    echo json_encode($failure_json);
    return;
  }catch(QueryFailedException $e){
    $failure_json = array("status" => "error" ,
                          "error" => "Unable to get pending pincode requests");
    echo json_encode($failure_json);
    return;
  }

	mysqli_close($connection);

?>
