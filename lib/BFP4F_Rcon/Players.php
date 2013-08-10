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

class Players
{
	public function fetch()
	{
		$data = Base::query('bf2cc pl');
		
		$spl = explode("\r", $data);
		$vars = 47;

		$chunks = array_chunk($spl, $vars);
		
		$soldiers = array();
		
		for($i=0,$c=count($chunks);$i<$c;$i++)
		{
			$soldiers[$i] = new \stdClass;
		}
		
		$i = 0;
		
    	$ch = array();
    	$x = 0;
    
    
    	for($b=0,$c=count($spl);$b<$c;$b++)
    	{
      		$v = explode("\t", $spl[$b]);

      		$ch[$b] = $v;
    	}
 
		foreach($ch as $item)
		{
			$current = &$soldiers[$i];
      
      if(@$chunks[$i+1])
      {
        $next = &$chunks[$i+1];
      }
      
			@$current->index = @$item[0];
			$current->name = @$item[1];
			$current->team = @$item[2];
			$current->ping = @$item[3];
			$current->connected = @$item[4];
			$current->valid =@ $item[5];
			$current->remote = @$item[6];
			$current->ai = @$item[7];
			$current->alive = @$item[8];
			$current->manDown = @$item[9];
			$current->profileId = @$item[10];
			$current->flagHolder = @$item[11];
			$current->suicide = @$item[12];
			$current->timeToSpawn = @$item[13];
			$current->squadId = @$item[14];
			$current->squadLeader = @$item[15];
			$current->commander = @$item[16];
			$current->spawnGroup = @$item[17];
			$current->ip = @$item[18];
			$current->damageAssists = @$item[19];
			$current->passengerAssists = @$item[20];
			$current->targetAssists = @$item[21];
			$current->revives = @$item[22];
			$current->teamDamages = @$item[23];
			$current->teamVehicleDamages = @$item[24];
			$current->cpCaptures = @$item[25];
			$current->cpDefends = @$item[26];
			$current->cpAssists = @$item[27];
			$current->cpNeutralizes = @$item[28];
			$current->cpNeutralizeAssists = @$item[29];
			$current->suicides = @$item[30];
			$current->kills = @$item[31];
			$current->tk = @$item[32];
			//$current->undefined = @$item[33];
			$current->kit = @$item[34];
			$current->time = @$item[35];
			$current->deaths = @$item[36];
			$current->score = @$item[37];
			$current->vehicleName = @$item[38];
			$current->level = @$item[39];
			$current->position = @$item[40];
			//$current->undefined = @$item[41];
			$current->cdKeyHash = @$item[42];
			//$current->undefined = @$item[43];
			//$current->undefined = @$item[44];
			//$current->undefined = @$item[45];
			$current->vip = @$item[46];
			$current->nucleusId = @$item[47];
			
			$i++;
		}
		
		return $soldiers;
	}
	
	public function kick($playerId, $reason=null)
	{
		Base::query("kick \"{$playerId}\" \"{$reason}\"");
	}
	
	public function ban($playerId, $time, $reason=null)
	{
		Base::query("ban {$playerId} {$time} \"{$reason}\"");
	}
}