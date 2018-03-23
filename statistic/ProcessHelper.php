<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 2018/3/22
 * Time: 下午5:35
 */

namespace statisticHelper;

use swoole_process;

class ProcessHelper
{

	/**
	 * @var StatisticLogHandlerInterface[];
	 */
	public $processHandler   = [];
	public $mpid             = 0;
	public $works            = [];
	public $handlerWithIndex = [];
	public $new_index        = 0;

	public $waitCallable = null;

	public function __construct(array $handler, callable $callable = null)
	{
		$this->processHandler = $handler;
		$this->waitCallable = $callable;

		try {
			swoole_set_process_name(sprintf("statistic-huya-danmu %s", "master"));
			$this->mpid = posix_getpid();
			$this->run();
			$this->processWait();
		} catch (\Exception $exception) {
			die('ALL error:' . $exception->getMessage());
		}
	}

	public function run()
	{
		foreach ($this->processHandler as $handler) {
			$this->createProcess($handler);
		}
	}

	public function createProcess(StatisticLogHandlerInterface $handler, $index = null)
	{
		if (is_null($index)) {
			$index = $this->new_index;
			$this->new_index++;
		}

		$process = new swoole_process(function (swoole_process $worker) use ($handler) {
			swoole_set_process_name(sprintf("statistic-huya-danmu %s", $handler->getName()));

			$handler->run($worker, $this->mpid);
		}, false, false);

		$pid = $process->start();
		$this->works[$index] = $pid;
		$this->handlerWithIndex[$index] = $handler;

		return $pid;
	}

	public static function checkMpid($mpid)
	{
		if (!swoole_process::kill($mpid, 0)) {

			return false;
		} else {

			return true;
		}
	}

	public function processWait()
	{
		while (1) {
			if ($this->waitCallable) {

				call_user_func($this->waitCallable, $this->mpid);
			}

			if (count($this->works)) {
				$ret = swoole_process::wait(false);
				if ($ret) {
					$this->rebootProcess($ret);
				}
			} else {
				break;
			}
			sleep(1);
		}
	}

	public function rebootProcess($ret)
	{
		$pid = $ret['pid'];
		$index = array_search($pid, $this->works);
		if ($index !== false) {
			$index = intval($index);
			$this->createProcess($this->handlerWithIndex[$index], $index);
			echo "reboot process $pid \n";

			return;
		}

		throw new \Exception("reboot process error : no pid $pid");
	}
}