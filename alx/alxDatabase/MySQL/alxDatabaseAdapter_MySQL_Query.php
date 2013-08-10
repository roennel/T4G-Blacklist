<?php

class alxDatabaseAdapter_MySQL_Query extends alxDatabaseAdapter_Query
{
  function __construct($queryString)
  {
    $this->query = mysql_query($queryString);
		
		return $this->query;
  }
  
  public function getRawQuery()
  {
    return $this->query;
  }

  public function fetch()
  {
    return @mysql_fetch_object($this->query);
  }
  
  public function fetchArray()
  {
    return @mysql_fetch_array($this->query);
  }
  
  public function rows($query)
  {
    return @mysql_num_rows($query);
  }
}