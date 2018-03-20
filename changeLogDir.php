<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 2018/3/20
 * Time: 下午2:48
 */


$targetDir = "/root/wsht/and2_huya_statistics/";

$fileListCmd = "ls $targetDir | egrep 'message.*.log'";
$fileList = `$fileListCmd`;
$fileList = explode("\n", $fileList);

class TargetDirDefine
{
	const ROOT_DIR    = '/data/huya_log/v1';
	const MESSAGE_DIR = self::ROOT_DIR . "/message/";
	const ONLINE_DIR  = self::ROOT_DIR . "/online/";
	const GIFT_DIR    = self::ROOT_DIR . "/gift/";

	static function writeLog($dir, $message, $timestamp)
	{
		$timestamp = intval($timestamp / 1000);
		$date = date("Y-m-d", $timestamp);
		$logName = "xxm-2058731947." . $date . ".log";
		$logDir = $dir . $logName;

		if (is_file($logDir)) {
			unlink($logDir);
		}

		file_put_contents($logDir, $message, FILE_APPEND);
	}
}


foreach ($fileList as $file) {
	$filePath = $targetDir . $file;
	echo "copy $filePath start\n";
	$handle = fopen($filePath, "r");
	while ($buffer = fgets($handle, 4096)) {
		$message = json_decode($buffer);
		switch ($message->type) {
			case "chat":
				TargetDirDefine::writeLog(TargetDirDefine::MESSAGE_DIR, $buffer, $message->time);
				break;
			case "gift":
				TargetDirDefine::writeLog(TargetDirDefine::GIFT_DIR, $buffer, $message->time);
				break;
			case "online":
				TargetDirDefine::writeLog(TargetDirDefine::ONLINE_DIR, $buffer, $message->time);
				break;
		}
	}
	echo "copy $filePath finish \n\n";
	fclose($handle);
}

$cmd = "ps -aux | grep node | grep -v grep | awk '{print $2}' | xargs kill ";

//var_dump(`$cmd`);

$startNode = "(node " . $targetDir . "danmu.js xxm 2058731947 >> " . TargetDirDefine::ROOT_DIR . "error.log &)";
var_dump($startNode);