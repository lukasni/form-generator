<?php

class Controller_Database extends Controller {

	public function action_test()
	{
		$test = <<<EOT
<form action="http://localhost/form-generator/Database/process" method="get">
	<input type="text" name="test1"><br>
	<input type="text" name="test2"><br>
	<input type="text" name="test3"><br>
	<button type="submit">Submit</button>
</form>
EOT;
		$this->response->body($test);
	}

	public function action_process()
	{
		print_r($this->request->data);
	}

	public function action_download()
	{
		$this->response->body(readfile(APPPATH.'local/download.zip'));	
		$this->response->header('Content-Type: application/zip');
		$this->response->header('Content-Disposition: attachment; filename="form-download.zip"');
		$this->response->header('Content-Length: '.$this->response->content_length());
	}

}