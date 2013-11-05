<?php

/**
 * Handles HTTP Request sent to the framework.
 *
 * @author  Lukas Niederberger <lukas.niederberger@gibmit.ch>
 */
class Request {

	protected static $initial = NULL;

	/**
	 * @var string Requested URI, not including base URL.
	 */
	protected $uri;

	/**
	 * @var string Requested controller.
	 */
	protected $controller;

	/**
	 * @var string Requested controller action
	 */
	protected $action;

	/**
	 * @var array Additional route parameters
	 */
	protected $params = array();

	/**
	 * @var string HTTP Method of the request
	 */
	protected $method;

	/**
	 * @var string HTTP_X_REQUESTED_WITH, used to detect ajax requests.
	 */
	protected $requested_with;

	/**
	 * @var array Request data, recieved via php://input;
	 */
	protected $data = array();

	public function __get( $name )
	{
		return $this->$name;
	}

	public function __construct($uri, $controller, $action, $params = array(), $method = 'GET', $requested_with = NULL, $data = NULL)
	{
		$this->uri = $uri;
		$this->controller = $controller;
		$this->action = $action;
		$this->params = $params;
		$this->method = $method;
		$this->requested_with = $requested_with;
		$this->data = $data;
	}

	/**
	 * Get the initial request instance. Might be modeified to allow for HMVC Requests.
	 * 
	 * @return Request initial requests, created from the $_SERVER Values.
	 */
	public static function instance()
	{
		if ( ! is_null(self::$initial) )
		{
			return self::$initial;
		}

		$uri = Arr::get($_SERVER, 'PATH_INFO', '');
		$uri = ltrim($uri, '/');
		
		$get_params = Arr::get($_SERVER, 'QUERY_STRING', '');
		
		$url_tokens = explode('/', $uri);

		$controller	= !empty($url_tokens['0']) ? $url_tokens[0] : '';
		$action 	= array_key_exists('1', $url_tokens) ? $url_tokens[1] : '';
		$params 	= array_key_exists('2', $url_tokens) ? array_slice($url_tokens, 2) : array();

		$method 	= isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
		$requested_with = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : null;

		$data 		= $method == 'GET' ? $get_params : file_get_contents('php://input');
		$pdata = [];

		if ( ! empty($data) )
		{
			parse_str($data, $pdata);
		}

		$request 	= new Request($uri, $controller, $action, $params, $method, $requested_with, $pdata);

		return $request;
	}

	public function data($key, $default = null)
	{
		if ( array_key_exists($key, $this->data) )
		{
			return $this->data[$key];
		}
		else
		{
			return $default;
		}
	}

	/**
	 * Detect if the request was an ajax request.
	 * 
	 * @return boolean True if $requested_with == xmlhttprequest, False otherwise.
	 */
	public function isAjax()
	{
		return strtolower($this->requested_with) == 'xmlhttprequest';
	}

	/**
	 * Get controller object via the Router class, execute action and echo the response body.
	 * TODO: Add proper response handling allowing for HMVC Request flow.
	 */
	public function execute()
	{
		$controller = Router::getController($this);

		Router::executeAction($this, $controller);

		echo $controller->execute();
	}

	/**
	 * Parse request data
	 *
	 * TODO: To be deleted, replaced by parse_str
	 * 
	 * @param  string $data Data gotten from a http request
	 * @return array        Reuqest data parsed as an array of key/value pairs.
	 */
	public function parseData($data)
	{
		if ( strpos($data, '=') === false )
		{
			return array();
		}

		if ( strpos($data, '&') === false )
		{
			list($key, $value) = explode('=', $data);

			return [$key => $value];
		}
		else
		{
			$fields = explode('&', $data);
			$result = [];

			foreach ( $fields as $f)
			{
				list($key, $value) = explode('=', $f);

				$result[$key] = $value;
			}

			return $result;
		}
	}

}