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

$fileList = explode("\r\n", $fileList);

var_dump($fileList);