<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 2018/3/26
 * Time: 上午8:59
 */


require_once __DIR__."/vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;

//set global Capusle
$capsule = new Capsule;
$capsule->addConnection([
	'driver'    => 'mysql',
	'host'      => "127.0.0.1",
	'database'  => 'huya_danmu',
	'username'  => 'root',
	'password'  => '',
	'charset'   => 'latin1',
	'collation' => 'latin1_swedish_ci',
	'prefix'    => '',
]);


$capsule->setAsGlobal();