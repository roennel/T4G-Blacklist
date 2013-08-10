<?php

# Used by alxApplication-Dependant Classes
const ALX_APP = true;

# Defines the Variable Name in which alxApplication will be initialized
const ALX_APP_VAR = 'app';

# Search Pattern used on searching config files
const ALX_APP_CONFIG_SEARCH_PATTERN = '.conf.php';

# Debug Mode
const ALX_APP_DEBUG = false;

/**
 * alxApplication
 * Application Extension for alxToolkit
 *
 * @author 		Ronny Gysin <roennel@alchemical.cc>
 * @version		0.9
 */
class alxApplication
{
	/**
	 * Stages Container
	 * @access 	private
	 * @var 		array
	 */
	private $_stages = array();

	/**
	 * System Log
	 * @access 	public
	 * @var 		alxSystemLog
	 */
	public static $systemLog;
	
	/**
	 * Config Collection
	 * @access 	public
	 * @var 		alxConfigCollection
	 */
	public static $configCollection;
	
	/**
	 * Module Collection
	 * @access 	public
	 * @var 		alxModuleCollection
	 */
	public static $moduleCollection;
	
	/**
	 * Event Collection
	 * @access 	public
	 * @var 		alxEventCollection
	 */
	public static $eventCollection;
	
	/**
	 * File Collection
	 * @access 	public
	 * @var 		alxFileCollection
	 */
	public static $fileCollection;
	
	/**
   * Constructor, Initializes Sub Classes
   * @return 	void
   */
	function __construct()
	{
		// Initialize System Log
		self::$systemLog = new alxSystemLog;
		self::$systemLog->addLog('Application StartUp');

		// Initialize Application Collections
		self::$configCollection = new alxApplicationConfigCollection;
		self::$moduleCollection = new alxModuleCollection;
		self::$eventCollection 	= new alxEventCollection;
		self::$fileCollection 	= new alxFileCollection; 
	}
	
	/**
   * Searches for Configs in User & Application Stage
   * @return 	void
   */
	public function searchConfigs()
	{
		# Define Available Config Paths
		$paths = array
		(
			alx::getToolkitPath() . 'alxApplication/configs',
			ALX_APP_PATH . '/cfg'
		);
		
		foreach($paths as $path)
		{
			foreach(array_filter(scandir($path), function($v) 
			{ 
				return strpos($v, ALX_APP_CONFIG_SEARCH_PATTERN) !== false; 
			}) as $file)
			{
				# Load Config
				include_once $path . '/' . $file;
				
				# Enable Write Protection for Configs
				$config->protect();
				
				# Add Config to Collection
				self::$configCollection->addConfig($config);
			}
		}
	}
	
	/**
   * Applies a user-defined Config, or a Host-Based Fallback.
	 * @param 	array $options
	 * @param 	array $sort
   * @return 	boolean
   */
	public function applyConfig($configId=null)
	{
		$configId = $configId ?: self::$configCollection->getConfigByHost($_SERVER['HTTP_HOST'])->getId();

		self::$configCollection->setActiveConfig($configId);
		
		return $configId;
	}
	
	/**
   * Shortcut: Loads an Item from the Active Config
	 * @param 	string $key
	 * @param 	string $parent
   * @return 	mixed
   */
	public static function getConfigVar($key, $parent=null)
	{
		$config = self::$configCollection->getActiveConfig();

		if($parent)
		{
			$item = $config->$parent->$key;
		}
		else
		{
			$item = $config->$key;
		}
		
		if(!empty($item))
		{
			return $item;
		}
		
		return false;
	}
	
	/**
   * Adds an ApplicationStage
	 * @param 	alxApplicationStage	$stage
   * @return 	void
   */
	public function addApplicationStage(alxApplicationStage $stage)
	{
		$this->_stages[] = $stage;
		
		return $stage->getId();
	}
	
	/**
   * Loads all Application Stages
   * @return 	void
   */
	public function loadApplicationStages()
	{
		$events = $this->_stages[0]->getEvents();
		
		for($i=0,$c=count($events);$i<$c;$i++)
		{
			foreach($this->_stages as $stage)
			{
				$eventId = $events[$i][0];
				
				self::$systemLog->addLog('Loading Stage Event', "{$stage->getId()}->{$eventId}");
				
				$stage->loadEventById($eventId);
			}
		}	
	}
	
	public function runApplicationStageEvents()
	{
		$events = $this->_stages[0]->getEvents();
		$delayed = array();
		
		for($i=0,$c=count($events);$i<$c;$i++)
		{
			foreach($this->_stages as $stage)
			{
				$eventId = $events[$i][0];
				
				$event = $stage->eventCollection->getEventById($eventId);
				
				if(@$events[$i][1])
				{
					$delayed[] = array($stage, $event);
				}
				else
				{
					$state = $event->runCallbacks();
					
					self::$systemLog->addLog('Running Stage Event', $stage->getId() .'->' . $event->getId() . ' : ' . $state);
				}
			}
		}	
	
		# Delayed
		foreach($delayed as $data)
		{
			$state = $data[1]->runCallbacks();
					
			self::$systemLog->addLog('Running Delayed Stage Event', $data[0]->getId() .'->' . $data[1]->getId() . ' : ' . $state);
		}
	}
}













/*

class alxApplication
{
  const GLOBALVIEW_TOP_COMMENT = '<!-- %Y &gt; alchemical.ch -->';
    
  // Configs
  protected $_customConfigs = array();
	protected $_defaultConfigs = array
  (
    'local' => array
    (
      '127.0.0.1',
      'localhost'
    )
  );
    
    // Containers
	protected static $_config;
	protected static $_modules = array();
	
	// Events
	protected $_events = array
	(
		'setHeaders' 				=> 'onHeadersInvoke',
		'invokeDatabase'		=> 'onDatabaseInvoke',
		'invokeSession'			=> 'onSessionInvoke',
		'invokeRequest'			=> 'onRequestInvoke',
		'invokeRouting'			=> 'onRoutingInvoke',
		'invokeModules'			=> 'onModulesInvoke',
		'invokeController'	=> 'onControllerInvoke',
		'invokeGlobalView'	=> 'onGlobalViewInvoke',
		'finalize'					=> 'onFinalize',
	);
	
	function __construct()
	{
		try
		{
			if(!$this->applyConfig()) 
				throw new alxApplicationException('Failed to apply Config');

			if(!$this->loadApplicationEvents()) 
				throw new alxApplicationException('Failed to load Application Events');
				
			if(!$this->runApplicationEvents()) 
				throw new alxApplicationException('Failed to run Application Events');
		}
		catch(alxApplicationException $e)
		{
			$e->handle();
		}
	}
	
	private function applyConfig()
	{
		$config = null;
    $configs = array_merge($this->_customConfigs, $this->_defaultConfigs);
    $currentHost = $_SERVER['HTTP_HOST'];

    foreach($configs as $id => $hosts)
    {
      foreach($hosts as $host)
      {
      	if($host == $currentHost) $config = $id;
      }
    }

    $config = "cfg/{$config}.conf.php";

		if(file_exists($config))
		{
			include_once $config;

			self::$_config = $config;
     
      define('CSS_PATH', 	$config->app->root . 'css/');
      define('JS_PATH', 	$config->app->root . 'js/');
      define('IMG_PATH', 	$config->app->root . 'img/');
		
			return true;
		}
		
		return false;
	}
	
	private function loadApplicationEvents()
	{
		alxEvents::initialize();
	
		$pathApp 	= alx::getToolkitPath() . "/alxApplication/events"; 
		$pathUser = $this->getConfigVar('path', 'app') . "events/application";
		
		foreach($this->_events as $eventAppId => $eventId)
		{
			$fileApp	= "{$pathApp}/{$eventId}.php";
			$fileUser = "{$pathUser}/{$eventId}.php";
		
			if(!file_exists($fileApp))
			{
				alxApplicationException::$lastMessage = $fileApp;
				return false;
			}
			
			if(!file_exists($fileUser))
			{
				alxApplicationException::$lastMessage = $fileUser;
			}
			
			$event = new alxEvent($eventId);
			
			include_once $fileApp;
			
			include_once $fileUser;
			
			alxEvents::registerEvent($event);
		}
		
		return true;
	}
	
	private function runApplicationEvents()
	{
		foreach(alxEvents::getEventCollection()->getEvents() as $eventId => $event)
		{
			$callback = $event->runCallbacks();
			
			if(!$callback)
			{
				alxApplicationException::$lastMessage = $event->getId();
				return false;
			}
		}
		
		return true;
	}

	public function run()
	{
    setlocale(LC_CTYPE, 'C');
		error_reporting(E_ALL ^ E_NOTICE);
	}
	
	public static function getConfigVar($var, $root=null)
	{
		if($root)
		{
			if(self::$_config->$root->$var)
			{
				return self::$_config->$root->$var;
			}
		}
		else
		{
			if(self::$_config->$var)
			{
				return self::$_config->$var;
			}
		}
		
		return false;
	}

	public static function getExternalPath()
	{
		$cfg = self::$_config->app;
	
		$path = $cfg->prot . '://' . $cfg->host;
		$path.= ($cfg->port != 80 ? ':' . $cfg->port : null);
		$path.= $cfg->root;
		
		return (string) $path;
	}
    
  public function addCustomConfig($id, array $hosts)
  {
    $this->_customConfigs[$id] = $hosts;
  }
	
	public function importSharedComponent($type, $file)
	{
		$file = ucFirst($file);
		$ext = 'php';
		
		switch($type)
		{
			case 'controller':
				$file = ucFirst($file);
				$path = "controllers/{$file}Controller";
			break;
			
			case 'view':
				$path = "views/{$file}";
			break;
			
			default:
				return false;
		}

		alx::loadShared($path, $ext);
	}
}

alx::load('Application', 'ApplicationAutoload');
alx::load('Application', 'ApplicationConfig');
alx::load('Application', 'ApplicationConfigCollection');
alx::load('Application/exceptions', 'ApplicationException');

*/