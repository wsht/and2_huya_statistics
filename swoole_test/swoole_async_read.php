<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 2018/3/21
 * Time: 下午3:20
 */

require __DIR__ . "/../vendor/autoload.php";

$filename = __DIR__ . "/../message.copy.log";


$timer = 0;
//
//swoole_async_read($filename, function ($fileName, $content) use (&$timer) {
//	$timer++;
//	var_dump($content);
//	echo "\n";
//	if ($timer == 3) {
//
//		return false;
//	}
//	sleep(1);
//
//	return true;
//}, 1024, 3 * 1024);


//echo "Installing signal handler...\n";
//pcntl_signal(SIGINT,  function($signo) {
//	echo "signal handler called\n";
//});
//
//
//echo "Dispatching...\n";
//pcntl_signal_dispatch();
//echo "Done\n";




class ReadLogFile
{
	private $buffer = '';

	private $bufferSize = 1024;

	private $readTimer = 0;

	private $lock = false;

	private $worker = [];
	/**
	 * @var swoole_process
	 */
	private $process;
	public function start($fileName){
		$this->createHandlerProcess();
		$this->readFile($fileName);
	}

	public function readFile($fileName)
	{
		swoole_async_read($fileName, function ($fileName, $content) {

			$this->readTimer++;

//			var_dump($this->readTimer);
//			var_dump($content);
			$this->process->push($content);
			return true;
		}, $this->bufferSize);
	}

	public function createHandlerProcess()
	{
		$process = new swoole_process($this->messageCallBack(), false, false);
		$process->useQueue();
		$this->process = $process;
		$pid = $process->start();

		$this->worker[$pid] = $process;
	}

	private function messageCallBack(){
		return function (swoole_process $worker) {
			$recv = $worker->pop();
			file_put_contents(__DIR__."/../process_read.log", $recv, FILE_APPEND);
		};
	}
}

(new ReadLogFile())->start($filename);

