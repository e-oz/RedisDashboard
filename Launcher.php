<?php
namespace Jamm\RedisDashboard;

class Launcher
{
	public function Start($base_url)
	{
		$RedisServer        = new \Jamm\Memory\RedisServer();
		$Request            = new \Jamm\HTTP\Request();
		$RequestParser      = new \Jamm\MVC\Controllers\RequestParser($Request);
		$PageRenderer       = new \Jamm\RedisDashboard\View\PageRenderer();
		$FallbackController = new Controller\Fallback($RedisServer, $PageRenderer);
		$Router             = new \Jamm\MVC\Controllers\Router($RequestParser, $FallbackController);
		$Response           = new \Jamm\HTTP\Response();

		$PageRenderer->setBaseURL($base_url);
		$Request->BuildFromInput();
		$Response->setSerializer($RequestParser->getAcceptedSerializer());
		$Router->addRouteForController('db', new Controller\Database($RedisServer, $RequestParser, $PageRenderer));
		$Router->addRouteForController('key', new Controller\DBKey($RedisServer, $RequestParser, $PageRenderer));

		$Response->setHeader('Content-type', 'text/html; charset=UTF-8');

		$Controller = $Router->getControllerForRequest();
		$Controller->fillResponse($Response);
		$Response->Send();
	}
}
