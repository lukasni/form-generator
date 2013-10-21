<?php

class Router {

	public static function get(Request $request)
	{
		try
		{
			$controller = null;
			$result = null;

			if ( is_subclass_of($request->controller, 'Controller_Base') )
			{
				$controller = new $request->controller($request);
			}
			else
			{
				throw new DomainException('Controller '.$request->controller.' does not exist or does not extend Controller_Base');
			}

			if ( method_exists($controller, $request->action) )
			{
				$controller->before();
				$controller->{$request->action}();
				$controller->after();
			}
			else
			{
				throw new BadMethodCallException('Action '.$request->action.' does not exist in '.$request->controller);
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

}