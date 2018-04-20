<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 2018/3/21
 * Time: 下午8:45
 */


require __DIR__ . "/vendor/autoload.php";
require __DIR__."/intiMysql.php";

date_default_timezone_set("Asia/Shanghai");

use statisticHelper\StatisticConfig;
use statisticHelper\StatisticMessage;
use statisticHelper\ProcessHelper;
//use Illuminate\Database\Capsule\Manager as Capsule;

class StatisticProcess
{
	private $fileMark = '';

	public function __construct()
	{
		$this->fileMark = md5_file(__FILE__);
	}

	private function kill($pid)
	{
		`kill $pid`;
	}

	public function getCallAble()
	{
		return function ($myPid) {
			if (md5_file(__FILE__) !== $this->fileMark) {
				$this->kill($myPid);
			}
		};
	}

	public function scriptRunNum()
	{
		$cmd_append = " | grep -v grep | awk '{print NR}' | tail -n 1 ";

		$cmd_ps_name = 'ps aux | grep "statistic-huya-danmu master"' . $cmd_append;
		$cmd_ps_script = 'ps aux | grep statistic.php' . $cmd_append;

//		var_dump($cmd_ps_name);
//		var_dump($cmd_ps_script);

		$result_ps_name = `$cmd_ps_name`;
		$result_ps_script = `$cmd_ps_script`;
//		var_dump($result_ps_script);
//		var_dump($result_ps_name);

		return intval($result_ps_name) + intval($result_ps_script);
	}
}

$process = new StatisticProcess();
if ($process->scriptRunNum() > 1) {
	echo "process is run exit \n";
	exit();
}


$messageConfig = new StatisticConfig();
//这里不应该由外部传入，如果由外部传入，也只是传入规则，不应该具体到某个日志
$messageConfig->setLogDir("/data/huya_log/v1//message");
$messageConfig->setRunTimerLogDir(__DIR__ . "/runtime_log/message");
$messageConfig->setName("xxm");
$messageConfig->setRoomid(13775209);// //2058731947
$messageConfig->setDate(date("Y-m-d"));

$statistic = new StatisticMessage($messageConfig);

//$statistic = new StatisticMessage($messageConfig);
//
$handler = [$statistic];

$callable = $process->getCallAble();

//while (1) {
//	call_user_func($callable, posix_getpid());
//	sleep(1);
//}

$process = new ProcessHelper($handler, $callable);


