<?php

class Controller_Database extends Controller_Template {

	public function action_login()
	{
		$view = [
			'form_action' => 'Database/generate',
		];

		$tpl = Mustache::factory('form/login.mustache');

		$this->content = $tpl->render($view);
	}

	public function action_getDB()
	{
		if ( ! $this->request->isAjax() )
		{
			throw new Exception("No direct access to this action.");
		}

		$dbhost = $this->request->data('host');
		$dbuser = $this->request->data('user');
		$dbpass = $this->request->data('password', '');

		$model = new Model_Database($dbhost, $dbuser, $dbpass);

		$this->content = json_encode($model->showDatabases());

		$this->response->header('Content-type: application/json');
	}

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

		$model = new Model_Database($dbhost, $dbuser, $dbpass, $db);

		$this->content = json_encode($model->showTables());
	}

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

		foreach ($fields as $line)
		{
			$data = $parser->parseLine($line);
			if ( $data !== false)
				$writer->{$data['type']}($data);
		}

		$writer->putLine('<button type="submit">Submit</button>');

		$view = [
			'code' => $writer->render(),
		];

		$tpl = Mustache::factory('form/output');

		$this->content = $tpl->render($view);
	}

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