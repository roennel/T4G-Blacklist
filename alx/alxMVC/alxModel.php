<?php

/**
 * alxModel
 * Abstract Base Class
 *
 * @author 		Ronny Gysin <roennel@alchemical.cc>
 * @version		0.2
 */
abstract class alxModel
{
	/**
	 * Data Container
	 * @access 	private
	 * @var 		array
	 */
	private $_data = array();
	
	/**
	 * Aliases Container
	 * @access 	protected
	 * @var 		array
	 */
	protected $_aliases = array();
	
	/**
	 * The Primary ID Key of the Table
	 * @access 	protected
	 * @var 		string
	 */
	protected $idKey = 'id';
	
	/**
	 * The Database Table
	 * @access 	protected
	 * @var 		string
	 */
	protected $table;
	
	/**
   * Creates a DB Entry from the Object
   * @return boolean
   */
	final public function create()
	{
		if(!$this->{$this->idKey})
		{
			$query = "INSERT INTO $this->table SET ";
			$query.= $this->getFormattedValues();
		
			if(alxDatabaseManager::query($query))
			{
				$this->{$this->idKey} = mysql_insert_id();
			
				return true;
			}
		}
		
		return false;
	}
	
	/**
   * Creates a DB Entry from the Object
	 * @param 	array $options
	 * @param 	array $sort
   * @return 	boolean
   */
	final public function load(array $options, array $sort=null)
	{
		$query = "SELECT * FROM $this->table WHERE ";
		$query.= $this->getFormattedValues(' AND ', $options);
		
		if($sort)
		{
			$query.= " ORDER BY $sort[0] " . strToUpper($sort[1]);
		}
			
		$query.= " LIMIT 1";

		$data = alxDatabaseManager::query($query)->fetch();
		
		if($data)
		{
			foreach($data as $key => $val)
			{
				if(array_key_exists($key, $this->_aliases))
				{
					$key = $this->_aliases[$key];
				}
			
				$this->$key = $val;
			}
			
			return true;
		}
		
		return false;
	}

	/**
   * Updates the DB Entry of the current Object
   * @return boolean
   */
	final public function update()
	{
		if($this->{$this->idKey})
		{
			$id = $this->{$this->idKey};
			
			$query = "UPDATE $this->table SET ";
			$query.= $this->getFormattedValues(); 
			$query.= " WHERE $this->idKey = '$id'";
		
			if(alxDatabaseManager::query($query))
			{
				return true;
			}
		}
		
		return false;
	}
	
	/**
   * Deletes the DB Entry of the current Object
   * @return boolean
   */
	final public function delete()
	{
		if($this->{$this->idKey})
		{
			$id = $this->{$this->idKey};
		
			$query = "DELETE FROM $this->table WHERE $this->idKey = '$id'";

			if(alxDatabaseManager::query($query))
			{
				return true;
			}
		}
		
		return false;
	}
	
	/**
   * Alias Method for getAll (Done for Backwards Compatibility)
	 * @param		array $options
	 * @param		array $sort
   * @return 	array
   */
	final public function getItems($options=null, $sort=null, $limit=null)
	{
		return $this->getAll($options, $sort, $limit);
	}
	
	/**
   * Factory
	 * @param		array $options
	 * @param		array $sort
   * @return 	array
   */
	final public function getAll($options=null,$sort=null, $limit=null)
	{
		$query = "SELECT $this->idKey FROM $this->table";
		
    if($options)
    {
      $query.= " WHERE ";
      $wheres = array();
      
      foreach($options as $key => $val)
      {
        $wheres[] = "{$key} = '{$val}'";
      }
      
      $query.= implode(' AND ', $wheres);
    }
    
		if($sort)
		{
			$query.= " ORDER BY $sort[0] " . strToUpper($sort[1]);
		}
		
		if($limit)
		{
			$query.= " LIMIT $limit";
		}

		$ids = alxDatabaseManager::query($query);
		
		$instances = array();
		
		while($data = $ids->fetch())
		{
			$instanceName = get_class($this);

			$instance  = new $instanceName;
			$instance->loadById($data->{$this->idKey});
			
			$valid = true;

			if($valid)
			{
				$instances[] = $instance;
			}
		}
		
		return $instances;
	}
	
	/**
   * Loads Object Data by Primary Key
	 * @param		string $id
   * @return 	boolean
   */
	final public function loadById($id)
	{
		return $this->load(array
		(
			$this->idKey => $id
		));
	}	
	
	/**
   * Magic Method: __set
	 * @param		string 	$key
	 * @param		mixed 	$value
   * @return 	void
   */
	final public function __set($key, $value)
	{
		$this->_data[$key] = $value;
	}
	
	/**
   * Magic Method: __get
	 * @param		string|integer $key
   * @return 	mixed
   */
	final public function __get($key)
	{
		if(array_key_exists($key, $this->_data))
		{
			return $this->_data[$key];
		}
		
		return false;
	}
	
	final public function getData()
	{
		return $this->_data;
	}
	
	/**
   * Formats current Object Values to a valid SQL String
	 * @access	protected
	 * @param		string 	$glue
	 * @param		array 	$data
   * @return 	string
   */
	final protected function getFormattedValues($glue=',', $data=null)
	{
		$items 		= array();
		$aliases 	= array_flip($this->_aliases);
		
		foreach($data ? $data : $this->_data as $key => $val)
		{
			if(isset($val))
			{
				if(array_key_exists($key, $aliases))
				{
					$key = $aliases[$key];
				}
		
				$items[] = "`$key`='$val'";
			}
		}
		
		return implode($glue, $items);
	}
}