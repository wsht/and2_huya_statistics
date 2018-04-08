<?php

$cmd = 'ps -aux | grep "node danmu.js xxm 2058731947" | grep -v grep | awk "{print NR}" | tail -n 1 ';


$runNum = intval(`$cmd`);


if($runNum >= 1){
    echo "runnum is:".$runNum."\n";
    exit(0);
}

$cmd = '(node danmu.js xxm 2058731947 >> /data/huya_log/v1/error.log &)';

echo `$cmd`;