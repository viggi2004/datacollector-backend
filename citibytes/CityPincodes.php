<?php

namespace citibytes;

use citibytes\utils\MysqlUtils;
use citibytes\exceptions\DatabaseConnectionException;
use citibytes\exceptions\QueryFailedException;

class CityPincodes
{

	private $city_name;

	//CSV file path containing all the pincodes of a city
	private $csv_file_path;

	//SimpleDb domain name
	private $domain_name;

	public function __construct($city_name)
	{
		$this->city_name			= $city_name;
		$this->csv_file_path	= "csv/{$city_name}_pincodes.csv";
		$this->domain_name		= "{$city_name}_pincodes";
	}

	/** 
	 *	Get pincode info. The pincode and the areas under it
	 *
	 *	@params $pincodes array containing the list of pincodes
	 *	@return Associative Array with key as the pincode and the
	 *					areas under the pincode as the value in an array
	 */
	public function batchGetPincodeInfo($connection,$pincodes)
	{
		if(empty($pincodes) === TRUE)
			return array();

		$pincodes_string = implode(",",$pincodes);
		
		$query = "SELECT pincode,GROUP_CONCAT(area_name) AS covered_areas "
						."FROM pincodes "
						."WHERE pincode IN ($pincodes_string) GROUP BY pincode";

		$result = mysqli_query($connection,$query);

		if($result === FALSE)
			throw new QueryFailedException("Query failed");

		$output		 = array();
		
		while($row = mysqli_fetch_assoc($result))
		{
			$pincode					= $row["pincode"];
			$covered_areas		= $row["covered_areas"];
			$covered_areas		= explode(",",$covered_areas);
			$output[$pincode]	= $covered_areas; 
		}

		return $output;
	}


	//TODO: Error handling if CSV file is not found
	public function import()
	{
		ini_set("auto_detect_line_endings",true);

		$city_name		= $this->city_name;
		$file_name		= $this->csv_file_path;
		$file_handle	= fopen($file_name,"r");
		$connection		= null;

		try{

	  	$connection = MysqlUtils::getConnection();

		}catch(DatabaseConnectionException $e){
			echo "Cannot connect to database".PHP_EOL;
			exit;
		}

		/**
		 * id,area_name,pincode 
		 */
		$line_number      = 0;
		$header_line      = null;

		while(($line = fgetcsv($file_handle,$file_name)) !== FALSE)
		{
			$line_number++;

			//Skip first line as they are the column headers in excel sheet
			if($line_number == 1)
			{
				$header_line = $line;
				continue;
			}

			$id         = $line[0];
			$area_name  = $line[1];
			$pincode    = $line[2];

			$query ="INSERT INTO `pincodes` (`id`, `pincode`, `area_name`, `city`) ".
							"VALUES (NULL, '$pincode', '$area_name', '$city_name')";
			$result = mysqli_query($connection,$query);

		}

		mysqli_close($connection);

	}

	public function getAllPinCodes($connection)
	{
		$city = $this->city_name;
		$query = "SELECT pincode,GROUP_CONCAT(area_name) AS covered_areas ".
						 "FROM pincodes WHERE city='$city' GROUP BY pincode;";
		$result = mysqli_query($connection,$query);	

		if($result === FALSE)
      throw new QueryFailedException("Query failed");
		
		$pincodes = array();
		while($row = mysqli_fetch_assoc($result))
		{
			$pincode			 = $row["pincode"];
			$covered_areas = $row["covered_areas"];
			$covered_areas = explode(",",$covered_areas);
			$pincodes[$pincode]	= $covered_areas;
		}
		
		return $pincodes;
	}

}

?>
