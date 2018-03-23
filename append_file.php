<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 2018/3/22
 * Time: 下午5:03
 */



while(true){
	file_put_contents(__DIR__."/message.copy.log", "{\"type\":\"gift\",\"time\":1521513648691,\"name\":\"虎粮\",\"from\":{\"name\":\"别碰卡尔的…\",\"rid\":\"1630811888\"},\"count\":1,\"price\":0.1,\"earn\":0.1,\"id\":\"73f5c09c92130de500ae0bf5f80265f1\"}", FILE_APPEND);
	usleep(100000);
}
