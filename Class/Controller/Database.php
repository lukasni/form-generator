<?php

class Controller_Database extends Controller_Template {

	public function action_login()
	{
		$m = Mustache::factory();

		$data = [
			'form_action' => 'Database/Login',
			'databases' => ['foo', 'bar', 'baz'],
			'tables' => ['foobar', 'foobaz', 'barbaz'],
		];

		$tpl = $m->loadTemplate('form/login.mustache');

		$this->content = $tpl->render($data);

		if ($this->request->method == 'POST')
		{
			$this->content .= '<pre>'.var_export($this->request->data, true).'</pre';
		}
	}

}