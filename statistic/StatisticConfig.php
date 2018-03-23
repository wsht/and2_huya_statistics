<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 2018/3/22
 * Time: 下午5:37
 */
namespace statisticHelper;

class StatisticConfig
{
	const DEFAULT_RUN_TIME_LOG_DIR = __DIR__ . "/";


	private $logDir         = '';
	private $runTimeLog     = '';
	private $runTimerLogDir = '';
	private $name           = '';
	private $date           = '';
	private $roomid         = '';

	/**
	 * @param string $logDir
	 */
	public function setLogDir($logDir)
	{
		$this->logDir = $logDir;

		return $this;
	}

	/**
	 * @return string
	 */
	private function getLogDir()
	{
		return $this->logDir;
	}


	public function getLogName()
	{
		return rtrim($this->getLogDir(), "/") . "/" . $this->getName() . "-" . $this->getRoomid() . "." . $this->getDate() . ".log";
	}

	/**
	 * @param string $runTimeLog
	 */
//	private function setRunTimeLog($runTimeLog)
//	{
//		$this->runTimeLog = $runTimeLog;
//
//		return $this;
//	}

	/**
	 * @return string
	 */
	public function getRunTimeLog()
	{
		return rtrim($this->getRunTimerLogDir(),"/"). "/" . $this->name . "-" . $this->getRoomid() . "." . $this->getDate();
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	public function getName()
	{
		return $this->name;
	}


	/**
	 * @param string $runTimerLogDir
	 */
	public function setRunTimerLogDir($runTimerLogDir)
	{
		$this->runTimerLogDir = $runTimerLogDir;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getRunTimerLogDir()
	{
		return $this->runTimerLogDir ?: self::DEFAULT_RUN_TIME_LOG_DIR;
	}

	/**
	 * @param string $date
	 */
	public function setDate($date)
	{
		$this->date = $date;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDate()
	{
		if (!$this->date) {
			$this->setDate(date("Y-m-d"));
		}

		return $this->date;
	}

	/**
	 * @return string
	 */
	public function getRoomid()
	{
		return $this->roomid;
	}

	/**
	 * @param string $roomid
	 */
	public function setRoomid($roomid)
	{
		$this->roomid = $roomid;

		return $this;
	}
}