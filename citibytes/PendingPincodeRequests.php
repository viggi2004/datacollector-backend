<?php

namespace citibytes;

use citibytes\exceptions\QueryFailedException;


class PendingPincodeRequests
{

	public function __construct()
	{
	}

	public function save($connection,$data)
	{
		$email					 = $data["email_id"];
		$pincode				 = $data["pincode"];
		$city						 = $data["city"];
		$personal_number = $data["personal_number"];

		$query = "INSERT INTO `pending_pincode_requests` ".
						 "(`email_id`,`personal_number` ,`pincode`, `city`, `request_ts`) ".
						 "VALUES ".
						 "('$email','$personal_number','$pincode', '$city' , NOW())";
	
		$result	= mysqli_query($connection,$query);

		if($result === FALSE)
		{
			error_log($query);
			throw new QueryFailedException("Query failed");
		}
			
		return TRUE;
	}

	public function delete($connection,$data)
	{
		$email	 = $data["email_id"];
		$pincode = $data["pincode"];
		$city		 = $data["city"];

		$query = "DELETE FROM pending_pincode_requests "
						."WHERE email_id='$email' AND city='$city' AND pincode='$pincode'";

		$result = mysqli_query($connection,$query);
		
		if($result === FALSE)
      throw new QueryFailedException("Query failed");
		
		return TRUE;
	}

	/**
	 * Get all pincodes that has been unapprvoed by the admin.
	 *
	 *
	 * @return array	List of all pincodes that isn't approved
	 */

	public function getUnApprovedPincodes($connection,$email,$city)
	{
		
		$unapproved_pincodes = array();
		$query = "SELECT pincode FROM pending_pincode_requests ".
       			 "WHERE email_id='$email' AND ".
						 "city='$city' ";

		$result = mysqli_query($connection,$query);
		
		if($result === FALSE)
			throw new QueryFailedException("Query failed");			
	
		$num_rows = mysqli_num_rows($result);

		if($num_rows === 0)
		{
			return $unapproved_pincodes;
		}

		while($row = mysqli_fetch_assoc($result))
			array_push($unapproved_pincodes,$row["pincode"]);

		return $unapproved_pincodes;
	}

	public function getAllUnApprovedPincodes($connection,$city)
	{
		$unapproved_pincodes = array();
		$query="SELECT email_id,personal_number,GROUP_CONCAT(pincode) AS pincodes ".
					 "FROM pending_pincode_requests ".
					 "WHERE city='$city' GROUP BY email_id ";

		$result = mysqli_query($connection,$query);
		
		if($result === FALSE)
			throw new QueryFailedException("Query failed");			
	
		$num_rows = mysqli_num_rows($result);

		if($num_rows === 0)
		{
			return $unapproved_pincodes;
		}

		while($row = mysqli_fetch_assoc($result))
		{
			$email_id = $row["email_id"];
			$pincodes = explode(",",$row["pincodes"]);
			$personal_number = $row["personal_number"];
			$unapproved_pincodes[$email_id] = array("pincodes" => $pincodes ,
																			 "personal_number" => $personal_number);
		}
	
		return $unapproved_pincodes;

	}

}


?>
