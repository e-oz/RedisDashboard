<?php
namespace Jamm\RedisDashboard\Controller;

class Database implements \Jamm\MVC\Controllers\IController
{
	private $Redis;
	private $RequestParser;
	private $PageRenderer;

	public function __construct(\Jamm\Memory\IRedisServer $Redis,
								\Jamm\MVC\Controllers\IRequestParser $RequestParser,
								\Jamm\MVC\Views\IPageRenderer $PageRenderer)
	{
		$this->Redis             = $Redis;
		$this->RequestParser     = $RequestParser;
		$this->PageRenderer = $PageRenderer;
	}

	/**
	 * @param \Jamm\HTTP\IResponse $Response
	 */
	public function fillResponse(\Jamm\HTTP\IResponse $Response)
	{
		$QueryArray = $this->RequestParser->getQueryArray();
		$DBInfo     = $this->getNewDBInfo($this->Redis);
		$db_index   = $QueryArray[1];
		$Database   = $DBInfo->getDBStructure($db_index);
		if (empty($Database))
		{
			$Response->setBody('Can not select database '.$db_index);
			return false;
		}

		$template = $this->PageRenderer->renderPage('Database.twig', array('database' => $Database));
		$Response->setBody($template);
	}

	protected function getNewDBInfo(\Jamm\Memory\IRedisServer $Redis)
	{
		return new \Jamm\RedisDashboard\Model\DBInfo($Redis);
	}
}
