<?php

namespace citibytes;

class SMS
{

	const URL = 
"http://bhashsms.com/api/sendmsg.php?user=citibytes&pass=citibytes@2013&sender=CBYTES&phone=%s&text=%s&priority=ndnd&stype=normal";

	public static function send($to,$message)
	{
		$encoded_message = urlencode($message);
		//$encoded_message = $message;
		$url = sprintf(SMS::URL,$to,$encoded_message);
		$response = file_get_contents($url);		
		//TODO: Response of the web service should be parsed
	}
}

?>
