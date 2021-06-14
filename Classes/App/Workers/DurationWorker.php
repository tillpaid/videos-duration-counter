<?php

namespace Classes\App\Workers;

use getID3;

class DurationWorker
{
	private $getID3;
	private $cacheWorker;
	private $timeWorker;

	private $startTime;
	private $processAll;
	private $clearRow;

	public function __construct()
	{
		$this->getID3 = new getID3();
		$this->cacheWorker = new CacheWorker();
		$this->timeWorker = new TimeWorker();

		$this->clearRow = str_repeat(' ', exec('tput cols')) . "\r";
	}

	public function process($files)
	{
		$processDuration = $this->processDuration($files);
		$this->cacheWorker->setCache();

		return $processDuration;
	}

	private function processDuration($files)
	{
		$filesDuration = [];

		$this->startTime = time();
		$this->processAll = array_sum(array_map('count', $files));
		$processCounter = 0;
		$cacheCounter = 0;

		$this->processFile($files, $filesDuration, $processCounter, true, $cacheCounter);
		$this->processFile($files, $filesDuration, $processCounter, false, $cacheCounter);

		return $filesDuration;
	}

	private function processFile($files, &$filesDuration, &$processCounter, $inCache, &$cacheCounter)
	{
		foreach ($files as $channelName => $channelFiles) {
			foreach ($channelFiles as $channelFile) {
				if (array_key_exists($channelFile, $this->cacheWorker->cache) == $inCache) {
					$this->addFileInfo($filesDuration, $channelName, $channelFile);

					$processCounter++;
					$inCache && $cacheCounter++;

					$this->printStatus($processCounter, $cacheCounter);
				}
			}
		}
	}

	private function addFileInfo(&$filesDuration, $channelName, $channelFile)
	{
		if (!array_key_exists($channelName, $filesDuration)) {
			$filesDuration[$channelName] = 0;
		}

		if (array_key_exists($channelFile, $this->cacheWorker->cache)) {
			$filesDuration[$channelName] += $this->cacheWorker->cache[$channelFile];
		} else {
			$fileInfo = $this->getID3->analyze($channelFile);

			if (array_key_exists('playtime_seconds', $fileInfo)) {
				$filesDuration[$channelName] += $fileInfo['playtime_seconds'];
				$this->cacheWorker->cache[$channelFile] = $fileInfo['playtime_seconds'];
			}
		}
	}

	private function printStatus($processCounter, $cacheCounter)
	{
		$duration = time() - $this->startTime;
		$totalCounter = $processCounter - $cacheCounter;
		$durationPerFile = $totalCounter ? $duration / $totalCounter : 0;
		$left = ceil($durationPerFile * ($this->processAll - $processCounter));

		$durationTime = $this->timeWorker->secondsToString($duration);
		$leftTime = $this->timeWorker->secondsToString($left);

		$processPercents = ceil($processCounter / ($this->processAll / 100));

		echo sprintf($this->clearRow);
		echo sprintf(" $processCounter / $this->processAll | Processed: {$processPercents}%% | Duration: {$durationTime}s | Left: {$leftTime}s \r");
	}
}