<?php

namespace citibytes;

use citibytes\Environment;
use citibytes\utils\SimpleDbUtils;
use citibytes\persister\SimpleDbPersister;

class BusinessCategory 
{

	private $_domain_name;

	//CSV file path containing all the pincodes of a city
	private $csv_file_path;

	public function __construct()
	{
		$this->csv_file_path	= ROOT_DIRECTORY . "/csv/business_category.csv";
		$this->_domain_name		= Environment::getBusinessCategoryDomain();
	}

	//TODO: Error handling if CSV file is not found
	public function import()
	{
		ini_set("auto_detect_line_endings",true);

		$domain_name	= $this->_domain_name;
		$file_name		= $this->csv_file_path;
		$file_handle	= fopen($file_name,"r");

		/**
		 * id,area_name,pincode 
		 */
		$line_number      = 0;
		$header_line      = null;
		$buffered_record_count = 0;
		$buffered_records = array();

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
			$category		= $line[1];
      $category   = trim($category,'"');

			$attributes = array($header_line[0] => $id , 
													$header_line[1] => $category);
			$simpledb_record = array("name" => $id,
															 "attributes" => $attributes);

			//SimpleDb can save a maximum of 25 records in a batch
			if($buffered_record_count < 25)
			{
				$buffered_record_count++;
				array_push($buffered_records,$simpledb_record);
			}
			else
			{
			 $items = SimpleDbUtils::genSimpleDbMultipleItemsArray($buffered_records);
				SimpleDbPersister::batch_save($domain_name,$items);
				$buffered_records = array(); $buffered_record_count = 0;
			}
		}

		//Upload remaining buffered records
		if($buffered_record_count > 0)
		{
			$items = SimpleDbUtils::genSimpleDbMultipleItemsArray($buffered_records);
			SimpleDbPersister::batch_save($domain_name,$items);
		}

	}

	public function getAllCategories()
	{
		$domain_name = $this->_domain_name;
		$query = "SELECT category FROM $domain_name";
		$next_token = "";
		$categories = array();
		
		do{
				$response = SimpleDbPersister::select($query,$next_token);
				$next_token = $response['NextToken'];
				$fetched_records = $response['Items'];
        foreach($fetched_records as $index => $record)
        	array_push($categories,$record["Attributes"][0]["Value"]);
		}while(empty($next_token) === FALSE);

		return $categories;
	}

}

?>
