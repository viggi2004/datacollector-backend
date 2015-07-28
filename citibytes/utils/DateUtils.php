<?php

namespace citibytes\utils;

date_default_timezone_set("Asia/Calcutta");

class DateUtils
{

	// Function to get the datetime of start of today
	public static function getStartOfToday()
	{
		$today = new \DateTime();
		$today->setTime(0,0,1);
		return $today;
	}

	// Function to get the datetime of end of today
	public static function getEndOfToday()
	{
		$today = new \DateTime();
		$today->setTime(23,59,59);
		return $today;
	}


	public static function getFirstDayOfWeek()
	{
    $today = new \DateTime();
    $week_number = $today->format("W");
    $year_number = $today->format("Y");

    $monday    = new \DateTime("${year_number}-W{$week_number}-1 00:00:01");
		return $monday;
	}

	public static function getLastDayOfWeek()
	{
    $today = new \DateTime();
    $week_number = $today->format("W");
    $year_number = $today->format("Y");

    $sunday    = new \DateTime("{$year_number}-W{$week_number}-7 23:59:59");
		return $sunday;
	}

	public static function getFirstDayOfMonth()
	{
		$first_day = new \DateTime("first day of this month 00:00:01");
		return $first_day;
	}

	public static function getLastDayOfMonth()
	{
		$last_day = new \DateTime("last day of this month 23:59:59");
		return $last_day;
	}

}


?>
