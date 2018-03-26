<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 2018/3/26
 * Time: 上午9:25
 */


require_once __DIR__ . "/../vendor/autoload.php";

require_once __DIR__ . "/../intiMysql.php";

use Illuminate\Database\Capsule\Manager as Capsule;
use statisticHelper\StatisticMessage;

function getMessage($startTime, $endTime){
	return Capsule::table("danmu_message")->whereBetween("sendTime", [$startTime, $endTime])->get()->toArray();
}

var_dump($data = getMessage("2018-03-26 00:00:00", "2018-03-26 23:59:59"));
var_dump($data[0]);
var_dump($data[0]->id);

exit();

$dir = "/data/huya_log/v1/gift/";

$messageList = array_diff(scandir($dir), [".", ".."]);

$messageHelper = new StatisticMessage();
$addGiftMessage = function ($buffer) use ($messageHelper) {
	$id = $buffer->id;
	$sendTime = $buffer->time;
	$rid = $buffer->from->rid;
	$content = [
		'name'  => $buffer->name,
		'count' => $buffer->count,
		'price' => $buffer->price,
		'earn'  => $buffer->earn
	];

	$content = json_encode($content);

	if (!$messageHelper->isMessageExist($id)) {
		$messageHelper->addMessage($id, $content, $sendTime, $rid, 2);
	}
};

foreach ($messageList as $list) {
	$fHandle = fopen($dir . $list, "r");
	while ($buffer = fgets($fHandle, 4096)) {
		$buffer = json_decode($buffer);

		$addGiftMessage($buffer);

	}

	fclose($fHandle);
}




//{"type":"gift","time":1522026786869,"name":"虎粮","from":{"name":"〃Styl…","rid":"1059813137"},"count":1,"price":0.1,"earn":0.1,"id":"1fe1c0bcfcdbbffdb5afd06b9170d61c"}

for ($i=15;$i<=26;$i++){
	$startTime = "2018-03-$i 00:00:00";
	$endTime = "2018-03-$i 23:59:59";

	$myMessage = getMessage();

	foreach ($myMessage as $message){
		if($message->type == 1){
			file_put_contents("/data/huya_log/v2/message/xxm-2058731947.2018-03-$i.log");
		}else{

		}
	}
}


function convertGiftMessageToLog($message){
	$content = json_decode($message->content);
	$data = [
		'type'=>"gift",
		"time" => $message->sendTime*1000,
		"name" => $content->name,
		"from" => [
			"name"
		]
	];
}