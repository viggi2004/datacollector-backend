<?php

namespace citibytes;

use citibytes\exceptions\QueryFailedException;

class User
{

	public static function getUserInfo($connection,$email)
	{
		$query	= "SELECT * FROM users WHERE email_id='$email'";
		$result	= mysqli_query($connection,$query);

		if($result === FALSE)
			throw new QueryFailedException("Query failed"); 

		$row_count = mysqli_num_rows($result);	
		if($row_count === 0)
			return NULL;

		$user_info = mysqli_fetch_assoc($result);
		return $user_info;
	} 

	public static function addNewUser($connection,$data)
	{

		$email				= $data["email_id"];
		$display_name = $data["display_name"];
		$personal_number = $data["personal_number"];
		$business_number = $data["business_number"];
		//A new user has the role of a non-admin
		$is_admin			= 0;

		$query = "INSERT INTO users ".
         		 "(`email_id`, `display_name`, `is_admin`,`personal_number`, ".
         		 "`business_number`, `created_ts`) " .
         		 "VALUES ".
         		 "('$email','$display_name','$is_admin','$personal_number', ".
         		 "'$business_number', NOW());";


		$result = mysqli_query($connection,$query);

		if($result === FALSE)
		{
			error_log($query);
			throw new QueryFailedException("Query failed"); 
		}

		return $result;

	}
}


?>
