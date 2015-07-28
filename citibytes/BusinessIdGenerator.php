<?php

namespace citibytes;

class BusinessIdGenerator
{
	public static function generate($prefix)
	{
		$random_number = mt_rand(100000,999999);
		return $prefix.$random_number;
	}
}


?>
