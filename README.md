RedisDashboard
==============
Simple tool to see data stored in your Redis DB.  

###Requires
[Jamm\\MVC](https://github.com/jamm/MVC)  
[Jamm\\Memory](https://github.com/jamm/Memory)  

###Example of Front Controller

	<?php
	namespace Jamm\RedisDashboard;
	
	$RedisServer        = new \Jamm\Memory\RedisServer();
    $Request            = new \Jamm\HTTP\Request();
    $RequestParser      = new \Jamm\MVC\Controllers\RequestParser($Request);
    $TemplatesRenderer  = new \Jamm\RedisDashboard\View\TemplatesRenderer();
    $FallbackController = new Controller\Fallback($RedisServer, $TemplatesRenderer);
    $Router             = new \Jamm\MVC\Controllers\Router($RequestParser, $FallbackController);
    $Response           = new \Jamm\HTTP\Response();
    
    $TemplatesRenderer->setBaseURL('/redis');
    $Request->BuildFromInput();
    $Router->addRouteForController('db', new Controller\Database($RedisServer, $RequestParser, $TemplatesRenderer));
    $Router->addRouteForController('key', new Controller\DBKey($RedisServer, $RequestParser, $TemplatesRenderer));
    
    $Response->setHeader('Content-type', 'text/html; charset=UTF-8');
    
    $Controller = $Router->getControllerForRequest();
    $Controller->fillResponse($Response);
    $Response->Send();

###ToDo
Design  
Edit values, run inputed commands  
 
###License
[MIT](http://en.wikipedia.org/wiki/MIT_License)

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do
so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
