<?php

class Model_Zip {
	
	protected $source;
	protected $tmpfile;
	protected $zip;

	public function __construct($source = null)
	{
		if ( is_null($source) )
		{
			$source = Config::get('download', 'source');
		}

		$this->source 	= $source;
		$this->tmpfile 	= tempnam(APPPATH.'tmp', 'zip');

		copy($this->source, $this->tmpfile);

		$zip = new ZipArchive();

		$res = $zip->open($this->tmpfile, ZipArchive::CREATE);

		if ( $res !== true )
		{
			throw new Exception('Zip file could not be opened');
		}
		else
		{
			$this->zip = $zip;
		}

		return $this;
	}

	public function addFile($content, $name = null)
	{
		if ( is_null($name) )
		{
			$name = Config::get('download', 'index');
		}

		$this->zip->deleteName($name);
		$this->zip->addFromString($name, $content);

		return $this;
	}

	public function read()
	{
		$this->zip->close();

		return file_get_contents($this->tmpfile);
	}

	public function __destruct()
	{
		unlink($this->tmpfile);
		unset($this->zip);
	}
	
}