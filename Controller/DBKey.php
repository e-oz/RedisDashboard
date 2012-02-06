<?php
namespace Jamm\RedisDashboard\Controller;

class DBKey implements \Jamm\MVC\Controllers\IController
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
		if (empty($QueryArray[1]))
		{
			$Response->setBody('Empty key name');
			return false;
		}
		if (!isset($QueryArray[3]))
		{
			$Response->setBody('Empty database name');
			return false;
		}
		$key_title  = rawurldecode($QueryArray[1]);
		$db_index   = $QueryArray[3];
		$DBKeyModel = $this->getNewDBKeyModel($this->Redis);
		$DBKey      = $DBKeyModel->getDBKeyStructure($key_title, $db_index);
		if (empty($DBKey))
		{
			$Response->setBody('Can not fetch key info');
			return false;
		}

		$template = $this->PageRenderer->renderPage(
			'Key.twig', array('key' => $DBKey));
		$Response->setBody($template);
	}

	protected function getNewDBKeyModel(\Jamm\Memory\IRedisServer $Redis)
	{
		return new \Jamm\RedisDashboard\Model\DBKey($Redis);
	}
}
