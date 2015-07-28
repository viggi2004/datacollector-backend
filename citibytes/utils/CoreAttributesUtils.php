<?php

namespace citibytes\utils;

use citibytes\Environment;
use citibytes\CitibytesSmarty;
use citibytes\persister\SimpleDbPersister;

class CoreAttributesUtils
{

	/**
	 * Checks whether there are any business like the one being saved
	 *
	 *	Rules for finding if two business are duplicates. If any one of the below
	 *	mentioned rules are satisfied, then the business data passed in as $data
	 *	param is a duplicate business.
	 *
	 *	 1) Any business already saved should not have atleast one mobile number 
	 *			same as the business being uploaded and status of business is
	 * 			BUSINESS_VERIFIED_COMPLETE.
	 *	 2) Any business already saved should not have atleast one landline number
	 *			same as the business being uploaded and status of business is
	 *			BUSINESS_VERIFIED_COMPLETE.
	 */

	public static function exists($data)
	{
		$mobile_number		= $data["mobile_number"];
		$domain_name			= Environment::getCoreAttirbutesDomain();

		$mobile_number = CoreAttributesUtils::implode($mobile_number);
		$query	=	"SELECT status FROM $domain_name WHERE mobile_number ".
							"IN ($mobile_number) ";
		
		$result = SimpleDbPersister::select($query);
		$items	= $result["Items"];
		if(isset($items) === TRUE 
				&& $items[0]["Attributes"][0]["Value"] == "BUSINESS_VERIFIED_COMPLETE")
			return TRUE;

		//Landline number not set, so no need to check
		if(isset($data["landline_number"]) === FALSE)
			return FALSE;

		$landline_number	= $data["landline_number"];	
		$landline_number =  CoreAttributesUtils::implode($landline_number);
		$query	=	"SELECT status FROM $domain_name WHERE landline_number ".
							"IN ($landline_number) ";
		
		$result = SimpleDbPersister::select($query);
		$items	= $result["Items"];
		if(isset($items) === TRUE 
			 && $items[0]["Attributes"]["Value"] == "BUSINESS_VERIFIED_COMPLETE")
			return TRUE;
	
		return FALSE;
	}

	public static function generateSMSMessage($data)
	{
		$smarty	= new CitibytesSmarty();	
		$smarty->assign("business_id",$data["business_id"]);
		$smarty->assign("business_name",$data["business_name"]);
		$smarty->assign("contact_person_name",$data["contact_person_name"]);
		$address = $data["address_line_1"];
		if(isset($data["address_line_2"]) === TRUE)
			$address.= ",".$data["address_line_2"];
		$smarty->assign("address",$address);
		$smarty->assign("city",$data["city"]);
		$smarty->assign("state",$data["state"]);
		$smarty->assign("pincode",$data["pincode"]);
		$smarty->assign("mobile_number",implode(",",$data["mobile_number"]));
		$smarty->assign("otp",$data["otp"]);
		
		if(isset($data["chain_name"]) === TRUE)
			$smarty->assign("chain_name",$data["chain_name"]);

		if(isset($data["landmark"]) === TRUE)
			$smarty->assign("landmark",implode(",",$data["landmark"]));

		if(isset($data["website"]) === TRUE)
			$smarty->assign("website",$data["website"]);

		if(isset($data["email"]) === TRUE)
		{
			$email = str_replace("@"," at ",$data["email"]);
			$email = str_replace("."," dot ",$email);
			$smarty->assign("email",$email);
		}

		if(isset($data["landline_number"]) === TRUE)
			$smarty->assign("landline_number",implode(",",$data["landline_number"]));


		$message = $smarty->fetch("sms.tpl");
		
		return $message;
	}	

	private static function implode($array)
	{
		foreach($array as $index => $value)
			$array[$index] = "'" . $value . "'";
		$array = implode(",",$array);
		return $array;
	}
}


?>
