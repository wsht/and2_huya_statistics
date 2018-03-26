<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 2018/3/26
 * Time: 上午8:57
 */

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../intiMysql.php";

date_default_timezone_set("Asia/Shanghai");

use statisticHelper\StatisticMessage;
use Illuminate\Database\Capsule\Manager as Capsule;

$statisticMessage = new StatisticMessage(new \statisticHelper\StatisticConfig());


$dir = "/data/huya_log/v1/message/";
//修理message ctime
$messageList = array_diff(scandir($dir), [".", ".."]);


function getUserCtime($rid)
{
	return Capsule::table("danmu_user")->where("rid", $rid)->get(['ctime'])->toArray();
}

function updateUserCtime($rid, $ctime, StatisticMessage $statisticMessage)
{
	$ctime = $statisticMessage->getFormatDate("Y-m-d H:i:s", $ctime);

	return Capsule::table("danmu_user")->where("rid", $rid)->update(compact('ctime'));

}

foreach ($messageList as $list) {
	$handle = fopen($dir . $list, 'r');
	while ($buf = fgets($handle, 4096)) {
		$buf = json_decode($buf);
		$userCtime = getUserCtime($buf->from->rid);
		if($userCtime){
			if(is_null($userCtime[0]->ctime)){
				updateUserCtime($buf->from->rid, $buf->time, $statisticMessage);
				echo "user {$buf->from->rid} add in time ".$statisticMessage->getFormatDate("Y-m-d H:i:s", $buf->time)."\n";
			}
		}else{
			$statisticMessage->addUser($buf->from->rid, $buf->from->name, $buf->time);
			echo "create user {$buf->from->rid} add in time ".$statisticMessage->getFormatDate("Y-m-d H:i:s", $buf->time)."\n";
		}
	}
}