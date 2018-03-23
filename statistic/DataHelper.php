<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 2018/3/22
 * Time: 下午5:38
 */

namespace statisticHelper;
trait DataHelper
{
	private function getFormatDate($format, $date)
	{
		$date = intval($date / 1000);;
		$date = date($format, $date);

		return $date;
	}


	public function addUser($rid, $name)
	{
		$builder = \Capsule::table("danmu_user");

		if (!$builder->where(["rid" => $rid])->exists()) {
			return $builder->insert(['rid' => $rid, 'name' => $name]);
		}

		return true;
	}
}
