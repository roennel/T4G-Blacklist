<?php

class alxDatabaseAdapter_MySQL extends alxDatabaseAdapter
{
	function connect($config) 
	{
		$this->cnx = mysql_connect($config->host, $config->user, $config->pwd);
		
		if($config->db)
		{
			$this->database($config->db);
		}
		
		return $this->cnx;
	}
  
	function disconnect()
	{
		return mysql_close($this->cnx);
	}
	
	function database($db) 
	{
		mysql_select_db($db, $this->cnx);
	}
	
	function query($query) 
	{
		return new alxDatabaseAdapter_MySQL_Query($query);
	}
  
  function rows($query)
  {
    return mysql_num_rows($query);
  }
	
	public function lastId()
	{
		return mysql_insert_id();
	}
}

alx::load("Database/MySQL", "DatabaseAdapter_MySQL_Query");