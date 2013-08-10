<?php

class alxEventCollection
{
	protected $_events = array();
	
	public function getEventById($id)
	{
		if(array_key_exists($id, $this->_events))
		{
			return $this->_events[$id];
		}
		
		return false;
	}
	
	public function addEvent(alxEvent $event)
	{
		$this->_events[$event->getId()] = $event;
	}
	
	public function getEvents()
	{
		return $this->_events;
	}
}