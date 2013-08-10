<?php

class alxHTMLBuilder
{
	protected $elementTag;
	protected $elementAttributes;
	protected $elementContent = null;
	protected $elementClasses	= array();
	
	protected $defaultAttributes = array
	(
		'style' => array(
			'type' => 'text/css'
		),
		'link' => array(
			'rel' 		=> 'stylesheet',
			'type' 		=> 'text/css'
		),
		'script' => array(
			'type' => 'text/javascript'
		)
	);
	
	protected $noCloseTags = array
	(
		'link',
		'img',
		'input'
	);

	function __construct($elementTag='div', $elementAttributes=array())
	{
		(string) 	$elementTag;
		(array) 	$elementAttributes;

		$this->elementTag = $elementTag;

		$this->elementAttributes = $elementAttributes;
	}
	
	public function addClass($class)
	{
		(string) $class;
		
		$this->elementClasses[] = $class;
	}

	public function addContent($htmlContent)
	{
		(string) $htmlContent;
		
		$this->elementContent.= $htmlContent;
	}
	
	public function set($key, $value)
	{
		(string) $key;
		(string) $value;
		
		$this->elementAttributes[$key] = $value;
	}
	
	public function setId($id)
	{
		(string) $id;
		
		$this->set('id', $id);
	}
	
	public function setClass($class)
	{
		(string) $class;
		
		$this->elementClasses = array();
		
		$this->addClass($class);
	}
	
	public function setContent($htmlContent)
	{
		(string) $htmlContent;
		
		$this->elementContent = $htmlContent;
	}
	
	public function setAttributes(array $attribs)
	{
		foreach($attribs as $key => $val)
		{
			$this->set($key, $val);
		}
	}
	
	public function get($key)
	{
		(string) $key;
		
		if(array_key_exists($key, $this->elementAttributes))
		{
			return $this->elementAttributes[$key];
		}
		
		return null;
	}
	
	public function getId()
	{
		return $this->get('id');
	}
	
	public function getClasses()
	{
		return $this->elementClasses;
	}

	public function render()
	{
		if(count($this->elementClasses) > 0)
		{
			$this->set('class', implode(' ', $this->elementClasses));
		}
		
		$htmlElement = $this->elementTag;
	
		$rawString = "<$htmlElement";
		
		if(array_key_exists($this->elementTag, $this->defaultAttributes))
		{
			$htmlAttributes = array_merge
			(
				$this->defaultAttributes[$this->elementTag],
				$this->elementAttributes
			);
		} 
		else
		{
			$htmlAttributes = $this->elementAttributes;
		}
		
		if(count($htmlAttributes) > 0)
		{
			$rawString.= ' ' . $this->buildAttributes($htmlAttributes);
		}

		if(in_array($htmlElement, $this->noCloseTags))
		{
			$rawString.= " />";
		}
		else
		{
			$rawString.= ">";
		
			$rawString.= htmlentities($this->elementContent);
			
			$rawString.= "</$htmlElement>";
		}
		
		return (string) $rawString;
	}
	
	public function __toString()
	{
		return (string) $this->render();
	}

	protected function buildAttributes($htmlAttributes=null)
	{
		(array) $htmlAttributes;
		
		$htmlAttributes = count($htmlAttributes) > 0 ? $htmlAttributes : $this->elementAttributes;
		
		$attributeCollection = array();
		
		foreach($htmlAttributes as $htmlAttributeKey => $htmlAttributeValue)
		{
			$attributeCollection[] = "$htmlAttributeKey=\"$htmlAttributeValue\"";
		}
		
		return implode(' ', $attributeCollection);
	}
}
