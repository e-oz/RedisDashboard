<?php
namespace Jamm\RedisDashboard\Controller;
class Fallback implements \Jamm\MVC\Controllers\IController
{
	private $Redis;
	private $PageRenderer;

	public function __construct(\Jamm\Memory\IRedisServer $Redis,
								\Jamm\MVC\Views\IPageRenderer $PageRenderer)
	{
		$this->Redis        = $Redis;
		$this->PageRenderer = $PageRenderer;
	}

	public function fillResponse(\Jamm\HTTP\IResponse $Response)
	{
		$StatsMonitor = $this->getNewStatsMonitor($this->Redis);
		$stats        = $StatsMonitor->getStats();
		$databases    = $StatsMonitor->getDatabases();
		$template = $this->PageRenderer->renderPage(
			'IndexPage.twig', array('databases' => $databases, 'stats' => $stats));
		$Response->setBody($template);
	}

	/**
	 * @param \Jamm\Memory\IRedisServer $Redis
	 * @return \Jamm\RedisDashboard\Model\StatsMonitor
	 */
	protected function getNewStatsMonitor(\Jamm\Memory\IRedisServer $Redis)
	{
		return new \Jamm\RedisDashboard\Model\StatsMonitor($Redis);
	}
}
