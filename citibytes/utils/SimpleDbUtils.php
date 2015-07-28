<?php

namespace citibytes\utils;

class SimpleDbUtils
{

	public static function genSimpleDbAttributesArray($data,$replace = FALSE)
	{
		$attributes = array();
		foreach($data as $key => $value)
		{
			//Values is an array, so should be pushed as multiple values
			if(is_array($value) === TRUE)
			{
				$size = count($value);
				for($i = 0 ; $i < $size ; $i++)
				{
					$array = array("Name" => $key ,
												 "Value" => $value[$i] , 
												 "Replace" => $replace);
					array_push($attributes,$array);
				}
				continue;
			}
				$array = array("Name" => $key,"Value" => $value,"Replace"=>$replace);
				array_push($attributes,$array);
		}
		return $attributes;
	}

	/**
	 *	Utility to generate the array to be passed to batchPutAttributes web
	 *	service of SimpleDb.
	 */
	public static function genSimpleDbMultipleItemsArray($records)
	{
		$output = array();
		foreach($records as $index => $record)	
		{
			$item_name	= $record["name"];
			$item_attributes = SimpleDbUtils::genSimpleDbAttributesArray(
																												$record["attributes"]);
			$item = array("Name" => $item_name , 
										"Attributes" => $item_attributes);
			array_push($output,$item);
		}
		return $output;
	}
	
	
}


?>
