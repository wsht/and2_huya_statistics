<?php

$cmd = 'ps -aux | grep "node /root/wsht/and2_huya_statistics/danmu.js xxm 2058731947" | grep -v grep | awk "{print NR}" | tail -n 1 ';


$runNum = intval(`$cmd`);
echo "current run num is $runNum\n";

if($runNum >= 1){
    echo "runnum is:".$runNum."\n";
    exit(0);
}

$cmd = '(node /root/wsht/and2_huya_statistics/danmu.js xxm 2058731947 >> /data/huya_log/v1/error.log &)';

echo `$cmd`;