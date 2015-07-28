<?php

namespace citibytes;

use citibytes\exceptions\QueryFailedException;

class ApprovedPincodeRequests
{

	public function __construct()
	{
	}

	public function save($connection,$data)
	{
    $email   = $data["email_id"];
    $pincode = $data["pincode"];
    $city    = $data["city"];
 		$personal_number = $data["personal_number"];

    $query = "INSERT INTO `approved_pincode_requests` ".
             "(`email_id`,`personal_number` ,`pincode`, `city`, `request_ts`) ".
             "VALUES ".
             "('$email','$personal_number','$pincode', '$city', NOW() )";

    $result = mysqli_query($connection,$query);

    if($result === FALSE)
		{
			error_log($query);
      throw new QueryFailedException("Query failed");
		}

    return TRUE;
	}

 	public function delete($connection,$data)
  {
    $email   = $data["email_id"];
    $pincode = $data["pincode"];
    $city    = $data["city"];

    $query = "DELETE FROM approved_pincode_requests "
            ."WHERE email_id='$email' AND city='$city' AND pincode='$pincode'";

    $result = mysqli_query($connection,$query);

    if($result === FALSE)
      throw new QueryFailedException("Query failed");

    return TRUE;
  }

	/**
	 * Get all pincodes that has been apprvoed by the admin for an user.
	 *
	 *
	 * @return array	List of all pincodes that isn't approved
	 */

	public function getApprovedPincodes($connection,$email,$city)
	{
		$approved_pincodes = array();
		$query = "SELECT pincode FROM approved_pincode_requests ".
       			 "WHERE email_id='$email' AND ".
						 "city='$city' ";
		$result = mysqli_query($connection,$query);

		if($result === FALSE)
			throw new QueryFailedException("Query failed");

		$row_count = mysqli_num_rows($result);
		if($row_count === 0)
			return $approved_pincodes;

		while($row = mysqli_fetch_assoc($result))
		{
			$pincode = $row["pincode"];
			array_push($approved_pincodes,$pincode);
		}

		return $approved_pincodes;
	}

 	public function getAllApprovedPincodes($connection,$city)
  {
    $approved_pincodes = array();
    $query="SELECT email_id,personal_number,GROUP_CONCAT(pincode) AS pincodes ".
           "FROM approved_pincode_requests ".
           "WHERE city='$city' GROUP BY email_id ";

    $result = mysqli_query($connection,$query);

    if($result === FALSE)
      throw new QueryFailedException("Query failed");

    $num_rows = mysqli_num_rows($result);

    if($num_rows === 0)
    {
      return $approved_pincodes;
    }

    while($row = mysqli_fetch_assoc($result))
    {
      $email_id = $row["email_id"];
      $pincodes = explode(",",$row["pincodes"]);
      $personal_number = $row["personal_number"];
      $approved_pincodes[$email_id] = array("pincodes" => $pincodes ,
                                  	 "personal_number" => $personal_number);
    }

    return $approved_pincodes;
  }

}


?>
