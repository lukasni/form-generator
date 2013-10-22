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

		$uri = str_replace(BASEURL, '', $_SERVER['REQUEST_URI']);
		$query_start = strpos($uri, '?');
		$get_params = '';

		if ( $query_start !== false )
		{
			$get_params = substr($uri, $query_start+1);
			$uri = substr($uri, 0, $query_start);
		}

		$url_tokens = explode('/', $uri);

		$controller	= !empty($url_tokens['0']) ? $url_tokens[0] : '';
		$action 	= array_key_exists('1', $url_tokens) ? $url_tokens[1] : '';
		$params 	= array_key_exists('2', $url_tokens) ? array_slice($url_tokens, 2) : array();

		$method 	= isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
		$requested_with = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : null;

		$data 		= $method == 'GET' ? $get_params : file_get_contents('php://input');

		if ( ! empty($data) )
		{
			$data = Request::parse_data($data);
		}

		$request 	= new Request($uri, $controller, $action, $params, $method, $requested_with, $data);

		return $request;
	}

	/**
	 * Parse request data
	 * @param  string $data Data gotten from a http request
	 * @return array        Reuqest data parsed as an array of key/value pairs.
	 */
	public function parse_data($data)
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

	/**
	 * Detect if the request was an ajax request.
	 * 
	 * @return boolean True if $requested_with == xmlhttprequest, False otherwise.
	 */
	public function is_ajax()
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

}