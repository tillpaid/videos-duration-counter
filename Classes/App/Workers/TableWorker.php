<?php

namespace Classes\App\Workers;

use Classes\Vendor\ConsoleTable\ConsoleTable;

class TableWorker
{
	private $timeWorker;

	private $table;

	public function __construct()
	{
		$this->timeWorker = new TimeWorker();

		$this->table = new ConsoleTable();
		$this->table->setHeaders(['Name', 'Duration', 'Count', 'Average']);
	}

	public function process($filesDuration, $files)
	{
		arsort($filesDuration);

		foreach ($filesDuration as $channel => $duration) {
			$this->setRowData($files, $channel, $duration);
		}

		$this->table->addBorderLine();
		$this->setSummaryRowData($filesDuration, $files);

		return $this->table->getTable();
	}

	private function setRowData($files, $channel, $duration)
	{
		$durationTime = $this->timeWorker->secondsToString($duration);
		$count = count($files[$channel]);
		$avg = $this->timeWorker->secondsToString($duration / $count);

		// It's wrong 'Й' char, I don't know about it, but it's break table spacing
		$channel = str_replace(['Й', 'й', 'ё'], ['Й', 'й', 'ё'], $channel);

		$this->table->addRow([$channel, $durationTime, $count, $avg]);
	}

	private function setSummaryRowData($filesDuration, $files)
	{
		$allDuration = array_sum($filesDuration);
		$allDurationTime = $this->timeWorker->secondsToString($allDuration);
		$allCount = array_sum(array_map('count', $files));
		$allAvg = $this->timeWorker->secondsToString($allDuration / $allCount);

		$this->table->addRow(['All', $allDurationTime, $allCount, $allAvg]);
	}
}
