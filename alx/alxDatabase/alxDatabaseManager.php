<?php

class alxDatabaseManager
{
	const DEFAULT_XMLQ_FILE = 'lib/database.xmlq';

	public static $_config;
	
	protected static $adapter;
	protected static $xmlqBuffer = null;

	public static function setAdapter(alxDatabaseAdapter $adapter)
	{
		self::$adapter = $adapter;
	}

	public static function connect()
	{
		if(self::$adapter)
		{
			return self::$adapter->connect(self::$_config);
		}
		
		return false;
	}
	
	public static function disconnect()
	{
		if(self::$adapter)
		{	
			return self::$adapter->disconnect();
		}
		
		return false;
	}
	
	public static function query($query)
	{
		if(self::$adapter)
		{
		  #echo "<br/>\n{$query}";
		  
			return self::$adapter->query($query);
		}
		
		return false;
	}
  
  public static function rows($query)
  {
    if(self::$adapter)
    {
      return self::$adapter->rows($query);
    }
    
    return false;
  }
  
  public static function fetchMultiple($query)
  {
    $items = array();
    
    $qry = self::query($query);
    
    while($item = $qry->fetch())
    {
      $items[] = $item;
    }
    
    return $items;
  }
	
	public static function load($id)
	{
		if(!self::$xmlqBuffer)
		{
			self::bufferXMLQ();
		}

		$element = self::$xmlqBuffer->getElementById($id);
		
		if($element->hasAttributes())
		{
			// non sql - query options
		}
		
		$query = $element->nodeValue;
		
		return self::query($query);
	}
	
	protected static function bufferXMLQ()
	{
		if(!self::$_config->xmlqFile)
		{
			self::$_config->xmlqFile = self::DEFAULT_XMLQ_FILE;
		}
		
		self::$xmlqBuffer = new DOMDocument;
		self::$xmlqBuffer->validateOnParse = true;
		self::$xmlqBuffer->load(self::$_config->xmlqFile);
	}
}

alx::load('Database', 'DatabaseAdapter');
alx::load('Database', 'DatabaseAdapter_Query');