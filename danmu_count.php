<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 18/3/19
 * Time: 上午8:46
 */

use Illuminate\Database\Capsule\Manager as Capsule;


$capsule = new Capsule;

$capsule->addConnection([
	'driver'    => 'mysql',
	'host'      => "localhost",
	'databases' => 'huya_danmu',
	'username'  => 'root',
	'password'  => 'password',
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

	}

	public function isMessageExist($id)
	{
		return Capsule::table("danmu_message")->where("id", $id)->exists();
	}

	public function addUser($rid, $name)
	{
		return Capsule::table("danmu_user")->updateOrInsert(['rid' => $rid, 'name' => $name]);
	}

	public function addTotalTimer($rid)
	{

	}

	public function add5MinutesTimer($rid, $date)
	{
		return $this->incrementCountTimer("danmu_count_minutes_5", $rid, $date);
	}

	public function addHourTimer($rid, $date)
	{
		return $this->incrementCountTimer("danmu_count_hours", $rid, $date);
	}

	public function countDayFromDatabases($rid, $date)
	{
		return $this->incrementCountTimer("danmu_count_day", $rid, $date, );
	}

	private function incrementCountTimer($table, $rid, $date)
	{
		$builder = Capsule::table("$table");

		$attr = array_filter(["rid" => $rid, 'date' => $date]);

		if ($builder->where($attr)->exists()) {
			return $builder->where($attr)->increment("timer");
		}

		return $builder->insert(array_merge($attr,['timer']));
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
				$dataHelper->addUser($buffer->rid, $buffer->name);
				$dataHelper->addTotalTimer($buffer->rid);
			}
		}
	}
	fclose($handle);
}