<?php

class alxModuleAuthDatabase extends alxModule
{
	const SESSION_VAR = 'AuthDatabaseObject';

	function __construct()
	{
		$this->id = 'AuthDatabase';
	}
	
	public function run()
	{
		if(!$this->getSessionObject())
		{
			$this->createSessionObject();
		}
		
		$sessionObject = $this->getSessionObject();
		
		if($sessionObject->validate())
		{
			// Authentification Success
			return true;
		}
		
		// Authentification Failed
		return false;
	}
	
	protected function getSessionObject()
	{
		return unserialize(alxSessionManager::getVar(self::SESSION_VAR));
	}
	
	protected function setSessionObject($sessionObject)
	{
		alxSessionManager::setVar(self::SESSION_VAR, serialize($sessionObject));
	}
	
	protected function createSessionObject()
	{
		$this->setSessionObject(new alxModuleAuthDatabase_SessionObject);
	}
}

class alxModuleAuthDatabase_SessionObject
{
	protected $userId;
	protected $sessionId;
	
	function __construct()
	{
		$this->userId = 0;
		$this->sessionId = alxSessionManager::getSessionId();
	}
	
	public function validate()
	{
		
	}
}