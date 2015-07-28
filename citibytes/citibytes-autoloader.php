<?php

require_once( __DIR__ . "/SplClassLoader.php" );

$directory = dirname(__DIR__);
$loader = new citibytes\SplClassLoader("citibytes",$directory);
$loader->register();

?>
