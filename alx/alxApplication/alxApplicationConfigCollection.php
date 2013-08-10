<?php

class alxApplicationConfigCollection
{
	private $_active = false;
	private $_configs = array();
	
	public function getConfig($configId)
	{
		return $this->_configs[$configId];
	}
	
	public function addConfig(alxApplicationConfig $config)
	{
		$this->_configs[$config->getId()] = $config;
	}
	
	public function getConfigs()
	{
		return $this->_configs;
	}
	
	public function getConfigByHost($host)
	{
		foreach($this->getConfigs() as $config)
		{
			if($config->hasHost($host))
			{
				return $config;
			}
		}
		
		return false;
	}
	
	public function getActiveConfig()
	{
		if($this->_active)
		{
			return $this->_configs[$this->_active];
		}
		
		return false;
	}
	
	public function setActiveConfig($configId)
	{
		$this->_active = $configId;
	}
}