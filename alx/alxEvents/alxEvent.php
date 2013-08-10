<?php

class alxEvent extends alxEvents
{
	protected $_id;
	protected $_callbacks = array();
	protected $_data = array();
	
	function __construct($id, $callback=null)
	{
		$this->_id = $id;
		
		if($callback)
		{
			$this->_callbacks[] = $callback;
		}
	}
	
	public function getId()
	{
		return $this->_id;
	}
	
	public function addVar($key, $value)
	{
		#$this->_data[$key] = $value;
		$GLOBALS["alxEvent_{$key}"] = $value;
	}
	
	public function getVar($key)
	{
		#return $this->_data[$key];
		return $GLOBALS["alxEvent_{$key}"];
	}	
	
	public function addCallback($callback)
	{
		$this->_callbacks[] = $callback;
	}
	
	public function getCallbacks()
	{
		return $this->_callbacks;
	}
	
	public function getCallback($callbackId)
	{
		return $this->_callbacks[$callbackId];
	}
	
	public function runCallbacks($callbackData=null)
	{
		$callbackData = $callbackData ? $callbackData : $this;
		
		foreach($this->getCallbacks() as $callbackId => $callback)
		{
			if(!$callback($this, $callbackData))
			{
				return false;
			}
		}
		
		return true;
	}
	
	public function runCallback($callbackId, $callbackData=null)
	{
		$this->_callbacks[$callbackId]($callbackData);
	}
}

class alxUserEvent extends alxEvent
{

}