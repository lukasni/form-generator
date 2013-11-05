<?php

/**
 * Controller for the input method database.
 *
 * @author  Lukas Niederberger <lukas.niederberger@gibmit.ch>
 */
class Controller_Database extends Controller_Template {

	/**
	 * Login action. Will generate the login view.
	 */
	public function action_login()
	{
		$view = [
			'form_action' => 'Database/generate',
		];

		$tpl = Mustache::factory('form/login.mustache');

		$this->content = $tpl->render($view);
	}

	/**
	 * Ajax action to get a list of databases to select from
	 * Throws an exception if the request is not ajax.
	 */
	public function action_getDB()
	{
		// Only allow ajax requests
		if ( ! $this->request->isAjax() )
		{
			throw new Exception("No direct access to this action.");
		}

		$dbhost = $this->request->data('host');
		$dbuser = $this->request->data('user');
		$dbpass = $this->request->data('password', '');

		// Try to connect to database.
		try
		{	
			$model = new Model_Database($dbhost, $dbuser, $dbpass);
		
			$this->content = json_encode($model->showDatabases());
			$this->response->header('Content-type: application/json');
		}
		catch (Exception $e)
		{
			$this->content = 'Cannot load databases. Please check your login information.';

			// Set the error code to ensure clientside error handling
			$this->response->statusCode(500);
		}
	}

	/**
	 * Ajax action to get a list of tables to select from
	 * Throws an exception if the request is not ajax.
	 */
	public function action_getTbl()
	{
		// Only allow ajax requests
		if ( ! $this->request->isAjax() )
		{
			throw new Exception("No direct access to this action.");
		}
		
		$dbhost = $this->request->data('host');
		$dbuser = $this->request->data('user');
		$dbpass = $this->request->data('password', '');
		$db 	= $this->request->data('database');

		// Try to connect to database.
		try
		{
			$model = new Model_Database($dbhost, $dbuser, $dbpass, $db);

			$this->content = json_encode($model->showTables());	
			$this->response->header('Content-type: application/json');
		}
		catch (Exception $e)
		{
			$this->content = "Can't load tables. Please select a valid database first.";

			// Set the error code to ensure clientside error handling
			$this->response->statusCode(500);
		}
	}

	/**
	 * Controller action to generate the output form from the selected database table.
	 * Uses Form_Parser_DB and Form_Writer to generate the output.
	 */
	public function action_generate()
	{
		// Set up the login data
		$dbhost = $this->request->data('host');
		$dbuser = $this->request->data('user');
		$dbpass = $this->request->data('password', '');
		$db 	= $this->request->data('database');
		$tbl 	= $this->request->data('table');

		$model = new Model_Database($dbhost, $dbuser, $dbpass, $db);

		// Fetch table fields
		$fields = $model->describe($tbl);

		// Initiate parser and writer
		$parser = new Form_Parser_DB();
		$writer = new Form_Writer();

		// Initilize the form
		$writer->init('', 'post', ['class' => 'form-vertical', 'id' => 'output']);

		$writer->fieldset(['legend' => ucfirst($tbl)]);

		// Loop through all fields, pass the parsed field to the writer
		foreach ($fields as $line)
		{
			$data = $parser->parseLine($line);
			if ( $data !== false)
				$writer->{$data['type']}($data);
		}

		// Set up view data
		$view = [
			'code' => $writer->render(),
		];

		$tpl = Mustache::factory('form/output');

		// Render the output
		$this->content = $tpl->render($view);
	}

	/**
	 * Download action. Generates a zip file that contains the generated form and some basic styling
	 */
	public function action_download()
	{
		// Create  a new temporary zip file
		$model = new Model_Zip();

		$view = [
			'form' => $this->request->data('code'),
		];

		// Set up the index template
		$tpl = Mustache::factory('download/index');

		// Add index file to zip
		$model->addFile($tpl->render($view));

		// Make sure the template is not added to the download
		$this->full_page = false;

		// Read the zip file as hex data, pass it to the response body
		$this->content = $model->read();

		// Set up headers to ensure correct download of the zip file.
		$this->response->header('Content-Type: application/zip');
		$this->response->header('Content-Length: '.strlen($this->content));
		$this->response->header('Content-Disposition: attachment; filename="form-download.zip"');
	}
}