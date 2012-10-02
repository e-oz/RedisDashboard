<?php
namespace Jamm\RedisDashboard\View;
class PageRenderer extends \Jamm\MVC\Views\TwigRenderer
{
	public function __construct()
	{
		parent::__construct(__DIR__.'/Templates');
	}
}
