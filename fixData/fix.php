<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 2018/3/26
 * Time: 下午1:21
 */


require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../intiMysql.php";

date_default_timezone_set("Asia/Shanghai");

use statisticHelper\StatisticMessage;
use Illuminate\Database\Capsule\Manager as Capsule;


$statisticMessage = new StatisticMessage(new \statisticHelper\StatisticConfig());


class Fix
{
	private $preLogDir = "/root/wsht/and2_huya_statistics/";

	private $preLogList = [
		"message.2018-3-15.log",
		"message.2018-3-16.log",
		"message.2018-3-17.log",
		"message.2018-3-18.log",
		"message.2018-3-19.log",
		"message.2018-3-20.log",
	];

	private $newMessageLogDir = "/data/huya_log/v1/message/";
	private $newGiftLogDir    = "/data/huya_log/v1/gift/";

	private $messageHelper = null;

	public function __construct()
	{
		$this->messageHelper = new StatisticMessage(new \statisticHelper\StatisticConfig());
	}

	public function getUserCtime($rid)
	{
		return Capsule::table("danmu_user")->where("rid", $rid)->get(['ctime'])->toArray();
	}

	public function updateUserCtime($rid, $ctime)
	{
		$ctime = $this->messageHelper->getFormatDate("Y-m-d H:i:s", $ctime);

		return Capsule::table("danmu_user")->where("rid", $rid)->update(compact('ctime'));
	}

	public function intoMessageList($buffer)
	{
		if (!$this->messageHelper->dataHandler($buffer)) {
			echo "failed:\n $buffer  \n";
		}
	}

	public function run_giftMessage()
	{
		foreach ($this->preLogList as $list) {
			$dir = $this->preLogDir . $list;
			echo "$dir is runing \n";
			$handler = fopen($dir, "r");

			while ($buffer = fgets($handler, 4098)) {
				$this->intoMessageList($buffer);
			}

			fclose($handler);
		}

		$dir = $this->newGiftLogDir;
		foreach ($this->getDirList($dir) as $list) {

			echo $dir . $list . " is runing \n";

			$handler = fopen($dir . $list, "r");

			while ($buffer = fgets($handler, 4098)) {
				$this->intoMessageList($buffer);
			}

			fclose($handler);
		}
	}

	public function getDirList($dir)
	{
		return array_diff(scandir($dir), [".", ".."]);
	}
}

$fix = new Fix();

$fix->run_giftMessage();



