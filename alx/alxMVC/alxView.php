<?php

class alxView
{
	public $id;
	public $subViews = array();
	
	protected $data = array();
	
	function __construct($id)
	{
		$this->id = $id;
	}

	public function get($key)
	{
		return $this->data[$key];
	}
	
	public function add($key, $val)
	{
		$this->data[$key] = $val;
	}
	
	public function setData($data)
	{
		$this->data = array_merge($this->data, $data);
	}
	
	public function getSubView($viewId)
	{
		return $this->subViews[$viewId]['view'];
	}

	public function render($global=false, $sharedApp=false)
	{
    $dir = "";
    
		if(!$sharedApp)
		{
			$dir = alxApplication::getConfigVar('app');
			$dir = $dir->path . $dir->view_dir;
		}
		
		$globalPath = $dir . $this->id . '.php';
		
		$sharedPath = $dir . 'shared/';
		$sharedPath.= $this->id . '.php';

		$specPath 	= $dir . alxRequestHandler::getController();
		$specPath	 .= '/' . $this->id . '.php';
		
    $shared2Path = alxApplication::getConfigVar('shared_path', 'app');
    $shared2Path.= '/' . $this->id . '.php';
    
    $t = (file_exists($sharedPath) ? 'shared' : alxRequestHandler::getController());
    
    if(file_exists($specPath))
    {
      $t = alxRequestHandler::getController();
    }
    
		$data = (object) $this->data;
		
        if($this->id != 'global') echo "\n<!-- View: " . $t . "/" . $this->id . " -->\n";
        
        $rt = false;
        
		if($global)
		{
			include $globalPath;
			
			$rt = true;
		}
		elseif($sharedApp)
		{
			$sharedAppPath = alx::getToolkitPath() . 'alxApplication/shared/views/dev/';
			$sharedAppPath.= $this->id . '.php';
			
			include $sharedAppPath;
			
			$rt = true;
		}
		elseif(file_exists($specPath))
		{
			include $specPath;
			
			$rt = true;
		}
		elseif(file_exists($sharedPath))
		{
			include $sharedPath;
			
			$rt = true;
		}
    elseif(file_exists($shared2Path))
    {
      include $shared2Path;
      
      $rt = true;
    }
		else
		{
			echo <<<EOT
There is no View '$specPath'
<br />
There is no View '$sharedPath'
EOT;
			
			$rt = false;
		}
        
        //if($this->id != 'global') echo "\n<!-- End View: " . $t . "/" . $this->id . " -->\n";
	
        return $rt;
    }

	public function addContainer($containerId, &$view=null, $viewData=null)
	{
		$this->subViews[$containerId] = array();
		$this->subViews[$containerId]['view'] = &$view;
		$this->subViews[$containerId]['data'] = $viewData;
	}
	
	public function getContainer($containerId)
	{
		return $this->subViews[$containerId];
	}
	
	public function insertContainer($containerId)
	{
		$view = $this->subViews[$containerId]['view'];
		
		if(is_object($view))
		{
			$data = $this->subViews[$containerId]['data'];
		
			if(is_array($data))
			{
				$view->setData($data);
			}
		
			$view->render();
		}
	}
	
	protected function insertTitle($content)
	{
		$title = new alxHTMLBuilder('title');
		$title->setContent($content);

		echo "\n{$title}";
	}
	
	protected function insertCSS($css, $external=false, $ext='css', $class=null)
	{
		$catch = $external ? 'external' : 'css';
		
		$link = alxLinkString($catch, "{$css}.{$ext}");	
		
		$style = new alxHTMLBuilder('link');
		$style->set('href', $link);
		
		if($class)
		{
			$style->setClass($class);
		}
		
		echo "\n{$style}";
	}
	
	protected function insertJS($js, $external=false, $ext='js')
	{
		$catch = $external ? 'external' : 'js';
		
		$link = alxLinkString($catch, "{$js}.{$ext}");	
		
		$script = new alxHTMLBuilder('script');
		$script->set('src', $link);
		
		echo "\n{$script}";
	}
}

alx::load('HTML','HTMLBuilder');