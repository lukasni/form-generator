<?php

class Router {

	const CONTROLLER_PREFIX = 'Controller_';
	const ACTION_PREFIX = 'action_';

	const DEFAULT_CONTROLLER = 'Index';
	const DEFAULT_ACTION = 'index';

	public static function getController(Request $request)
	{
		$controller = self::CONTROLLER_PREFIX;
		$result = null;

		if ( $request->controller == '' )
		{
			$controller .= self::DEFAULT_CONTROLLER;
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
		$action = self::ACTION_PREFIX;

		if ( $request->action == '' )
		{
			$action .= self::DEFAULT_ACTION;
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