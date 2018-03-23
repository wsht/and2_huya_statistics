<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 2018/3/22
 * Time: 下午5:36
 */

namespace statisticHelper;
use swoole_process;
interface StatisticLogHandlerInterface
{
	public function getName();

	public function run(swoole_process &$worker, $parentID);

	public function dataHandler($buffer);
}
