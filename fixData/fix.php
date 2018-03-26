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

		$dirList = [$this->newMessageLogDir, $this->newGiftLogDir];
		foreach ($dirList as $dir) {
//			$dir = $this->newGiftLogDir;
			foreach ($this->getDirList($dir) as $list) {

				echo $dir . $list . " is runing \n";

				$handler = fopen($dir . $list, "r");

				while ($buffer = fgets($handler, 4098)) {
					$this->intoMessageList($buffer);
				}

				fclose($handler);
			}
		}
	}

	public function updateUserCreateTime()
	{
		$stime = 15;
		$curTime = date("d");

		for ($i = $stime; $i <= $curTime; $i++) {
			$s = "2018-03-$i 00:00:00";
			$e = "2018-03-$i 23:59:59";
			echo "start $s \n";
			foreach ($this->getFromMessage($s, $e) as $item) {
				$rid = $item[0];
//				var_dump($item);
//				sleep(1);
				$sendTime = $item[1];
				$userCtime = $this->getUserCtime($rid);

				if ($userCtime) {
					if (is_null($userCtime[0]->ctime)) {
						$this->updateUserCtime($rid, $sendTime);
						echo "user {$rid} add in time " . $sendTime . "\n";
					}
				} else {
					echo "user not exist";
				}
			}
		}
	}

	public function getFromMessage($s, $e)
	{
		$result = Capsule::table("danmu_message")->whereBetween("sendTime", [$s, $e])->orderBy("sendTime")->get();

		$result = $result->toArray();
		foreach ($result as $res) {
			yield [$res->rid, $res->sendTime];
		}
	}

	public function getDirList($dir)
	{
		return array_diff(scandir($dir), [".", ".."]);
	}
}

$fix = new Fix();
$fix->run_giftMessage();
//$fix->updateUserCreateTime();



