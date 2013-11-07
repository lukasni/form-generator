<?php

/**
 * Model class for managing Zip files.
 *
 * @author  Lukas Niederberger <lukas.niederberger@gibmit.ch>
 */
class Model_Zip {
	
	protected $source;
	protected $tmpfile;
	protected $zip;

	/**
	 * Constructor copies the source Archive to a temporary location and creates a ZipArchive object for it.
	 *
	 * If no source path is passed the source will be loaded from config. 
	 * @param string $source Path to source file.
	 * @return Model_Zip parent object for method chaining.
	 */
	public function __construct($source = null)
	{
		if ( is_null($source) )
		{
			$source = Config::get('download', 'source');
		}

		// Create temporary zip file for manipulation.
		$this->source 	= $source;
		$this->tmpfile 	= tempnam(APPPATH.'tmp', 'zip');

		copy($this->source, $this->tmpfile);

		$zip = new ZipArchive();

		// Open the temporary zip archive.
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

	/**
	 * Add a new file to the zip archive.
	 * @param string $content Content of the new file
	 * @param string $name    name and location for the new file, relative to the root of the zip folder.
	 * @return Model_Zip parent object for method chaining
	 */
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

	/**
	 * Read the zip file as a hex string.
	 * @return Model_File parent object for method chaining.
	 */
	public function read()
	{
		$this->zip->close();

		return file_get_contents($this->tmpfile);
	}

	/**
	 * Delete the temporary file when the object is destroyed.
	 */
	public function __destruct()
	{
		unlink($this->tmpfile);
		unset($this->zip);
	}
	
}