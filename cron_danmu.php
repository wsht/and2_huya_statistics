<?php

$cmd = 'ps -aux | grep "node danmu.js xxm 2058731947" | grep -v grep | awk "{print NR}" | tail -n 1 ';


$runNum = intval(`$cmd`);


if($runNum >= 1){
    exit(0);
}

$cmd = '(node danmu.js xxm 2058731947 >> /data/huya_log/v1/error.log &)';

`$cmd`;