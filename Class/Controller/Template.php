<?php

/**
 * Generic Template controller. Sets up generic layout, loads styles and scripts.
 */
abstract class Controller_Template extends Controller {

	/**
	 * @var string Location of the template file
	 */
	protected $template = 'template';

	/**
	 * Format: ['href' => 'path/to/style.css']
	 * @var array Contains all required stylesheets
	 */
	protected $styles = array();

	/**
	 * Format: ['src' => 'path/to/script.js']
	 * @var array Contains all required script files
	 */
	protected $scripts = array();

	/**
	 * Format: ['rel' => 'favicon',
	 * 			'href' => 'path/to/favicon.ico']
	 * @var array Contains favicon and apple touch icons
	 */
	protected $icons = array();

	/**
	 * Format: ['name' => 'autor',
	 * 			'content' => 'Foo Barson']
	 * @var array Contains meta tags
	 */
	protected $meta = array();

	/**
	 * @var string Base url for relative anchors. generates a HTML5 <base> tag.
	 */
	protected $base_url;

	/**
	 * @var string Document language
	 */
	protected $lang;

	/**
	 * @var string Document charset
	 */
	protected $charset;

	/**
	 * @var string Page title. Generates a <title> tag.
	 */
	protected $page_title;

	/**
	 * @var string Main content, everyting inside the body.
	 */
	protected $content;

	/**
	 * @var boolean True if the whole template should be rendered, false if only $content is required
	 */
	protected $full_page;

	/**
	 * Set up all generic information.
	 */
	public function before()
	{
		parent::before();

		//$this->styles[]	= ['href' => 'css/reset.css'];
		$this->styles[] = ['href' => 'css/main.css'];
		$this->styles[]	= ['href' => 'css/form.css'];

		$this->scripts[] = ['src' => 'js/vendor/jQuery.min.js'];
		$this->scripts[] = ['src' => 'js/vendor/webforms/webforms2-p.js'];
		$this->scripts[] = ['src' => 'js/main.js'];

		$this->base_url = Config::get('global', 'baseurl');
		$this->lang = Config::get('global', 'language');

		$this->charset = 'utf-8';

		$page_title = 'Form Generator';
		$content = '';

		$this->full_page = ! $this->request->isAjax();
	}

	/**
	 * Pass the template information to Mustache, render output.
	 */
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

			$tpl = Mustache::factory($this->template);

			$this->response->body($tpl->render($view_template));
		}
		else
		{
			$this->response->body($this->content);
		}

		parent::after();
	}

}