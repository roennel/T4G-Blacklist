<?php

class alxFile
{
	protected $file;
	protected $path;
	protected $type = 'text/plain';
	protected $content = null;
	
	function __construct($file=null, $path=null, $type=null)
	{
		if($file) $this->file = $file;
		if($path) $this->path = $path;
		if($type) $this->type = $type;
	}
	
	public function __get($key)
	{
		return $this->{$key};
	}
	
	public function __set($key, $value)
	{
		$this->{$key} = $value;
	}
	
	public function addLine($content)
	{
		$this->content.= "$content\n";
	}
}