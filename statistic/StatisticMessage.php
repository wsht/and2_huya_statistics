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
				if ($buffer->type == 'online') {
					return true;
				}
				if (!$this->isMessageExist($buffer->id)) {
					$type = 1;
					if ($buffer->type == "gift") {
						$type = 2;
						$content = $this->convertLogDataToContent($buffer);

						$giftId = $this->createOrGetGiftID($buffer);

						$this->addGiftHourTimer($buffer->from->rid, $buffer->time, $giftId, $buffer->count);
						$this->addGiftTotalTimer($buffer->from->rid, $giftId, $buffer->count);
					} elseif ($buffer->type == 'chat') {
						$content = $buffer->content;
					} else {
						return true;
					}

					$this->addMessage($buffer->id, $content, $buffer->time, $buffer->from->rid, $type);
					$this->addUser($buffer->from->rid, $buffer->from->name, $buffer->time, $type);
					$this->addTotalTimer($buffer->from->rid, $type);
					$this->add5MinutesTimer($buffer->from->rid, $buffer->time, $type);
					$this->addHourTimer($buffer->from->rid, $buffer->time, $type);
					$this->addDayTimer($buffer->from->rid, $buffer->time, $type);
				}
			});
			echo "message:".json_encode($buffer)." handler finish\n";
			return true;
		} catch (\Exception $exception) {
			echo $exception->getMessage()."\n";
			return false;
		}
	}

	public function addMessage($id, $content, $sendTime, $rid, $type = 1)
	{
		$sendTime = $this->getFormatDate("Y-m-d H:i:s", $sendTime);

		return Capsule::table("danmu_message")->insert([
			"id"       => $id,
			"content"  => $content,
			"sendTime" => $sendTime,
			"rid"      => $rid,
			'type'     => $type
		]);
	}

	public function isMessageExist($id)
	{
		return Capsule::table("danmu_message")->where(["id" => "$id"])->exists();
	}

	public function addTotalTimer($rid, $type = 1)
	{
		$attr = array_merge(compact('rid'), compact('type'));

		return $this->incrementCountTimer("danmu_count_total", $attr);
	}

	public function add5MinutesTimer($rid, $date, $type = 1)
	{
		$calDate = (int)( $date / 1000 );
		$curMinutes = date("i", $calDate);
		$minutes = intval($curMinutes / 5) * 5;

		$date = $this->getFormatDate("Y-m-d H", $date) . ":" . str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":00";


		$attr = array_merge(
			compact('rid'),
			compact('date'),
			compact('type')
		);

		return $this->incrementCountTimer("danmu_count_minutes_5", $attr);
	}

	public function addHourTimer($rid, $date, $type = 1)
	{
		$date = $this->getFormatDate("Y-m-d H", $date) . ":00:00";

		$attr = array_merge(
			compact('rid'),
			compact('date'),
			compact('type')
		);

		return $this->incrementCountTimer("danmu_count_hours", $attr);
	}

	public function addDayTimer($rid, $date, $type = 1)
	{
		$date = $this->getFormatDate("Y-m-d", $date) . " 00:00:00";

		$attr = array_merge(
			compact('rid'),
			compact('date'),
			compact('type')
		);

		return $this->incrementCountTimer("danmu_count_day", $attr);
	}

	public function addGiftHourTimer($rid, $date, $giftid, $count = 1)
	{
		$date = $this->getFormatDate("Y-m-d H", $date) . ":00:00";
		$attr = array_merge(compact("rid"), compact('date'), compact('giftid'));

		return $this->incrementCountTimer("gift_count_hours", $attr, $count);
	}

	public function addGiftTotalTimer($rid, $giftid, $count = 1)
	{
		$attr = array_merge(compact("rid"), compact('giftid'));

		return $this->incrementCountTimer("gift_count_total", $attr, $count);
	}

	private function incrementCountTimer($table, $attr, $count = 1)
	{
		$builder = Capsule::table("$table");

		$attr = array_filter($attr);

		if ($builder->where($attr)->exists()) {
			return $builder->where($attr)->increment("timer", $count);
		}

		return $builder->insert(array_merge($attr, ['timer' => 1]));
	}

	public function convertLogDataToContent($buffer)
	{
		$data = [
			"name"      => $buffer->name,
			"count"     => $buffer->count,
			"price"     => $buffer->price,
			"earn"      => $buffer->earn,
			"time"      => $buffer->time,
			"user_name" => $buffer->from->name
		];

		return json_encode($data);
	}

	public function createOrGetGiftID($buffer)
	{
		$builder = Capsule::table("gift_detail");

		$attr = ["name" => $buffer->name];
		if ($builder->where($attr)->exists()) {

			return $builder->where($attr)->get(["id"])->toArray()[0]->id;
		}

		$data = ['name' => $buffer->name, "price" => $buffer->price / $buffer->count];

		return $builder->insertGetId($data);
	}
}