<?php

class alxApplicationStage
{
	protected $_id;
	protected $_eventPath = null;
	protected $_events = array();
	public $eventCollection;
	
	function __construct()
	{
		$this->eventCollection = new alxEventCollection;
	}
	
	public function getId()
	{
		return $this->_id;
	}
	
	public function setId($id)
	{
		$this->_id = $id;
	}
	
	public function getEventPath()
	{
		return $this->_eventPath;
	}
	
	public function setEventPath($path)
	{
		$this->_eventPath = $path;
	}
	
	public function setEvents(array $events)
	{
		$this->_events = $events;
	}
	
	public function getEvents()
	{
		return $this->_events;
	}
	
	public function loadEventById($eventId)
	{
		$file = $this->getEventPath() . "/{$eventId}.php";
			
		if(file_exists($file))
		{
			$event = new alxEvent($eventId);
				
			include_once $file;
				
			$this->eventCollection->addEvent($event);
		}
		else
		{
			alxApplication::$systemLog->add($this->getId() == 'application' ? 'Fatal' : 'Notice', "Couldn't load Event File", $file);
		}
	}
}