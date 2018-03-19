<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 18/3/19
 * Time: 上午8:46
 */
require "vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;


$capsule = new Capsule;

$capsule->addConnection([
	'driver'    => 'mysql',
	'host'      => "127.0.0.1",
	'database'  => 'huya_danmu',
	'username'  => 'root',
	'password'  => 'root',
	'charset'   => 'latin1',
	'collation' => 'latin1_swedish_ci',
	'prefix'    => '',
]);


$capsule->setAsGlobal();


$fileList = [
	"./message.2018-3-15.log",
	"./message.2018-3-16.log",
	"./message.2018-3-17.log"
];


class DataHelper
{
	public function addMessage($id, $content, $sendTime, $rid)
	{
		$sendTime = $this->getFormatDate("Y-m-d H:i:s", $sendTime);

		return Capsule::table("danmu_message")->insert([
			"id" => $id,
			"content" =>$content,
			"sendTime" => $sendTime,
			"rid" => $rid
		]);
	}

	public function isMessageExist($id)
	{
		return Capsule::table("danmu_message")->where(["id"=>"$id"])->exists();
	}

	public function addUser($rid, $name)
	{
		return Capsule::table("danmu_user")->updateOrInsert(['rid' => $rid, 'name' => $name]);
	}

	public function addTotalTimer($rid)
	{
		return $this->incrementCountTimer("danmu_count_total", $rid);
	}

	public function add5MinutesTimer($rid, $date)
	{
		$calDate = (int)( $date / 1000 );
		$curMinutes = date("i", $calDate);
		$minutes = intval($curMinutes / 5) * 5;

		$date = $this->getFormatDate("Y-m-d H", $date) . ":" . str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":00";

		return $this->incrementCountTimer("danmu_count_minutes_5", $rid, $date);
	}

	public function addHourTimer($rid, $date)
	{
		$date = $this->getFormatDate("Y-m-d H", $date) . ":00:00";

		return $this->incrementCountTimer("danmu_count_hours", $rid, $date);
	}

	public function countDayFromDatabases($rid, $date)
	{
		$date = $this->getFormatDate("Y-m-d", $date) . " 00:00:00";

		return $this->incrementCountTimer("danmu_count_day", $rid, $date);
	}

	private function incrementCountTimer($table, $rid, $date = null)
	{
		$builder = Capsule::table("$table");

		$attr = array_filter(["rid" => $rid, 'date' => $date]);

		if ($builder->where($attr)->exists()) {
			return $builder->where($attr)->increment("timer");
		}

		return $builder->insert(array_merge($attr, ['timer']));
	}


	private function getFormatDate($format, $date)
	{
		$date = (int)( $date / 1000 );

		$date = date($format, $date);

		return $date;
	}
}

$dataHelper = new DataHelper();

foreach ($fileList as $file) {
	$handle = fopen($file, "r");
	while ($buffer = fgets($handle, 4096)) {
		$buffer = json_decode($buffer);
		if ($buffer->type === 'chat') {
			if (!$dataHelper->isMessageExist($buffer->id)) {
				$dataHelper->addMessage($buffer->id, $buffer->content, $buffer->time, $buffer->from->rid);
				$dataHelper->addUser($buffer->from->rid, $buffer->from->name);
				$dataHelper->addTotalTimer($buffer->from->rid);
			}
		}
	}
	fclose($handle);
}