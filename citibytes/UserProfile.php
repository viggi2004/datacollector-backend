<?php

namespace citibytes;

use citibytes\exceptions\QueryFailedException;

class UserProfile
{

	public function __construct()
	{
		
	}

	public static function getProfile($connection,$email_id)
	{
		$query	= "SELECT * FROM users WHERE email_id='$email_id'";	
		$result = mysqli_query($connection,$query);
	
		if($result === FALSE)
      throw new QueryFailedException("Query failed");

		$result = mysqli_fetch_assoc($result);
		
		$result["is_admin"] = $result["is_admin"] == "1" ? TRUE : FALSE;  
		return $result;
	}

	public static function editProfile($connection,$email_id,
																		 $mobile_number,$business_number)
	{
		$query = "UPDATE users ".
						 "SET personal_number = '$mobile_number', ".
						 "business_number = '$business_number' ".
						 "WHERE email_id='$email_id'";


		$result = mysqli_query($connection,$query);

		if($result === FALSE)
		{
			error_log($query);
			throw new QueryFailedException("Query failed");
		}

		return $result;
	}

	/**
	 *	Sets the role of the user
	 *
	 *	The possible roles are either admin or content associate. The database
	 *	has a column `is_admin` of boolean data type which determines whether
	 *	the user is an admin or content associate.
	 */

	public static function setRole($connection,$email_id,$is_admin)
	{
		$query = "UPDATE users SET is_admin = $is_admin WHERE email_id='$email_id'";
		$result= mysqli_query($connection,$query);

		if($result === FALSE)
		{
			error_log($query);
			throw new QueryFailedException("Query Failed");
		}

		return $result;
	}

}


?>
