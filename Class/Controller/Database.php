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
		if ( ! $this->request->isAjax() )
		{
			throw new Exception("No direct access to this action.");
		}

		$dbhost = $this->request->data('host');
		$dbuser = $this->request->data('user');
		$dbpass = $this->request->data('password', '');

		try
		{	
			$model = new Model_Database($dbhost, $dbuser, $dbpass);
		
			$this->content = json_encode($model->showDatabases());
		}
		catch (Exception $e)
		{
			$this->content = 'Cannot load databases. Please check your login information.';

			$this->response->statusCode(400);
		}

		$this->response->header('Content-type: application/json');
	}

	/**
	 * Ajax action to get a list of tables to select from
	 * Throws an exception if the request is not ajax.
	 */
	public function action_getTbl()
	{
		if ( ! $this->request->isAjax() )
		{
			throw new Exception("No direct access to this action.");
		}
		
		$dbhost = $this->request->data('host');
		$dbuser = $this->request->data('user');
		$dbpass = $this->request->data('password', '');
		$db 	= $this->request->data('database');

		try
		{
			$model = new Model_Database($dbhost, $dbuser, $dbpass, $db);

			$this->content = json_encode($model->showTables());	
		}
		catch (Exception $e)
		{
			$this->content = "Can't load tables. Please select a valid database first.";

			$this->response->statusCode(400);
		}

		$this->response->header('Content-type: application/json');
	}

	/**
	 * Controller action to generate the output form from the selected database table.
	 * Uses Form_Parser_DB and Form_Writer to generate the output.
	 */
	public function action_generate()
	{
		$dbhost = $this->request->data('host');
		$dbuser = $this->request->data('user');
		$dbpass = $this->request->data('password', '');
		$db 	= $this->request->data('database');
		$tbl 	= $this->request->data('table');

		$model = new Model_Database($dbhost, $dbuser, $dbpass, $db);

		$fields = $model->describe($tbl);

		$parser = new Form_Parser_DB();
		$writer = new Form_Writer();
		$writer->init('', 'post', ['class' => 'form-vertical', 'id' => 'output']);

		$writer->fieldset(['legend' => ucfirst($tbl)]);

		foreach ($fields as $line)
		{
			$data = $parser->parseLine($line);
			if ( $data !== false)
				$writer->{$data['type']}($data);
		}

		$view = [
			'code' => $writer->render(),
		];

		$tpl = Mustache::factory('form/output');

		$this->content = $tpl->render($view);
	}

	/**
	 * Download action. Generates a zip file that contains the generated form and some basic styling
	 */
	public function action_download()
	{
		$model = new Model_Zip();

		$view = [
			'form' => $this->request->data('code'),
		];

		$tpl = Mustache::factory('download/index');

		$model->addFile($tpl->render($view));

		$this->full_page = false;

		$this->content = $model->read();

		$this->response->header('Content-Type: application/zip');
		$this->response->header('Content-Length: '.strlen($this->content));
		$this->response->header('Content-Disposition: attachment; filename="form-download.zip"');
	}
}