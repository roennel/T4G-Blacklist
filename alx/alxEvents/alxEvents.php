<?php

class alxEvents
{
	protected static $_events;
	
	public static function initialize()
	{
		self::$_events = new alxEventCollection;
	}	
	
	public static function registerEvent(alxEvent $event)
	{
		self::$_events->addEvent($event);
	}
	
	public static function callEvent($eventId, $callbackData=null)
	{
		$event = self::$_events->getEventById($eventId);
		
		if($event)
		{
			foreach($event->getCallbacks() as $callback)
			{
				$callback($callbackData);
			}
		}
		
		return false;
	}
	
	public static function getEventCollection()
	{
		return self::$_events;
	}
}

alx::load('Events', 'EventCollection');
alx::load('Events', 'Event');