<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 2018/3/21
 * Time: 上午10:15
 */

require "vendor/autoload.php";


class TestSwoole
{
	public $mpid        = 0;
	public $works       = [];
	public $max_process = 2;
	public $new_index   = 0;

	public function __construct()
	{
		try {
			swoole_set_process_name(sprintf("php-ps %s", 'master'));;
			$this->mpid = posix_getpid();
			$this->run();
			$this->processWait();
		} catch (\Exception $exception) {
			die('ALL error:' . $exception->getMessage());
		}
	}

	public function run()
	{
		for ($i = 0; $i < $this->max_process; $i++) {
			$this->createProcess();
		}
	}

	public function createProcess($index = null)
	{
		$process = new swoole_process(function (swoole_process $worker) use (&$index) {
			if (is_null($index)) {
				$index = $this->new_index;
				$this->new_index++;
			}

			swoole_set_process_name(sprintf("php-ps %s", $index));
			for ($j = 0; $j < 10; $j++) {
				$this->checkMpid($worker);
				echo "msg: {$j} \n";
				sleep(1);
			}

		}, false, false);


		$pid = $process->start();
		$this->works[$index] = $pid;

		return $pid;
	}

	public function checkMpid(swoole_process &$worker)
	{
		if(!swoole_process::kill($this->mpid, 0)){
			$worker->exit();
			echo "master process exited, i {$worker['pid']} also quit\n";
		}
	}

	public function rebootProcess($ret)
	{
		$pid = $ret['pid'];
		$index = array_search($pid, $this->works);
		if($index !== false){
			$index = intval($index);
			$new_pid = $this->createProcess($index);
			echo "rebootProcess: {$index}={$new_pid} done\n";
		}

		throw new \Exception("reboot process error : no pid");
	}

	public function processWait()
	{
		while (1) {
			if (count($this->works)) {
				$ret = swoole_process::wait();
				if ($ret) {
					$this->rebootProcess($ret);
				}
			} else {
				break;
			}
		}
	}
}


new TestSwoole();
