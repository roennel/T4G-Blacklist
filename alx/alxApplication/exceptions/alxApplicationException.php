<?php

class alxApplicationException extends Exception
{
	public static $lastMessage = null;
	
	private $data = null;
	
	function __construct($message, $data=null) 
	{
		$this->data = $data;
		
   	parent::__construct($message, 0);
  }
	
	public function handle()
	{
		$msg = $this->getMessage();
		$add = self::$lastMessage;
		
		echo <<<EOT
<h2>Exception</h2>
<p>$msg</p>
<p>$add</p>
EOT;
		
		die();
	}
}