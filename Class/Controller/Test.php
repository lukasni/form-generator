<?php

class Controller_Test extends Controller_Template {

	public function action_test()
	{
		$test = <<<EOT
<form action="http://localhost/form-generator/Database/process" method="post">
	<input type="text" name="test1"><br>
	<input type="text" name="test2"><br>
	<input type="text" name="test3"><br>
	<button type="submit">Submit</button>
</form>
EOT;
		$this->content = $test;
	}

	public function action_process()
	{
		$this->content = var_export($this->request->data);
	}

	public function action_download()
	{
		$this->full_page = false;
		$this->content = readfile(APPPATH.'local/download.zip');	
		$this->response->header('Content-Type: application/zip');
		$this->response->header('Content-Disposition: attachment; filename="form-download.zip"');
		$this->response->header('Content-Length: '.$this->response->contentLength());
	}

	public function action_mustache()
	{
		$this->content = "Test!";
	}

}