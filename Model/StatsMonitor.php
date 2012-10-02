<?php
namespace Jamm\RedisDashboard\Model;
class StatsMonitor
{
	private $Redis;
	private $info;

	public function __construct(\Jamm\Memory\IRedisServer $Redis)
	{
		$this->Redis = $Redis;
	}

	private function getStatFromInfoKey($key, $title = '')
	{
		if (empty($title))
		{
			$title = ucfirst(str_replace('_', ' ', $key));
		}
		$Stat        = $this->getNewStatDataStructure();
		$Stat->title = $title;
		$Stat->value = $this->info[$key];
		return $Stat;
	}

	protected function getNewStatDataStructure()
	{
		return new DataStructure\Stat();
	}

	public function getStats()
	{
		$info = $this->Redis->Info();
		if (empty($info))
		{
			return false;
		}
		$info = explode("\n", $info);
		foreach ($info as $info_line)
		{
			$exploded_line = explode(':', $info_line);
			if (isset($exploded_line[1]))
			{
				$this->info[trim($exploded_line[0])] = trim($exploded_line[1]);
			}
		}
		$stats                 = array();
		$stats[]               = $this->getStatFromInfoKey('redis_version');
		$stats[]               = $this->getStatFromInfoKey('uptime_in_seconds');
		$stats[]               = $this->getStatFromInfoKey('uptime_in_days');
		$stats[]               = $this->getStatFromInfoKey('connected_clients');
		$stats[]               = $this->getStatFromInfoKey('used_memory_human');
		$stat_last_save        = $this->getNewStatDataStructure();
		$stat_last_save->title = 'Last save time';
		$stat_last_save->value = date('d.m.y H:i:s', $this->info['last_save_time']);
		$stats[]               = $stat_last_save;
		$stats[]               = $this->getStatFromInfoKey('keyspace_hits');
		$stats[]               = $this->getStatFromInfoKey('keyspace_misses');
		return $stats;
	}

	public function getDatabases()
	{
		$databases = array();
		$db_count  = 128; //config get doesn't work
		if ($db_count > 0)
		{
			for ($i = 0; $i < $db_count; $i++)
			{
				if ($this->Redis->Select($i))
				{
					if (($keys_count = $this->Redis->DBsize()))
					{
						$database             = $this->getNewDatabaseDataStructure();
						$database->title      = $i;
						$database->keys_count = $keys_count;
						$databases[]          = $database;
					}
				}
				else
				{
					break;
				}
			}
			$this->Redis->Select(0);
		}
		return $databases;
	}

	protected function getNewDatabaseDataStructure()
	{
		$database = new DataStructure\Database();
		return $database;
	}
}
