<?php

class AwardTranslate
{
  protected static $strings = 
  [
    'de' =>
    [
      'CATEGORY' =>
      [
        'SOLDIER'     => 'Soldaten Awards',
        'TEAM'        => 'Team Awards',
        'WEAPON'      => 'Waffen Awards',
		'VEHICLE'	  => 'Fahrzeug Awards',
		'T4G' 		  => 'T4G Awards'
      ],
      'AWARD' =>
      [
	    'LEVEL30'   		=> 'General',
        'LEVEL30_0' 		=> 'Level %N%',
        'LEVEL30_1' 		=> 'Level %N%',
        'LEVEL30_2' 		=> 'Level %N%',
        'LEVEL30_3' 		=> 'Level %N%',
        'GAMES_PLAYED'   	=> 'Dauergast',
        'GAMES_PLAYED_0' 	=> '%N% Spiele',
        'GAMES_PLAYED_1' 	=> '%N% Spiele',
        'GAMES_PLAYED_2' 	=> '%N% Spiele',
        'GAMES_PLAYED_3' 	=> '%N% Spiele',
		'BESTSCORE'      	=> 'Highscorer',
        'BESTSCORE_0'    	=> '%N% Pkt',
        'BESTSCORE_1'    	=> '%N% Pkt',
        'BESTSCORE_2'    	=> '%N% Pkt',
        'BESTSCORE_3'    	=> '%N% Pkt',
		'SPM'   			=> 'Fleißige Biene',
        'SPM_0' 			=> '%N% Pkt',
        'SPM_1' 			=> '%N% Pkt',
        'SPM_2' 			=> '%N% Pkt',
        'SPM_3' 			=> '%N% Pkt',
        'KILLSTREAK'     	=> 'Serienkiller',
        'KILLSTREAK_0'   	=> '%N% Kill-Serie',
        'KILLSTREAK_1'   	=> '%N% Kill-Serie',
        'KILLSTREAK_2'   	=> '%N% Kill-Serie',
        'KILLSTREAK_3'   	=> '%N% Kill-Serie',
        'DEATHSTREAK'    	=> 'König der Zielscheiben',
        'DEATHSTREAK_0'  	=> '%N% Verlust-Serie',
        'DEATHSTREAK_1'  	=> '%N% Verlust-Serie',
        'DEATHSTREAK_2'  	=> '%N% Verlust-Serie',
        'DEATHSTREAK_3'  	=> '%N% Verlust-Serie',
		'EAGLE_EYE'    		=> 'Adlerauge',
        'EAGLE_EYE_0'  		=> '%N% Pkt',
        'EAGLE_EYE_1'  		=> '%N% Pkt',
        'EAGLE_EYE_2'  		=> '%N% Pkt',
        'EAGLE_EYE_3'  		=> '%N% Pkt',
		'ROADKILLS'   		=> 'Kühlerfigur',
        'ROADKILLS_0' 		=> '%N% Roadkills',
        'ROADKILLS_1' 		=> '%N% Roadkills',
        'ROADKILLS_2' 		=> '%N% Roadkills',
        'ROADKILLS_3' 		=> '%N% Roadkills',
		'HEADSHOTS'     	=> 'Kopfjäger',
        'HEADSHOTS_0'  		=> '%N%% Kopftreffer',
        'HEADSHOTS_1'  		=> '%N%% Kopftreffer',
        'HEADSHOTS_2'  		=> '%N%% Kopftreffer',
        'HEADSHOTS_3'  		=> '%N%% Kopftreffer',
		'HEADSHOTS2'   		=> 'Kopfjäger ',
        'HEADSHOTS2_0' 		=> '%N%% Kopftreffer',
        'HEADSHOTS2_1' 		=> '%N%% Kopftreffer',
        'HEADSHOTS2_2' 		=> '%N%% Kopftreffer',
        'HEADSHOTS2_3' 		=> '%N%% Kopftreffer',
		'ACCURACY'     		=> 'Scharfschütze',
        'ACCURACY_0'  		=> '%N%% Präzision',
        'ACCURACY_1'  		=> '%N%% Präzision',
        'ACCURACY_2'  		=> '%N%% Präzision',
        'ACCURACY_3'  		=> '%N%% Präzision',
		'ACCURACY2'   		=> 'Scharfschütze ',
        'ACCURACY2_0' 		=> '%N%% Präzision',
        'ACCURACY2_1' 		=> '%N%% Präzision',
        'ACCURACY2_2' 		=> '%N%% Präzision',
        'ACCURACY2_3' 		=> '%N%% Präzision',
		
        'TEAMSCORE'      	=> 'Teamplayer Nr.1',
        'TEAMSCORE_0'    	=> '%N% Pkt',
        'TEAMSCORE_1'    	=> '%N% Pkt',
        'TEAMSCORE_2'     	=> '%N% Pkt',
        'TEAMSCORE_3'     	=> '%N% Pkt',
		'WINS'      		=> 'Veteran',
        'WINS_0'    		=> '%N% Siege',
        'WINS_1'    		=> '%N% Siege',
        'WINS_2'     		=> '%N% Siege',
        'WINS_3'     		=> '%N% Siege',
        'REPAIRS'         	=> 'Mechaniker',
        'REPAIRS_0'       	=> '%N% Pkt',
        'REPAIRS_1'       	=> '%N% Pkt',
        'REPAIRS_2'       	=> '%N% Pkt',
        'REPAIRS_3'       	=> '%N% Pkt',
        'HEALS'           	=> 'Chefarzt',
        'HEALS_0'         	=> '%N% Pkt',
        'HEALS_1'         	=> '%N% Pkt',
        'HEALS_2'         	=> '%N% Pkt',
        'HEALS_3'         	=> '%N% Pkt',
		'REVIVES'           => 'Lebensretter',
        'REVIVES_0'         => '%N% Reanim.',
        'REVIVES_1'         => '%N% Reanim.',
        'REVIVES_2'         => '%N% Reanim.',
        'REVIVES_3'         => '%N% Reanim.',
		'AMMO_SUPPLY'     	=> 'Menschliches Magazin',
        'AMMO_SUPPLY_0'   	=> '%N% Pkt',
        'AMMO_SUPPLY_1'   	=> '%N% Pkt',
        'AMMO_SUPPLY_2'   	=> '%N% Pkt',
        'AMMO_SUPPLY_3'   	=> '%N% Pkt',
		'MOTION_SENSOR'   	=> 'Bewegungsmelder',
        'MOTION_SENSOR_0' 	=> '%N% Pkt',
        'MOTION_SENSOR_1' 	=> '%N% Pkt',
        'MOTION_SENSOR_2' 	=> '%N% Pkt',
        'MOTION_SENSOR_3'	=> '%N% Pkt',
		'CONQUEROR'   		=> 'Eroberer',
        'CONQUEROR_0' 		=> '%N% Flaggen',
        'CONQUEROR_1' 		=> '%N% Flaggen',
        'CONQUEROR_2' 		=> '%N% Flaggen',
        'CONQUEROR_3' 		=> '%N% Flaggen',
		'MCOMARM'   		=> 'M-COM Hacker',
        'MCOMARM_0' 		=> '%N% M-COMs',
        'MCOMARM_1' 		=> '%N% M-COMs',
        'MCOMARM_2' 		=> '%N% M-COMs',
        'MCOMARM_3' 		=> '%N% M-COMs',
		'TRACER_DART'   	=> 'Dart Weltmeister',
        'TRACER_DART_0' 	=> '%N% Pfeile',
        'TRACER_DART_1' 	=> '%N% Pfeile',
        'TRACER_DART_2' 	=> '%N% Pfeile',
        'TRACER_DART_3' 	=> '%N% Pfeile',
		'SPOT_ASSISTS'   	=> 'Wachsames Auge',
        'SPOT_ASSISTS_0' 	=> '%N% Sichtungen',
        'SPOT_ASSISTS_1' 	=> '%N% Sichtungen',
        'SPOT_ASSISTS_2' 	=> '%N% Sichtungen',
        'SPOT_ASSISTS_3' 	=> '%N% Sichtungen',
		'SAVIOR_KILLS'   	=> 'Schutzengel',
        'SAVIOR_KILLS_0' 	=> '%N% Retter-Kills',
        'SAVIOR_KILLS_1' 	=> '%N% Retter-Kills',
        'SAVIOR_KILLS_2' 	=> '%N% Retter-Kills',
        'SAVIOR_KILLS_3' 	=> '%N% Retter-Kills',
		
		'MELEEKILLS'    	=> 'Samurai',
        'MELEEKILLS_0'  	=> '%N% Kills',
        'MELEEKILLS_1'  	=> '%N% Kills',
        'MELEEKILLS_2'  	=> '%N% Kills',
        'MELEEKILLS_3'  	=> '%N% Kills',
		'GRENADE'   		=> 'Grenadier',
        'GRENADE_0' 		=> '%N% Kills',
        'GRENADE_1' 		=> '%N% Kills',
        'GRENADE_2' 		=> '%N% Kills',
        'GRENADE_3' 		=> '%N% Kills',
		'CLAYMORE'     		=> 'Stolperdraht',
        'CLAYMORE_0'   		=> '%N% Kills',
        'CLAYMORE_1'   		=> '%N% Kills',
        'CLAYMORE_2'   		=> '%N% Kills',
        'CLAYMORE_3'   		=> '%N% Kills',
		'MORTAR'   			=> 'Mobile Artillerie',
        'MORTAR_0' 			=> '%N% Kills',
        'MORTAR_1' 			=> '%N% Kills',
        'MORTAR_2' 			=> '%N% Kills',
        'MORTAR_3' 			=> '%N% Kills',
		'LIGHTNING'   		=> 'Elektroschocker',
        'LIGHTNING_0' 		=> '%N% Kills',
        'LIGHTNING_1' 		=> '%N% Kills',
        'LIGHTNING_2' 		=> '%N% Kills',
        'LIGHTNING_3' 		=> '%N% Kills',
		'RPG'         		=> 'Menschliche Rakete',
        'RPG_0'       		=> '%N% Kills',
        'RPG_1'       		=> '%N% Kills',
        'RPG_2'       		=> '%N% Kills',
        'RPG_3'       		=> '%N% Kills',
		'MINE'   			=> 'Landmine',
        'MINE_0' 			=> '%N% Kills',
        'MINE_1' 			=> '%N% Kills',
        'MINE_2' 			=> '%N% Kills',
        'MINE_3' 			=> '%N% Kills',
		'AIRBURST'   		=> 'Brennende Luft',
        'AIRBURST_0' 		=> '%N% Kills',
        'AIRBURST_1' 		=> '%N% Kills',
        'AIRBURST_2' 		=> '%N% Kills',
        'AIRBURST_3' 		=> '%N% Kills',
		'C4'     			=> 'Sprengstoffexperte',
        'C4_0'   			=> '%N% Kills',
        'C4_1'   			=> '%N% Kills',
        'C4_2'   			=> '%N% Kills',
        'C4_3'   			=> '%N% Kills',
		
		'HUMVEE_KILLS'   	=> 'Pimp my Humvee',
        'HUMVEE_KILLS_0' 	=> '%N% Kills',
        'HUMVEE_KILLS_1' 	=> '%N% Kills',
        'HUMVEE_KILLS_2' 	=> '%N% Kills',
        'HUMVEE_KILLS_3' 	=> '%N% Kills',
		'TANK_KILLS'   		=> 'Panzergeneral',
        'TANK_KILLS_0' 		=> '%N% Kills',
        'TANK_KILLS_1' 		=> '%N% Kills',
        'TANK_KILLS_2' 		=> '%N% Kills',
        'TANK_KILLS_3' 		=> '%N% Kills',
		'APC_KILLS'   		=> 'Truppenführer',
        'APC_KILLS_0' 		=> '%N% Kills',
        'APC_KILLS_1' 		=> '%N% Kills',
        'APC_KILLS_2' 		=> '%N% Kills',
        'APC_KILLS_3' 		=> '%N% Kills',
		'APACHE_KILLS'   	=> 'Heli Bob!',
        'APACHE_KILLS_0' 	=> '%N% Kills',
        'APACHE_KILLS_1' 	=> '%N% Kills',
        'APACHE_KILLS_2' 	=> '%N% Kills',
        'APACHE_KILLS_3' 	=> '%N% Kills',
		'LITTLE_BIRD_KILLS'   	=> 'König der kleinen Vögel',
        'LITTLE_BIRD_KILLS_0' 	=> '%N% Kills',
        'LITTLE_BIRD_KILLS_1' 	=> '%N% Kills',
        'LITTLE_BIRD_KILLS_2' 	=> '%N% Kills',
        'LITTLE_BIRD_KILLS_3' 	=> '%N% Kills',
		'BLACK_HAWK_KILLS'   	=> 'Walkürenritt',
        'BLACK_HAWK_KILLS_0' 	=> '%N% Kills',
        'BLACK_HAWK_KILLS_1' 	=> '%N% Kills',
        'BLACK_HAWK_KILLS_2' 	=> '%N% Kills',
        'BLACK_HAWK_KILLS_3' 	=> '%N% Kills',
		'JET_KILLS'   		=> 'Top Gun',
        'JET_KILLS_0' 		=> '%N% Kills',
        'JET_KILLS_1' 		=> '%N% Kills',
        'JET_KILLS_2' 		=> '%N% Kills',
        'JET_KILLS_3' 		=> '%N% Kills',
		'BOAT_KILLS'   		=> 'Bootsführer',
        'BOAT_KILLS_0' 		=> '%N% Kills',
        'BOAT_KILLS_1' 		=> '%N% Kills',
        'BOAT_KILLS_2' 		=> '%N% Kills',
        'BOAT_KILLS_3' 		=> '%N% Kills',
		
		'SUBMISSIONS'   	=> 'Geisterjäger',
        'SUBMISSIONS_0' 	=> '%N% Submissions',
        'SUBMISSIONS_1' 	=> '%N% Submissions',
        'SUBMISSIONS_2' 	=> '%N% Submissions',
        'SUBMISSIONS_3' 	=> '%N% Submissions',
		'BANS'    			=> 'Sherlock',
        'BANS_0'  			=> '%N%% gebannt',
        'BANS_1'  			=> '%N%% gebannt',
        'BANS_2'  			=> '%N%% gebannt',
        'BANS_3'  			=> '%N%% gebannt'
      ]
    ],
    'en' =>
    [
      'CATEGORY' =>
      [
        'SOLDIER'     => 'Soldier Awards',
        'TEAM'        => 'Team Awards',
		'WEAPON'      => 'Weapon Awards',
		'VEHICLE'	  => 'Vehicle Awards',
		'T4G' 		  => 'T4G Awards'
      ],
      'AWARD' =>
      [
	    'LEVEL30'   		=> 'General',
        'LEVEL30_0' 		=> 'Level %N%',
        'LEVEL30_1' 		=> 'Level %N%',
        'LEVEL30_2' 		=> 'Level %N%',
        'LEVEL30_3' 		=> 'Level %N%',
        'GAMES_PLAYED'   	=> 'Here to Stay!',
        'GAMES_PLAYED_0' 	=> '%N% Games',
        'GAMES_PLAYED_1' 	=> '%N% Games',
        'GAMES_PLAYED_2' 	=> '%N% Games',
        'GAMES_PLAYED_3' 	=> '%N% Games',
		'BESTSCORE'      	=> 'High Scorer',
        'BESTSCORE_0'    	=> '%N% Pts',
        'BESTSCORE_1'    	=> '%N% Pts',
        'BESTSCORE_2'    	=> '%N% Pts',
        'BESTSCORE_3'    	=> '%N% Pts',
		'SPM'   			=> 'Busy Bee',
        'SPM_0' 			=> '%N% Pts',
        'SPM_1' 			=> '%N% Pts',
        'SPM_2' 			=> '%N% Pts',
        'SPM_3' 			=> '%N% Pts',
        'KILLSTREAK'     	=> 'Serial Killer',
        'KILLSTREAK_0'   	=> '%N% Killstreak',
        'KILLSTREAK_1'   	=> '%N% Killstreak',
        'KILLSTREAK_2'   	=> '%N% Killstreak',
        'KILLSTREAK_3'   	=> '%N% Killstreak',
        'DEATHSTREAK'    	=> 'King of Targets',
        'DEATHSTREAK_0'  	=> '%N% Deathstreak',
        'DEATHSTREAK_1'  	=> '%N% Deathstreak',
        'DEATHSTREAK_2'  	=> '%N% Deathstreak',
        'DEATHSTREAK_3'  	=> '%N% Deathstreak',
		'EAGLE_EYE'    		=> 'Eagle Eye',
        'EAGLE_EYE_0'  		=> '%N% Pts',
        'EAGLE_EYE_1'  		=> '%N% Pts',
        'EAGLE_EYE_2'  		=> '%N% Pts',
        'EAGLE_EYE_3'  		=> '%N% Pts',
		'ROADKILLS'   		=> 'Hood ornament',
        'ROADKILLS_0' 		=> '%N% Roadkills',
        'ROADKILLS_1' 		=> '%N% Roadkills',
        'ROADKILLS_2' 		=> '%N% Roadkills',
        'ROADKILLS_3' 		=> '%N% Roadkills',
		'HEADSHOTS'     	=> 'Head Hunter',
        'HEADSHOTS_0'  		=> '%N%% Headshots',
        'HEADSHOTS_1'  		=> '%N%% Headshots',
        'HEADSHOTS_2'  		=> '%N%% Headshots',
        'HEADSHOTS_3'  		=> '%N%% Headshots',
		'HEADSHOTS2'   		=> 'Head Hunter ',
        'HEADSHOTS2_0' 		=> '%N%% Headshots',
        'HEADSHOTS2_1' 		=> '%N%% Headshots',
        'HEADSHOTS2_2' 		=> '%N%% Headshots',
        'HEADSHOTS2_3' 		=> '%N%% Headshots',
		'ACCURACY'     		=> 'Sharpshooter',
        'ACCURACY_0'  		=> '%N%% Accuracy',
        'ACCURACY_1'  		=> '%N%% Accuracy',
        'ACCURACY_2'  		=> '%N%% Accuracy',
        'ACCURACY_3'  		=> '%N%% Accuracy',
		'ACCURACY2'   		=> 'Sharpshooter ',
        'ACCURACY2_0' 		=> '%N%% Accuracy',
        'ACCURACY2_1' 		=> '%N%% Accuracy',
        'ACCURACY2_2' 		=> '%N%% Accuracy',
        'ACCURACY2_3' 		=> '%N%% Accuracy',
		
        'TEAMSCORE'      	=> 'Teamplayer No.1',
        'TEAMSCORE_0'    	=> '%N% Pts',
        'TEAMSCORE_1'    	=> '%N% Pts',
        'TEAMSCORE_2'    	=> '%N% Pts',
        'TEAMSCORE_3'    	=> '%N% Pts',
		'WINS'      		=> 'Veteran',
        'WINS_0'    		=> '%N% Wins',
        'WINS_1'    		=> '%N% Wins',
        'WINS_2'     		=> '%N% Wins',
        'WINS_3'     		=> '%N% Wins',
        'REPAIRS'        	=> 'Mechanic',
        'REPAIRS_0'      	=> '%N% Pts',
        'REPAIRS_1'      	=> '%N% Pts',
        'REPAIRS_2'      	=> '%N% Pts',
        'REPAIRS_3'      	=> '%N% Pts',
        'HEALS'          	=> 'Head Doctor',
        'HEALS_0'        	=> '%N% Pts',
        'HEALS_1'        	=> '%N% Pts',
        'HEALS_2'        	=> '%N% Pts',
        'HEALS_3'        	=> '%N% Pts',
		'REVIVES'           => 'Rescuer',
        'REVIVES_0'         => '%N% Revives',
        'REVIVES_1'         => '%N% Revives',
        'REVIVES_2'         => '%N% Revives',
        'REVIVES_3'         => '%N% Revives',
		'AMMO_SUPPLY'    	=> 'Human Magazine',
        'AMMO_SUPPLY_0'  	=> '%N% Pts',
        'AMMO_SUPPLY_1'  	=> '%N% Pts',
        'AMMO_SUPPLY_2'  	=> '%N% Pts',
        'AMMO_SUPPLY_3'  	=> '%N% Pts',
		'MOTION_SENSOR'  	=> 'Passive Infrared Detector',
        'MOTION_SENSOR_0'	=> '%N% Pts',
        'MOTION_SENSOR_1'	=> '%N% Pts',
        'MOTION_SENSOR_2'	=> '%N% Pts',
        'MOTION_SENSOR_3'	=> '%N% Pts',
		'CONQUEROR'   		=> 'Conqueror',
        'CONQUEROR_0' 		=> '%N% Flags',
        'CONQUEROR_1' 		=> '%N% Flags',
        'CONQUEROR_2' 		=> '%N% Flags',
        'CONQUEROR_3' 		=> '%N% Flags',
		'MCOMARM'   		=> 'M-COM Hacker',
        'MCOMARM_0' 		=> '%N% M-COMs',
        'MCOMARM_1' 		=> '%N% M-COMs',
        'MCOMARM_2' 		=> '%N% M-COMs',
        'MCOMARM_3' 		=> '%N% M-COMs',
		'TRACER_DART'   	=> 'Dart World Champion',
        'TRACER_DART_0' 	=> '%N% Darts',
        'TRACER_DART_1' 	=> '%N% Darts',
        'TRACER_DART_2' 	=> '%N% Darts',
        'TRACER_DART_3' 	=> '%N% Darts',
		'SPOT_ASSISTS'   	=> 'Watchful Eye',
        'SPOT_ASSISTS_0' 	=> '%N% Spots',
        'SPOT_ASSISTS_1' 	=> '%N% Spots',
        'SPOT_ASSISTS_2' 	=> '%N% Spots',
        'SPOT_ASSISTS_3' 	=> '%N% Spots',
		'SAVIOR_KILLS'   	=> 'Guardian Angel',
        'SAVIOR_KILLS_0' 	=> '%N% Savior kills',
        'SAVIOR_KILLS_1' 	=> '%N% Savior kills',
        'SAVIOR_KILLS_2' 	=> '%N% Savior kills',
        'SAVIOR_KILLS_3' 	=> '%N% Savior kills',
		
		'MELEEKILLS'    	=> 'Samurai',
        'MELEEKILLS_0'  	=> '%N% Kills',
        'MELEEKILLS_1'  	=> '%N% Kills',
        'MELEEKILLS_2'  	=> '%N% Kills',
        'MELEEKILLS_3'  	=> '%N% Kills',
		'GRENADE'   		=> 'Grenadier',
        'GRENADE_0' 		=> '%N% Kills',
        'GRENADE_1' 		=> '%N% Kills',
        'GRENADE_2' 		=> '%N% Kills',
        'GRENADE_3' 		=> '%N% Kills',
		'CLAYMORE'     		=> 'Tripwire',
        'CLAYMORE_0'   		=> '%N% Kills',
        'CLAYMORE_1'   		=> '%N% Kills',
        'CLAYMORE_2'   		=> '%N% Kills',
        'CLAYMORE_3'   		=> '%N% Kills',
		'MORTAR'   			=> 'Mobile Artillery',
        'MORTAR_0' 			=> '%N% Kills',
        'MORTAR_1' 			=> '%N% Kills',
        'MORTAR_2' 			=> '%N% Kills',
        'MORTAR_3' 			=> '%N% Kills',
		'LIGHTNING'   		=> 'Stun Gun',
        'LIGHTNING_0' 		=> '%N% Kills',
        'LIGHTNING_1' 		=> '%N% Kills',
        'LIGHTNING_2' 		=> '%N% Kills',
        'LIGHTNING_3' 		=> '%N% Kills',
		'RPG'         		=> 'Human Rocket',
        'RPG_0'       		=> '%N% Kills',
        'RPG_1'       		=> '%N% Kills',
        'RPG_2'       		=> '%N% Kills',
        'RPG_3'       		=> '%N% Kills',
		'MINE'   			=> 'Landmine',
        'MINE_0' 			=> '%N% Kills',
        'MINE_1' 			=> '%N% Kills',
        'MINE_2' 			=> '%N% Kills',
        'MINE_3' 			=> '%N% Kills',
		'AIRBURST'   		=> 'Bursting Air',
        'AIRBURST_0' 		=> '%N% Kills',
        'AIRBURST_1' 		=> '%N% Kills',
        'AIRBURST_2' 		=> '%N% Kills',
        'AIRBURST_3' 		=> '%N% Kills',
		'C4'     			=> 'Explosives Expert',
        'C4_0'   			=> '%N% Kills',
        'C4_1'   			=> '%N% Kills',
        'C4_2'   			=> '%N% Kills',
        'C4_3'   			=> '%N% Kills',
		
		'HUMVEE_KILLS'   	=> 'Pimp my humvee',
        'HUMVEE_KILLS_0' 	=> '%N% Kills',
        'HUMVEE_KILLS_1' 	=> '%N% Kills',
        'HUMVEE_KILLS_2' 	=> '%N% Kills',
        'HUMVEE_KILLS_3' 	=> '%N% Kills',
		'TANK_KILLS'   		=> 'Tank Commander',
        'TANK_KILLS_0' 		=> '%N% Kills',
        'TANK_KILLS_1' 		=> '%N% Kills',
        'TANK_KILLS_2' 		=> '%N% Kills',
        'TANK_KILLS_3' 		=> '%N% Kills',
		'APC_KILLS'   		=> 'APC Commander',
        'APC_KILLS_0' 		=> '%N% Kills',
        'APC_KILLS_1' 		=> '%N% Kills',
        'APC_KILLS_2' 		=> '%N% Kills',
        'APC_KILLS_3' 		=> '%N% Kills',
		'APACHE_KILLS'   	=> 'Heli Bob!',
        'APACHE_KILLS_0' 	=> '%N% Kills',
        'APACHE_KILLS_1' 	=> '%N% Kills',
        'APACHE_KILLS_2' 	=> '%N% Kills',
        'APACHE_KILLS_3' 	=> '%N% Kills',
		'LITTLE_BIRD_KILLS'   	=> 'King of little birds',
        'LITTLE_BIRD_KILLS_0' 	=> '%N% Kills',
        'LITTLE_BIRD_KILLS_1' 	=> '%N% Kills',
        'LITTLE_BIRD_KILLS_2' 	=> '%N% Kills',
        'LITTLE_BIRD_KILLS_3' 	=> '%N% Kills',
		'BLACK_HAWK_KILLS'   	=> 'Ride of the Valkyries',
        'BLACK_HAWK_KILLS_0' 	=> '%N% Kills',
        'BLACK_HAWK_KILLS_1' 	=> '%N% Kills',
        'BLACK_HAWK_KILLS_2' 	=> '%N% Kills',
        'BLACK_HAWK_KILLS_3' 	=> '%N% Kills',
		'JET_KILLS'   		=> 'Top Gun',
        'JET_KILLS_0' 		=> '%N% Kills',
        'JET_KILLS_1' 		=> '%N% Kills',
        'JET_KILLS_2' 		=> '%N% Kills',
        'JET_KILLS_3' 		=> '%N% Kills',
		'BOAT_KILLS'   		=> 'Boatman',
        'BOAT_KILLS_0' 		=> '%N% Kills',
        'BOAT_KILLS_1' 		=> '%N% Kills',
        'BOAT_KILLS_2' 		=> '%N% Kills',
        'BOAT_KILLS_3' 		=> '%N% Kills',
		
		'SUBMISSIONS'    	=> 'Ghosthunter',
        'SUBMISSIONS_0'  	=> '%N% Submissions',
        'SUBMISSIONS_1'  	=> '%N% Submissions',
        'SUBMISSIONS_2'  	=> '%N% Submissions',
        'SUBMISSIONS_3'  	=> '%N% Submissions',
		'BANS'    			=> 'Sherlock',
        'BANS_0'  			=> '%N%% banned',
        'BANS_1'  			=> '%N%% banned',
        'BANS_2'  			=> '%N%% banned',
        'BANS_3'  			=> '%N%% banned'
      ]
    ]
  ];

  
  public static function getTranslation($language, $key, $type)
  {
    if(!array_key_exists($language, self::$strings))
    {
      $language = 'en';
    }
    if(array_key_exists($key, self::$strings[$language][$type]))
    {
      return self::$strings[$language][$type][$key];
    }
      
    return 'UNDEFINED';
  }
}

class Awards
{
  protected static $_awards = 
  [
    'SOLDIER' =>
    [
	  'LEVEL30' =>
      [
        'profile_soldiers',
        'LEVEL30',
        'level',
        [
          21,
          24,
          27,
          30
        ],
		true
      ],
      'GAMES_PLAYED' =>
      [
        'profile_stats',
        'GAMES_PLAYED',
        'games',
        [
          100,
          500,
          1000,
          5000
        ],
		true
      ],
      'BESTSCORE' =>
      [
        'profile_stats',
        'BESTSCORE',
        'bestScore',
        [
          10000,
          15000,
          20000,
          25000
        ],
		true
      ],
	  'SPM' =>
      [
        'profile_stats',
        'PER_MINUTE',
        'spm',
        [
          250,
          400,
          550,
          700
        ],
		true
      ],
      'KILLSTREAK' =>
      [
        'profile_stats',
        'KILLSTREAK',
        'killstreak',
        [
          20,
          40,
          60,
          80
        ],
		true
      ],
      'DEATHSTREAK' =>
      [
        'profile_stats',
        'DEATHSTREAK',
        'deathstreak',
        [
          5,
          10,
          20,
          30
        ],
		true
      ],
	  'EAGLE_EYE' =>
      [
        'profile_stats',
        'EAGLE_EYE',
        'bestRangedKill',
        [
          100,
          200,
          300,
          400
        ],
		true
      ],
	  'ROADKILLS' =>
      [
        'profile_stats',
        'ROADKILLS',
        'roadKills',
        [
          50,
          100,
          250,
          500
        ],
		true
      ],
	  'HEADSHOTS' =>
      [
        'profile_stats',
        'HEAD',
        'headshotratio',
        [
          19,
          22,
          25,
          28
        ],
		true,
		'medic,assault,engineer'
      ],
	  'HEADSHOTS2' =>
      [
        'profile_stats',
        'HEAD',
        'headshotratio',
        [
          40,
          50,
          60,
          70
        ],
		true,
		'recon'
      ],
	  'ACCURACY' =>
      [
        'profile_stats',
        'ACCURACY',
        'accuracy',
        [
          24,
          27,
          30,
          33
        ],
		true,
		'medic,assault,engineer'
      ],
	  'ACCURACY2' =>
      [
        'profile_stats',
        'ACCURACY',
        'accuracy',
        [
          30,
          35,
          45,
          55
        ],
		true,
		'recon'
      ]
    ],
    'TEAM' =>
    [
      'TEAMSCORE' =>
      [
        'profile_stats',
        'TEAMSCORE',
        'teamScore',
        [
          200000,
          500000,
          1000000,
          2000000
        ],
		true
      ],
	  'WINS' =>
      [
        'profile_stats',
        'WINS',
        'wins',
        [
          200,
          500,
          1000,
          2000
        ],
		true
      ],
      'REPAIRS' =>
      [
        'profile_gameEventStats',
        'REPAIRS',
        'count',
        [
          2000,
          5000,
          10000,
          20000
        ],
		true,
		'engineer',
        ' AND eventId = 35'
      ],
      'HEALS' =>
      [
        'profile_gameEventStats',
        'HEALS',
        'count',
        [
          5000,
          10000,
          20000,
          50000
        ],
		true,
		'medic',
        ' AND eventId = 42'
      ],
	  'REVIVES' =>
      [
        'profile_gameEventStats',
        'REVIVE',
        'count',
        [
          500,
          1000,
          2000,
          5000
        ],
		true,
		'medic',
        ' AND eventId = 33'
      ],
	  'AMMO_SUPPLY' =>
      [
        'profile_gameEventStats',
        'AMMO_SUPPLY',
        'count',
        [
          2000,
          5000,
          10000,
          20000
        ],
		true,
		'assault',
        ' AND eventId = 41'
      ],
	  'MOTION_SENSOR' =>
      [
        'profile_gameEventStats',
        'MOTION_SENSOR',
        'count',
        [
          2000,
          5000,
          10000,
          20000
        ],
		true,
		'recon',
        ' AND eventId = 52'
      ],
	  'CONQUEROR' =>
      [
        'profile_gameEventStats',
        'CONQUEROR',
        'count',
        [
          500,
          1000,
          2000,
          5000
        ],
		true,
		'',
        ' AND eventId = 29'
      ],
	  'MCOMARM' =>
      [
        'profile_rushMapStats',
        'MCOM',
        'mcomarm',
        [
          200,
          500,
          1000,
          2000
        ],
		true,
		'',
        ' AND mapId = 0'
      ],
	  'TRACER_DART' =>
      [
        'profile_gameEventStats',
        'TRACER_DART',
        'count',
        [
          500,
          1000,
          2000,
          5000
        ],
		true,
		'',
        ' AND eventId = 45'
      ],
	  'SPOT_ASSISTS' =>
      [
        'profile_gameEventStats',
        'SPOTS',
        'count',
        [
          250,
          500,
          1000,
          2000
        ],
		true,
		'',
        ' AND eventId = 49'
      ],
	  'SAVIOR_KILLS' =>
      [
        'profile_gameEventStats',
        'ANGEL',
        'count',
        [
          500,
          1000,
          2000,
          5000
        ],
		true,
		'',
        ' AND eventId = 34'
      ]
	],
	'WEAPON' =>
    [
	  'MELEEKILLS' =>
      [
        'profile_weaponStats',
        'MELEEKILLS',
        'kills',
        [
          100,
          200,
          500,
          1000
        ],
		true,
		'',
        ' AND weaponId = 3027'
      ],
	  'GRENADE' =>
      [
        'profile_weaponStats',
        'GRENADE',
        'kills',
        [
          100,
          200,
          500,
          1000
        ],
		true,
		'',
        ' AND weaponId = 2005'
      ],
	  'RPG' =>
      [
        'profile_weaponStats',
        'RPG',
        'kills',
        [
          500,
          1000,
          2000,
          4000
        ],
		true,
		'engineer',
        ' AND weaponId = 2054'
      ],
	  'MINE' =>
      [
        'profile_weaponStats',
        'MINE',
        'kills',
        [
          200,
          500,
          1000,
          2000
        ],
		true,
		'engineer',
        ' AND weaponId = 2046'
      ],
	  'AIRBURST' =>
      [
        'profile_weaponStats',
        'AIRBURST',
        'kills',
        [
          100,
          200,
          500,
          1000
        ],
		true,
		'engineer',
        ' AND weaponId = 2051'
      ],
	  'CLAYMORE' =>
      [
        'profile_weaponStats',
        'CLAYMORE',
        'kills',
        [
          100,
          200,
          500,
          1000
        ],
		true,
		'recon',
        ' AND weaponId = 2048'
      ],
	  'MORTAR' =>
      [
        'profile_weaponStats',
        'MORTAR',
        'kills',
        [
          200,
          500,
          1000,
          2000
        ],
		true,
		'recon',
        ' AND weaponId = 2025'
      ],
	  'LIGHTNING' =>
      [
        'profile_weaponStats',
        'LIGHTNING',
        'kills',
        [
          100,
          200,
          500,
          1000
        ],
		true,
		'medic',
        ' AND weaponId = 2030'
      ],
	  'C4' =>
      [
        'profile_weaponStats',
        'C4',
        'kills',
        [
          200,
          500,
          1000,
          2000
        ],
		true,
		'assault',
        ' AND weaponId = 2034'
      ]
	],
	'VEHICLE' =>
    [
	  'HUMVEE_KILLS' =>
      [
        'profile_vehicleStats',
        'HUMVEE',
        'kills',
        [
          100,
          200,
          500,
          1000
        ],
		true,
		'',
        ' AND vehicleId = 1'
      ],
	  'TANK_KILLS' =>
      [
        'profile_vehicleStats',
        'TANK',
        'kills',
        [
          100,
          500,
          1000,
          2000
        ],
		true,
		'',
        ' AND vehicleId = 2'
      ],
	  'APC_KILLS' =>
      [
        'profile_vehicleStats',
        'APC',
        'kills',
        [
          100,
          500,
          1000,
          2000
        ],
		true,
		'',
        ' AND vehicleId = 3'
      ],
	  'APACHE_KILLS' =>
      [
        'profile_vehicleStats',
        'APACHE',
        'kills',
        [
          200,
          1000,
          2000,
          5000
        ],
		true,
		'',
        ' AND vehicleId = 5'
      ],
	  'LITTLE_BIRD_KILLS' =>
      [
        'profile_vehicleStats',
        'LITTLE_BIRD',
        'kills',
        [
          100,
          500,
          1000,
          2000
        ],
		true,
		'',
        ' AND vehicleId = 6'
      ],
	  'BLACK_HAWK_KILLS' =>
      [
        'profile_vehicleStats',
        'BLACK_HAWK',
        'kills',
        [
          100,
          200,
          500,
          1000
        ],
		true,
		'',
        ' AND vehicleId = 7'
      ],
	  'JET_KILLS' =>
      [
        'profile_vehicleStats',
        'JET',
        'kills',
        [
          100,
          500,
          1000,
          2000
        ],
		true,
		'',
        ' AND vehicleId = 4'
      ],
	  'BOAT_KILLS' =>
      [
        'profile_vehicleStats',
        'BOAT',
        'kills',
        [
          10,
          20,
          30,
          50
        ],
		true,
		'',
        ' AND vehicleId = 8'
      ]
	],
	'T4G' =>
    [
	  'SUBMISSIONS' =>
      [
        'submissions',
        'SUBMISSIONS',
        'count',
        [
          25,
          50,
          100,
          500
        ],
		true
      ],
	  'BANS' =>
      [
        'submissions',
        'SUBMISSIONS',
        'count',
        [
          50,
          60,
          70,
          90
        ],
		true
      ]
    ]
  ];
  
  public static function getForSoldierId($lang, $nucleusId, $soldierId)
  {
    $awards = [];
    
    foreach(self::$_awards as $category => $array)
    {
      $cat = AwardTranslate::getTranslation($lang, $category, 'CATEGORY');

      foreach($array as $root => $data)
      {
        $rootLabel = AwardTranslate::getTranslation($lang, $root, 'AWARD');
        $id = $root;
        $table = $data[0];
        $image = $data[1];
        $key = $data[2];
        $steps = $data[3];
		$show = $data[4];
		$classRestriction = (isset($data[5]) ? $data[5] : '');
        $where = (isset($data[6]) ? $data[6] : '');
        $order = (isset($data[7]) ? $data[7] : 'date DESC');
		
		if($id == 'SUBMISSIONS') {
			$stats = alxDatabaseManager::query
			("SELECT COUNT(*) AS {$key} FROM {$table} WHERE sourceNucleusId = '{$nucleusId}'")->fetch();
		} elseif($id == 'BANS') {
			$subs = alxDatabaseManager::query
			("SELECT COUNT(*) AS {$key} FROM {$table} WHERE sourceNucleusId = '{$nucleusId}' AND done = '1'")->fetch();
			
			$bans = alxDatabaseManager::query
			("SELECT COUNT(*) AS {$key} FROM {$table} s INNER JOIN bans b ON b.nucleusId = s.targetNucleusId WHERE s.sourceNucleusId = '{$nucleusId}' AND s.done = '1' AND b.active = '1'")->fetch();
			
			$stats->{$key} = ($subs->{$key} ? round(100 / $subs->{$key} * $bans->{$key}) : 0);
			if($stats->{$key} > 100)
				$stats->{$key} = 100;
		} else {
			$stats = alxDatabaseManager::query
			("SELECT {$key} FROM {$table} WHERE soldierId = '{$soldierId}' {$where} ORDER BY {$order} LIMIT 1")->fetch();
			
			if($id == 'HEADSHOTS' || $id == 'HEADSHOTS2' || $id == 'ACCURACY' || $id == 'ACCURACY2')
				$stats->{$key} *= 100;
		}
		
        $awards[$cat][$rootLabel] = 
        [
          'id' => $id,
          'image' => $image,
          'data' => [],
		  'stats' => (float) @round($stats->{$key}),
          'classRestriction' => $classRestriction,
		  'show' => $show
        ];
      
        foreach($steps as $i => $award)
        {
          $label = AwardTranslate::getTranslation($lang, "{$id}_{$i}", 'AWARD');
		  $check = ((float) @$stats->{$key} >= $award ? true : false);
		  $awards[$cat][$rootLabel]['data'][] = [$check, $label, $award];
        }
      } 
    }

    return $awards;
  }
}
