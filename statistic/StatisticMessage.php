<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 2018/3/22
 * Time: 下午5:51
 */

namespace statisticHelper;

use Illuminate\Database\Capsule\Manager as Capsule;
use swoole_process;

class StatisticMessage implements StatisticLogHandlerInterface
{
	use DataHelper, StatisticRunHelper;

	/**
	 * @var StatisticConfig
	 */
	private $config;


	public function __construct(StatisticConfig $conf)
	{
		$this->config = $conf;
	}

	public function run(swoole_process &$worker, $parentID)
	{
		$this->run_process($worker, $parentID);
	}

	public function getName()
	{
		return $this->config->getName();
	}

	public function dataHandler($buffer)
	{
		$buffer = json_decode($buffer);
		if (!$buffer) {
			return false;
		}
		try {
			Capsule::connection()->transaction(function () use ($buffer) {
				if (!$this->isMessageExist($buffer->id)) {
					$this->addMessage($buffer->id, $buffer->content, $buffer->time, $buffer->from->rid);
					$this->addUser($buffer->from->rid, $buffer->from->name);
					$this->addTotalTimer($buffer->from->rid);
					$this->add5MinutesTimer($buffer->from->rid, $buffer->time);
					$this->addHourTimer($buffer->from->rid, $buffer->time);
					$this->addDayTimer($buffer->from->rid, $buffer->time);
				}
			});

			return true;
		} catch (\Exception $exception) {
			return false;
		}
	}

	public function addMessage($id, $content, $sendTime, $rid, $type=1)
	{
		$sendTime = $this->getFormatDate("Y-m-d H:i:s", $sendTime);

		return Capsule::table("danmu_message")->insert([
			"id"       => $id,
			"content"  => $content,
			"sendTime" => $sendTime,
			"rid"      => $rid,
			'type' => $type
		]);
	}

	public function isMessageExist($id)
	{
		return Capsule::table("danmu_message")->where(["id" => "$id"])->exists();
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

	public function addDayTimer($rid, $date)
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

		return $builder->insert(array_merge($attr, ['timer' => 1]));
	}
}