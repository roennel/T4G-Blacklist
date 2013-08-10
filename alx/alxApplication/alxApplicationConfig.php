<?php

class alxApplicationConfig
{
	private $_id;
	private $_data = array();
	private $_hosts = array();
	private $_protected = false;
	
	function __construct($id)
	{
		$this->_id = $id;
	}
	
	public function getId()
	{
		return $this->_id;
	}
	
	public function protect()
	{
		$this->_protected = true;
	}
	
	public function addHost($host)
	{
		$this->_hosts[] = $host;
	}

	public function hasHost($host)
	{
		return in_array($host, $this->_hosts);
	}
	
	public function __set($key, $value)
	{
		if(!$this->_protected)
		{
			$this->_data[$key] = $value;
		}
	}
	
	public function __get($key)
	{
		return $this->_data[$key];
	}
}