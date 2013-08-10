<?php

/**
 * Battlefield Play4Free RCON Base Class
 * 
 * Provides Basic RCON Functionality that specific Sub-Classes can Build upon.
 * 
 * ! This Package is based on 'bf2php' from 'jamie.rfurness@gmail.com' (http://code.google.com/p/bf2php/) !
 * 
 * @author Ronny 'roennel' Gysin <roennel@alchemical.cc>
 * @version 0.3-beta
 * @package T4G.BFP4F
 */

namespace BFP4F_Rcon;

class Base
{
	/**
	 * The RCON Version
	 * @access public
	 * @var string
	 */
	public $version;
	
	/**
	 * The IP of the Server
	 * @access public
	 * @var string
	 */
	public $ip;
	
	/**
	 * The RCON Port of the Server
	 * @access public
	 * @var int
	 */
	public $port;
	
	/**
	 * The RCON Password of the Server
	 * @access public
	 * @var string
	 */
	public $pwd;
	
	/**
	 * Socket Container
	 * @access protected
	 * @var resource
	 */
  	protected static $socket;

	/**
	 * Initializes Connection and Log's In
	 * @return bool
	 */
	public function init()
	{
		self::$socket = @fsockopen($this->ip, $this->port);
    
    if(!self::$socket) return '';

    $this->version = $this->read(true);
		
    return $this->version;
  }
  
  public function login()
  {
		if(!$this->login2()) return false;

    	return true;
	}
	
	/**
	 * Tries to Login
	 * @access protected
	 * @return bool
	 */
	protected function login2()
	{
		return $this->query('login '.md5(substr($this->read(true), 17) . $this->pwd)) == 'Authentication successful, rcon ready.';
	}
	
	/**
	 * Executes a Query on the Server
	 * @param string $line
	 * @param bool $bare
	 * @return string
	 */
	public static function query($line, $bare = false) 
	{
		self::write($line, $bare);
		
		if(strpos($result = self::read($bare), 'rcon: unknown command:') === 0) return false;

    	return $result;
  	}
	
	/**
	 * Writes on the Socket
	 * @param string $line
	 * @param bool $bare
	 * @return void
	 */
	protected static function write($line, $bare = false) 
	{
  		fputs(self::$socket, ($bare ? '' : "\x02") . $line . "\n");
  	}
	
	/**
	 * Reads the Buffer
	 * @param bool $bare
	 * @return string
	 */
  	protected static function read($bare = false) 
	{
  		$delim = $bare ? "\n" : "\x04";
    
		for($buffer = '';($char = fgetc(self::$socket)) != $delim;$buffer .= $char);

    	return trim($buffer);
  	}
	
	/*
	 * Destructor; Closes Socket
	 */
  	public function __destruct() 
	{
		if(self::$socket) @fclose($this->socket);
  	}
}
