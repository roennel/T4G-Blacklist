<?php

/**
 * Battlefield Play4Free RCON Chat Sub-Class
 * 
 * Provides Chat-Based Methods
 * 
 * ! This Package is based on 'bf2php' from 'jamie.rfurness@gmail.com' (http://code.google.com/p/bf2php/) !
 * 
 * @author Ronny 'roennel' Gysin <roennel@alchemical.cc>
 * @version 0.3-beta
 * @package T4G.BFP4F
 */

namespace BFP4F_Rcon;

class Chat
{
	public function fetch($limit=null)
	{
		$data = Base::query('bf2cc serverchatbuffer');
		
		$spl = explode("\r\r", $data);
		
		$result = array();
		
		$i = 0;
		
		foreach($spl as $chat)
		{
		 	$ex = explode("\t", $chat);
      
      if(count($ex) < 2) continue;
      
			list($index, $origin, , $type, $time, $message) = $ex;
			
			if(!$limit or $limit > count($spl)-$i)
			{
				$result[] = (object) array
				(
					'origin'	=> 	$origin,
        			'type'    =>  $type,
        			'time'    =>  substr($time, 1, -1),
        			'message' =>  $message,
        			'index'   =>  $index
      			);
			}
			
			$i++;
		}

		return (object) $result;
	}
	
	public function send($message)
	{
  		return trim(Base::query('exec game.sayAll "'.$message . '"'));
	}
	
	public function sendPlayer($player, $message) // DOESN'T REALLY WORK, IT SENDS IT TO ALL PLAYERS (GLOBAL)
	{ 
	   return trim(Base::query('exec game.sayToPlayerWithName ' . $player . ' "' . $message . '"'));
  	//return trim(Base::query('bf2cc sendplayerchat \"' . $player . '\" ' . $message . ""));
	}
}