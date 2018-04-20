<?php

$roomid = "13775209";

$cmd = 'ps -aux | grep "node /root/wsht/and2_huya_statistics/danmu.js xxm '.$roomid.'" | grep -v grep | awk "{print NR}" | tail -n 1 ';


$runNum = intval(`$cmd`);
echo "current run num is $runNum\n";

if($runNum >= 1){
    echo "runnum is:".$runNum."\n";
    exit(0);
}else
{
    $cmd = '(node /root/wsht/and2_huya_statistics/danmu.js xxm '.$roomid.' >> /data/huya_log/v1/error.log &)';

    echo `$cmd`;
}