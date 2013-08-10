<?php

abstract class alxController
{
	public static $views = array();
	
	protected $data = array();

	protected $defaultContainer = 'content';
	
	public $onRender = null;

	protected function add($key, $value)
	{
		$this->data[$key] = $value;
	}
	
	public function redirect($controller=null, $action=null, $data=null, $queryString=null)
	{
		$route = alxLinkString(array
		(
			'controller' => $controller, 
			'action' => $action
		));

		if(is_array($queryString))
		{
			$route.= '?';
			$qs = array();
			
			foreach($queryString as $key => $val)
			{
				$qs[] = "$key=$val";
			}
			
			$route.= implode('&', $qs);
		}
		
		header("location: $route");
	}
	
	public function redirectDefault($queryString=null)
	{
		$default = alxApplication::getConfigVar('default');
		
		$this->redirect($default->controller, $default->action, null, $queryString);
	}

  protected function render($view=null,$container=null)
	{
		$data = array
		(
			$view ? $view :  alxRequestHandler::getAction(), 
			$this->data,
			$this->onRender
		);
		
		self::$views[$container ? $container :  $this->defaultContainer] = $data;
	}
	
	protected function respond($view=null)
	{
		$view = new alxView($view ?: alxRequestHandler::getAction());
		
		$view->setData($this->data);
		
		$view->render();
		
		exit();
	}
  
  protected function respondJSON($str)
  {
    exit(json_encode($str));
  }
	
	protected function respondString($str)
	{
		exit($str);
	}
}