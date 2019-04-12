<?php
/**
 * Created by Tyler Mckenzie
 * Date: 4/11/2019
 *
 * This class is using PECL's pthreads library.
 * In order to use this class you have to be running php7.0-ztx^
 *
 */

class AsyncTranslate extends Thread
{
	protected $store;
	protected $value;

	protected static $__id = 0;
	protected static $__run_count = 0;
	protected static $__thread_id;

	public function __construct(
		$value,
		Threaded $store
	) {
		$this->value = $value;
		$this->store = $store;
		self::$__id++;
		self::$__thread_id = uniqid(self::$__id . "__");
	}

	public function run()
	{
		self::$__run_count++;
		$thread_id = self::$__thread_id;
		$start_time = microtime(true);
		echo "Thread ({$thread_id}) [" . date("H:i:s") . "]: Times Run (" . self::$__run_count . "): start\n";
		$connector = $this->worker->getConnector();

		if ($connector->open()) {
			$translation = $connector->translate($this->value);
			$connector->close();
		} else {
			echo "Thread ({$thread_id}) [" . date("H:i:s") . "]: Value '{$this->value}' - FAILED!\nERR: Could not open connector.\n";
			return;
		}



		if ($connector->errno !== 0) {
			echo "Thread ({$thread_id}) [" . date("H:i:s") . "]: Value '{$this->value}' - FAILED!\nERR: '{$connector->error}'\n";
			return;
		}

		$end_time = microtime(true);
		$total_time = $end_time - $start_time;

		if ($translation !== false) {
			$this->store[] = "{$this->value} : {$translation}";
			echo "Process ({$thread_id}) [" . date("H:i:s") . "]: Value '{$this->value}' took " . $total_time . "ms to translate into {$translation}.\n";
		} else {
			echo "Process ({$thread_id}) [" . date("H:i:s") . "]: Value '{$this->value}' provided no translation.\n";
		}

		sleep(2);
	}
}
