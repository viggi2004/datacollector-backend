<?php

require_once("SMS.php");

$sms_contents = file_get_contents("/tmp/smstemplate");

SMS::send("9585518355",$sms_contents);

?>
