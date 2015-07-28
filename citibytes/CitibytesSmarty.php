<?php

namespace citibytes;

require_once(ROOT_DIRECTORY.'/libs/Smarty/libs/Smarty.class.php');

class CitiBytesSmarty extends \Smarty
{

	public function __construct()
	{
		parent::__construct();	
		$this->setTemplateDir(ROOT_DIRECTORY."/templates/");
		$this->setCompileDir("/tmp/");
	}


}


?>
