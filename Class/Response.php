<?php

/**
 * Handles HTTP request responses. Allows for the setting of custom protocol, headers and response code.
 *
 * @author  Lukas Niederberger <lukas.niederberger@gibmit.ch>
 */
class Response {

	/**
	 * @var array Stores the headers that will be sent on execution.
	 */
	protected $headers = array();

	/**
	 * @var int The HTTP Response code
	 */
	protected $responseCode;

	/**
	 * @var string Holds the body of the response. this is what will be displayed on the client.
	 */
	protected $body;

	/**
	 * @var array Lists all HTTP error codes.
	 */
	public static $errorCodes = array(
	    100 => 'Continue',
	    101 => 'Switching Protocols',
	    102 => 'Processing',
	    200 => 'OK',
	    201 => 'Created',
	    202 => 'Accepted',
	    203 => 'Non-Authoritative Information',
	    204 => 'No Content',
	    205 => 'Reset Content',
	    206 => 'Partial Content',
	    207 => 'Multi-Status',
	    208 => 'Already Reported',
	    226 => 'IM Used',
	    300 => 'Multiple Choices',
	    301 => 'Moved Permanently',
	    302 => 'Found',
	    303 => 'See Other',
	    304 => 'Not Modified',
	    305 => 'Use Proxy',
	    307 => 'Temporary Redirect',
	    308 => 'Permanent Redirect',
	    400 => 'Bad Request',
	    401 => 'Unauthorized',
	    402 => 'Payment Required',
	    403 => 'Forbidden',
	    404 => 'Not Found',
	    405 => 'Method Not Allowed',
	    406 => 'Not Acceptable',
	    407 => 'Proxy Authentication Required',
	    408 => 'Request Timeout',
	    409 => 'Conflict',
	    410 => 'Gone',
	    411 => 'Length Required',
	    412 => 'Precondition Failed',
	    413 => 'Request Entity Too Large',
	    414 => 'Request-URI Too Long',
	    415 => 'Unsupported Media Type',
	    416 => 'Requested Range Not Satisfiable',
	    417 => 'Expectation Failed',
	    500 => 'Internal Server Error',
	    501 => 'Not Implemented',
	    502 => 'Bad Gateway',
	    503 => 'Service Unavailable',
	    504 => 'Gateway Timeout',
	    505 => 'HTTP Version Not Supported',
	    509 => 'Bandwidth Limit Exceeded',
	    510 => 'Not Extended', 
	    511 => 'Network Authentication Required', 
	    598 => 'Network read timeout error', 
	    599 => 'Network connect timeout error',
	);

	/**
	 * Add a new header to the list.
	 * 
	 * TODO: Change headers to key/value array
	 * 
	 * @param string  $header  The header according to RFC 2616, see PHP manual header()
	 * @param boolean $replace 
	 */
	public function header($header, $replace = true)
	{
		$this->headers[] = ['text' => $header, 'replace' => $replace];
	}

	/**
	 * Get or set the HTTP Status code.
	 * @param  int $code Status code that will be set for the response. Leave empty to get current code.
	 * @return int       the current HTTP Status code
	 */
	public function statusCode($code = null)
	{
		if ( is_null($code) )
		{
			return $this->responseCode;
		}
		$this->responseCode = $code;
	}

	/**
	 * Get or set the body of the response.
	 * 
	 * @param  string $value Body that will be set. Leave empty to get current body.
	 * @return string        Current information stored in body.
	 */
	public function body($value = null)
	{
		if ( is_null($value) )
		{
			return $this->body;
		}

		$this->body = (string) $value;
	}

	/**
	 * Render the response. Will set the headers and status code, then return the response for display.
	 * @return Response complete response for display.
	 */
	public function render()
	{
		foreach ($this->headers as $h)
		{
			header($h['text'], $h['replace']);
		}

		http_response_code($this->responseCode);

		return $this;
	}

	/**
	 * Get the length of the information stored in body
	 * @return int Content length, used for some headers.
	 */
	public function contentLength()
	{
		return strlen($this->body);
	}

	/**
	 * Show the body when the object is converted to a string
	 * @return string Current body content.
	 */
	public function __toString()
	{
		return (string) $this->body;
	}
}