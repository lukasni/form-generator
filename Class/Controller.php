<?php

abstract class Controller {

	protected $request;
	protected $response;
	
	public function __construct(Request $request)
	{
		$this->request  = $request;
		$this->response = new Response;
	}

	public function action_index()
	{
		
	}

	public function before()
	{

	}

	public function after()
	{

	}

	public function request(Request $request = null)
	{
		if ( is_null($request) )
		{
			return $this->request;
		}

		$this->request = $request;
	}

	public function response(Response $response = null)
	{
		if ( is_null($response) )
		{
			return $this->response;
		}

		$this->response = $response;
	}

	public function execute()
	{
		return $this->response->render();
	}

}