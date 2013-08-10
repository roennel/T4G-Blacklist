<?php

class alxRoutingManager
{
	public static $_config;
	
	protected static $routes = array();
	
	public static function addRoute(alxRoute $route)
	{
		self::$routes[] = $route;
		
		alxApplication::$systemLog->addLog('Added Route', $route);
		
		return true;
	}
	
	public static function addRoutes()
	{
		foreach(func_get_args() as $route)
		{
			self::addRoute($route);
		}
	}
	
	public static function getRoute($conditions)
	{
		$stage2 = array();
	
		foreach(self::$routes as $route)
		{
			$errors = 0;
			
			foreach($conditions as $key => $value)
			{
				if(!array_key_exists($key, $route->vars))
				{
					$errors++;
				}
			}
			
			if($errors == 0)
			{
				$stage2[] = $route;
			}
		}
		
		foreach($stage2 as $route)
		{
			if(count($route->vars) == count($conditions))
			{
				return $route;
			}
		}
	}
	
	public static function getRoutes()
	{
		return self::$routes;
	}
	
	public static function addRewriteRules(alxFile_HtAccess $htaccess)
	{
		foreach(self::getRoutes() as $route)
		{
			// Build Catch
			$catchVars = array_walk($route->vars, function(&$item)
			{
				$item = '([^/\.]+)';
			});
			
			$catch = '^' . $route->getComputedString($catchVars) . '/?$';
			
			// Build Path
			$i = 0;
			$pathVars = array_filter(function($key, $value) use($i)
			{
				return "{$key}=" . $i++;
			}, 
			array_keys($route->vars),
			array_values($route->vars));
			
			$path = 'application.php?' . implode('&', $pathVars);

			// Build Options
			$options = 'QSA, L';
			
			// Add Rule
			$htaccess->addRewriteRule($catch, $path, $options);
		}
	}
}

alx::load('Routing', 'Route');




/**
	* Shortcut Method for routed Links in Views
	* required conditions: 
	*	- controller
	* optional contitions:
	*	- action
	* - query
	*	- ...{user defined}
	*/
	
function alxLink($conditions, $value=null, $atr=null)
{
	$a = new alxHTMLBuilder('a');
	$a->set('href', alxLinkString($conditions));
	
	if($atr)
	{
		$a->setAttributes($atr);
	}
	
	if($value)
	{
		$a->setContent($value);
	}
	else
	{
		if(array_key_exists('action', $conditions))
		{
			$a->setContent($conditions['action']);
		}
		else
		{
			$a->setContent($conditions['controller']);
		}
	}

	return (string) $a;
}

function alxLinkString($conditions, $shortcutted=false)
{
	if($shortcutted)
	{
		$conditions = array($conditions => $shortcutted);
	}

	if(@$conditions['action'] == alxApplication::getConfigVar('action','default'))
	{
		unset($conditions['action']);
	}
	
	array_walk($conditions, function(&$v) {
		$v = strToLower($v);
	
		if(strpos($v, 'http://') === false)
		{
			$v = urlencode($v);
			$v = rawurlencode($v);
		}
	});
	
	if(array_key_exists('controller', $conditions))
	{
		$conditions['lang'] = $_GET['lang'];
	}
	
	$route = alxRoutingManager::getRoute($conditions);

	$routeString = $route->getComputedString($conditions);
	
	if(!$route->hasFlag(ALX_ROUTE_EXTERNAL))
	{
		$routeString = (string) alxApplication::getConfigVar('root', 'app') . $routeString;
	}
	
	
	return (string) $routeString;
}

function a($c, $a, $n, $atr=null)
{
	echo alxLink(array
	(
		'controller' => $c,
		'action' => $a
	), $n, $atr);
}