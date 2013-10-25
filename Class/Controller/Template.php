<?php

abstract class Controller_Template extends Controller {

	protected $template = 'template';

	protected $styles = array();
	protected $scripts = array();
	protected $icons = array();
	protected $meta = array();
	protected $base_url;
	protected $lang;
	protected $charset;

	protected $page_title;
	protected $content;

	protected $full_page;

	public function before()
	{
		parent::before();

		//$this->styles[]	= ['href' => 'css/reset.css'];
		$this->styles[] = ['href' => 'css/main.css'];
		$this->styles[]	= ['href' => 'css/form.css'];

		$this->scripts[] = ['src' => 'js/vendor/jQuery.min.js'];
		$this->scripts[] = ['src' => 'js/main.js'];

		$this->base_url = Config::get('global', 'baseurl');
		$this->lang = Config::get('global', 'language');

		$this->charset = 'utf-8';

		$page_title = 'Form Generator';
		$content = '';

		$this->full_page = ! $this->request->isAjax();
	}

	public function after()
	{
		if ( $this->full_page )
		{
			$view_template = new View_Template();

			$view_template->styles  	= $this->styles;
			$view_template->scripts 	= $this->scripts;
			$view_template->icons 		= $this->icons;
			$view_template->meta 		= $this->meta;
			$view_template->base_url 	= $this->base_url;
			$view_template->lang 		= $this->lang;
			$view_template->charset 	= $this->charset;
			$view_template->page_title 	= $this->page_title;
			$view_template->content 	= $this->content;

			$m = Mustache::factory();

			$tpl = $m->loadTemplate($this->template);

			$this->response->body($tpl->render($view_template));
		}
		else
		{
			$this->response->body($this->content);
		}

		parent::after();
	}

}