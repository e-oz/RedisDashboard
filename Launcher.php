<?php
namespace Jamm\RedisDashboard;
class Launcher
{
	public function Start($base_url, $host = 'localhost', $port = 6379, $auth = '')
	{
		$RedisServer = new \Jamm\Memory\RedisServer($host, $port);
		if (!empty($auth)) $RedisServer->Auth($auth);
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
		$Response->setCookie(new \Jamm\HTTP\Cookie('host', $host));
		$Response->setCookie(new \Jamm\HTTP\Cookie('port', $port));
		$Response->setCookie(new \Jamm\HTTP\Cookie('auth', $auth));
		$Response->Send();
	}
}
