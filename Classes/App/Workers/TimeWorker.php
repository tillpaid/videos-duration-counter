<?php

namespace Classes\App\Workers;

class TimeWorker
{
	public function secondsToString($seconds)
	{
		$hours = floor($seconds / 3600);
		$mins = floor($seconds / 60 % 60);
		$secs = floor($seconds % 60);

		return sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
	}
}