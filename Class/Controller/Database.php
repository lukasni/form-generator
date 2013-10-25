<?php

class Controller_Database extends Controller_Template {

	public function action_login()
	{
		$m = Mustache::factory();

		$data = [
			'form_action' => 'Database/generate',
		];

		$tpl = $m->loadTemplate('form/login.mustache');

		$this->content = $tpl->render($data);
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
		$writer->init('', 'POST', ['class' => 'form-vertical', 'id' => 'output']);

		foreach ($fields as $line)
		{
			$data = $parser->parseLine($line);
			if ( $data !== false)
				$writer->{$data['type']}($data);
		}

		$writer->putLine('<button type="submit">Submit</button>');
		$writer->putLine('<div class="spacer"></div>');

		$view = [
			'code' => $writer->render(),
		];

		$m = Mustache::factory();
		$output = $m->loadTemplate('form/output');

		$this->content = $output->render($view);
	}

	public function action_download()
	{
		$this->content = $this->request->data('code');
	}
}