<?php

class Controller_Base {

	protected $request;
	
	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	public function action_index()
	{
		print_r($_REQUEST);
	}

	public function before()
	{

	}

	public function after()
	{

	}

}