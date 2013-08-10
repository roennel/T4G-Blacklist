<?php 

class alxFileCollection
{
	private $_files = array();
	
	function getFile($id)
	{
		foreach($this->getFiles() as $file)
		{
			if($file->file == $id)
			{
				return $file;
			}	
		}
	}
	
	function addFile(alxFile $file)
	{
		$this->_files[] = $file;
	}
	
	public function getFiles()
	{
		return $this->_files;
	}
}