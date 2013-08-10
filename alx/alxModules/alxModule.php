<?php

abstract class alxModule
{
	protected $id;
	
	public function getId($id)
	{
		return $this->id;
	}
}