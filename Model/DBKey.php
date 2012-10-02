<?php
namespace Jamm\RedisDashboard\Model;
class DBKey
{
	private $Redis;

	public function __construct(\Jamm\Memory\IRedisServer $Redis)
	{
		$this->Redis = $Redis;
	}

	/**
	 * @param string $key_name
	 * @param int $db_index
	 * @return DataStructure\DBKey
	 */
	public function getDBKeyStructure($key_name, $db_index)
	{
		$Key           = $this->getNewDBKeyDataStructure();
		$Key->title    = $key_name;
		$Key->database = $db_index;
		if (!$this->Redis->Select($Key->database))
		{
			return false;
		}
		$Key->type = $this->Redis->Type($Key->title);
		switch ($Key->type)
		{
			case 'list':
				$Key->elements_count = $this->Redis->LLen($Key->title);
				break;
			case 'set':
				$Key->elements_count = $this->Redis->sCard($Key->title);
				$Key->elements       = $this->Redis->sMembers($Key->title);
				break;
			case 'zset':
				$Key->elements_count = $this->Redis->zCard($Key->title);
				break;
			case 'hash':
				$Key->elements_count = $this->Redis->hLen($Key->title);
				break;
			default:
				$Key->value = $this->Redis->Get($Key->title);
		}
		return $Key;
	}

	protected function getNewDBKeyDataStructure()
	{
		return new DataStructure\DBKey();
	}
}
