<?php

class Router {

	const CONTROLLER_PREFIX = 'Controller_';
	const ACTION_PREFIX = 'action_';

	const DEFAULT_CONTROLLER = 'Index';
	const DEFAULT_ACTION = 'index';

	public static function getController(Request $request)
	{
		$controller = CONTROLLER_PREFIX;
		$result = null;

		if ( empty($request->controller) )
		{
			$controller .= DEFAULT_CONTROLLER;
		}
		else
		{
			$controller .= $controller;
		}

		if ( is_subclass_of($controller, 'Controller_Base') )
		{
			return new $controller($request);
		}
		else
		{
			throw new DomainException('Controller '.$controller.' does not exist or does not extend Controller_Base');
		}
	}

	public static function getAction($request, Controller_Base $controller)
	{
		$action = ACTION_PREFIX;

		if ( empty($request->action) )
		{
			$action .= DEFAULT_ACTION;
		}
		else
		{
			$action .= $request->action;
		}

		if ( method_exists($controller, $action) )
		{
			$controller->before();
			$controller->{$action}();
			$controller->after();
		}
		else
		{
			throw new BadMethodCallException('Action '.$action.' does not exist in '.$controller);
		}
	}

}