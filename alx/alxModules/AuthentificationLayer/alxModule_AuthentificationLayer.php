<?php

class alxModule_AuthentificationLayer
{
	const EVENTID_SUCCESS = 'onUserAuthentificationSuccess';
	const EVENTID_FAILURE = 'onUserAuthentificationFailure';

	protected $adapter;
	
	protected $guestUser = false;
	protected $sessionKey = 'alxModule_AuthentificationLayer';

	public function setUserAdapter(alxModel $model)
	{
		$this->adapter = $model;
	}
	
	public function enableGuestUser()
	{
		$this->guestUser = true;
	}
	
	public function disableGuestUser()
	{
		$this->guestUser = false;
	}
	
	public function run()
	{
		$this->id = 'authentificationLayer';
	
		if(!alxEvents::getEventCollection()->getEventById(self::EVENTID_SUCCESS))
		{
			$event = new alxEvent(self::EVENTID_SUCCESS);

			$event->addCallback(function()
			{
				
			});

			alxEvents::registerEvent($event);
		}
		
		if(!alxEvents::getEventCollection()->getEventById(self::EVENTID_SUCCESS))
		{
			$event = new alxEvent(self::EVENTID_SUCCESS);

			$event->addCallback(function()
			{
				
			});

			alxEvents::registerEvent($event);
		}
	
		$userSession = alxSessionManager::getVar($this->sessionKey, true);
	
		if(!$userSession)
		{
			$userSession = new alxModule_AuthentificationLayer_UserSessionObject;
			$userSession->setAuthState(false);
		}
		
		alxSessionManager::setVar($this->sessionKey, $userSession);
		
		if($userSession->isLogged() or (!$userSession->isLogged() && $this->guestUser))
		{
			if($this->checkPermissions())
			{
				alxEvents::callEvent(self::EVENTID_FAILURE);
				return true;
			}
		}

		alxEvents::callEvent(self::EVENTID_FAILURE);
		return false;
	}
}

class alxModule_AuthentificationLayer_UserSessionObject
{
	protected $_loggedIn = false;
	protected $_permission = 0;
	
	public function setAuthState($state)
	{
		if($state == true)
		{
			$this->_loggedIn = true;
			return;
		}
		
		$this->_loggedIn = false;
	}

	public function setPermission($permission)
	{
		$this->_permission = $permission;
	}
}