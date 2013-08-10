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

class Server
{
	public function fetch()
	{
		$data = Base::query('bf2cc si');
		
		$spl = explode("\t", $data);
		
		$result = array
		(
			'name' => $spl[7],
			'map' => $spl[5],
			'playersCurrent' => $spl[3],
			'playersMax' => $spl[2],
			'playersJoining' => $spl[4],
			'tickets' => array($spl[10] - $spl[11], $spl[10] - $spl[16]),
			'ticketsMax' => $spl[10],
			'timeElapsed' => $spl[18],
			'timeRemaining' => $spl[19],
			'gameMode' => $spl[20]
		);
		
		return (object) $result;
	}
}