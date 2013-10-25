<?php

class Router {

	public static function getController(Request $request)
	{
		$controller = Config::get('router', 'prefix.controller');
		$result = null;

		if ( $request->controller == '' )
		{
			$controller .= Config::get('router', 'default.controller');
		}
		else
		{
			$controller .= $request->controller;
		}

		if ( is_subclass_of($controller, 'Controller') )
		{
			return new $controller($request);
		}
		else
		{
			throw new DomainException('Controller '.$controller.' does not exist or does not extend Controller');
		}
	}

	public static function executeAction($request, Controller &$controller)
	{
		$action = Config::get('router', 'prefix.action');

		if ( $request->action == '' )
		{
			$action .= Config::get('router', 'default.action');
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
			throw new BadMethodCallException('Action '.$action.' does not exist in '.get_class($controller));
		}
	}

}