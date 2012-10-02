<?php
namespace Jamm\RedisDashboard\Model;
class DBInfo
{
	private $Redis;

	public function __construct(\Jamm\Memory\IRedisServer $Redis)
	{
		$this->Redis = $Redis;
	}

	/**
	 * @param int $db_index
	 * @return bool|DataStructure\Database
	 */
	public function getDBStructure($db_index)
	{
		$Database        = $this->getNewDBStructure();
		$Database->title = $db_index;
		$select          = $this->Redis->Select(intval($Database->title));
		if (!$select)
		{
			return false;
		}
		$Database->keys_count = $this->Redis->DBsize();
		$Database->keys       = $this->Redis->Keys('*');
		$this->Redis->Select(0);
		return $Database;
	}

	protected function getNewDBStructure()
	{
		return new DataStructure\Database();
	}
}