<?php

class alxSystemLog
{
	public static $exitOnFatal = true;

	private $_data = array();
	
	public function add($type='notice', $description, $data=null)
	{
		$item = new stdClass;
		$item->date = time();
		$item->type = $type;
		$item->description = $description;
		$item->data = $data;
		
		$time = microtime();
		
		$this->_data[$time] = $item;
		
		return $time;
	}
	
	public function addLog($msg, $data=null)
	{
		$this->add('log', $msg, $data);
	}
	
	public function addNotice($msg, $data=null)
	{
		$this->add('notice', $msg, $data);
	}
	
	public function addFatal($msg, $data=null)
	{
		$time = $this->add('fatal', $msg, $data);
		
		if(self::$exitOnFatal)
		{
			exit($this->getItem($time));
		}
	}
	
	public function getItems()
	{
		return $this->_data;
	}
	
	public function getItem($time)
	{
		return $this->_data[$time];
	}
	
	public function createHTML()
	{
		$view = new alxView('systemLog');
		
		$view->add('log', $this->getItems());
		
		$view->render(false, true);
	}
}