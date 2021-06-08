<?php

namespace Classes\App\Workers;

class CacheWorker
{
	private $cacheFile = ROOT_PATH . '/TempFiles/cache.json';
	public $cache;

	public function __construct()
	{
		$this->getCache();
	}

	public function getCache()
	{
		if (!file_exists($this->cacheFile)) {
			$this->setCache();
		}

		$this->cache = json_decode(file_get_contents($this->cacheFile), true);
	}

	public function setCache()
	{
		$data = $this->cache ?: [];
		file_put_contents($this->cacheFile, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	}
}