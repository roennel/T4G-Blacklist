<?php

class alxFile_HtAccess extends alxFile
{
	private $struct = array
	(
		'DirectoryIndex' 	=> array(),
		'ResetOptions' 		=> false,
		'Options' 				=> array(),
		'OutputFilter' 		=> array(),
		'ErrorDocuments' 	=> array(),
		'RewriteRules' 		=> array()
	);

	function __construct()
	{
		$this->file = '.htaccess';
		$this->type = 'text/plain';
		
		if(defined('ALX_APP'))
		{
			$this->path = ALX_APP_PATH . '/';
		}
	}
	
	public function addDirectoryIndex($index)
	{
		$this->struct['DirectoryIndex'][] = $index;
	}
	
	public function resetOptions()
	{
		$this->struct['ResetOptions'] = true;
	}
	
	public function addOption($option)
	{
		$this->struct['Options'][] = $option;
	}
	
	public function addOutputFilter($filter)
	{
		$this->struct['OutputFilter'][] = $filter;
	}
	
	public function addErrorDocument($code, $path)
	{
		$this->struct['ErrorDocuments'][$code] = $path;
	}
	
	public function addRewriteRule($catch, $path, $options=null)
	{
		$this->struct['RewriteRules'][] = array($catch, $path, $options);
	}
	
	public function process()
	{
		foreach($struct as $id => $data)
		{
			switch($id)
			{
				case 'DirectoryIndex':
					$this->addLine('DirectoryIndex ' . implode(' ', $data));
				break;
				
				case 'ResetOptions':
					$this->addLine('Options None');
				break;
				
				case 'Options':
					$this->addLine('Option ' . implode(' ', $data));
				break;
				
				case 'OutputFilter':
					$this->addLine('SetOutputFilter ' . implode(' ', $data));
				break;
				
				case 'ErrorDocument':
					foreach($data as $code => $path)
					{
						$this->addLine("ErrorDocument $code $path");
					}
				break;

				case 'RewriteRules':
					if(!empty($data))
					{
						$this->addLine('RewriteEngine On');
					}
					
					foreach($data as $rule)
					{
						$options = !empty($rule[2]) ? "[{$rule[2]}]" : null;
						
						$this->addLine("RewriteRule ^{$rule[0]}$ {$rule[1]} $options");
					}
				break;
			}
		}
	}
	
}