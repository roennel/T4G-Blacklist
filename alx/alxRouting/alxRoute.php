<?php

const ALX_ROUTE_EXTERNAL = 0x10;

class alxRoute
{
	const VPX = '%';

	public $id;
	public $route;
	public $vars = array();
	public $flags = array();
	
	function __construct($id, $route, array $vars=array(), $flag=null)
	{
		$this->id 		= (string) $id;
		$this->route 	= (string) $route;
		$this->vars 	= $vars;
		
		if($flag)
		{
			$this->flags[] = $flag;		
		}
	}
	
	public function addFlag($flag)
	{
		$this->flags[] = $flag;
	}
	
	public function hasFlag($flag)
	{
		return in_array($flag, $this->flags);
	}
	
	public function getComputedString($conditions=null)
	{
		$route = $this->route;
	
		foreach($this->vars as $var => $default)
		{
			switch(true)
			{
				case array_key_exists($var, $conditions):
					$rep = $conditions[$var];
				break;
				
				case alxRequestHandler::getVar($var):
					$rep = alxRequestHandler::getVar($var);
				break;
				
				default:
					$rep = $default;
			}
			
			$route = str_replace(self::VPX . $var, $rep, $route);
		}
		
		return (string) $route;
	}
}