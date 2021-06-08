<?php

namespace Classes\App\Workers;

class FileWorker
{
	private $rootDir;

	public function __construct()
	{
		$this->rootDir = rtrim(isset($_ENV['VIDEO_DIR']) ? $_ENV['VIDEO_DIR'] : '', '/');
	}

	public function process()
	{
		$this->checkRootDir();

		$files = [];
		$this->getFiles($files, $this->rootDir);

		return $files;
	}

	private function checkRootDir()
	{
		if (!file_exists($this->rootDir) || !is_dir($this->rootDir)) {
			die('Video dir not exists or not setting in .env file');
		}
	}

	private function getFiles(&$files, $dir)
	{
		$dirArray = explode('/', $dir);
		$dirName = array_pop($dirArray);

		$ignoreDirs = ['.', '..'];
		$dirItems = scandir($dir);

		foreach ($dirItems as $dirItem) {
			if (in_array($dirItem, $ignoreDirs)) {
				continue;
			}

			if (substr($dirItem, 0, 2) == '._') {
				continue;
			}

			$dirItemFull = "$dir/$dirItem";

			if (is_dir($dirItemFull)) {
				$this->getFiles($files, $dirItemFull);
			} else {
				$files[$dirName][] = $dirItemFull;
			}
		}
	}
}