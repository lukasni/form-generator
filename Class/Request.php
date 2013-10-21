<?php

class Request {

	protected static $initial = NULL;

	protected $uri;
	protected $controller;
	protected $action;
	protected $params;
	protected $method;
	protected $requested_with;
	protected $data = array();

	public function __get( $name )
	{
		return $this->$name;
	}

	private function __construct($uri, $controller, $action, $params = array(), $method = 'GET', $requested_with = NULL, $data = NULL)
	{
		$this->uri = $uri;
		$this->controller = $controller;
		$this->action = $action;
		$this->params = $params;
		$this->method = $method;
		$this->requested_with = $requested_with;
		$this->data = $data;
	}

	public static function instance()
	{
		if ( ! is_null(self::$initial) )
		{
			return self::$initial;
		}

		$uri = str_replace(BASEURL, '', $_SERVER['REQUEST_URI']);
		$url_tokens = explode('/', $uri);

		$controller	= !empty($url_tokens['0']) ? $url_tokens[0] : 'index';
		$action 	= array_key_exists('1', $url_tokens) ? $url_tokens[1] : 'index';
		$params 	= array_key_exists('2', $url_tokens) ? array_slice($url_tokens, 2) : null;

		$method 	= isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
		$requested_with = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : null;

		$data 		= file_get_contents('php://input');

		$request 	= new Request($uri, $controller, $action, $params, $method, $requested_with, $data);

		return $request;
	}

	public function is_ajax()
	{
		return strtolower($this->requested_with) == 'xmlhttprequest';
	}

}