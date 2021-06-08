<?php

namespace Classes\App;

use Classes\App\Workers\DurationWorker;
use Classes\App\Workers\FileWorker;
use Classes\App\Workers\TableWorker;

class DurationCounter
{
	private $fileWorker;
	private $durationWorker;
	private $tableWorker;

	public function __construct()
	{
		$this->fileWorker = new FileWorker();
		$this->durationWorker = new DurationWorker();
		$this->tableWorker = new TableWorker();
	}

	public function process()
	{
		$files = $this->fileWorker->process();
		$filesDuration = $this->durationWorker->process($files);
		$table = $this->tableWorker->process($filesDuration, $files);

		echo PHP_EOL . PHP_EOL;
		echo $table;
	}
}
