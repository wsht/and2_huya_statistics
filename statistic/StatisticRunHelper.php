<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 2018/3/22
 * Time: 下午5:39
 */

namespace statisticHelper;
use swoole_process;
trait StatisticRunHelper
{

	/**
	 * @var StatisticConfig
	 */
	private $config;

	/**
	 * @var RunTimeData
	 */
	private $runTimeData;

	public function initRunTimeData()
	{
		if (!$this->runTimeData) {
			$this->runTimeData = new RunTimeData();
		}

		if (!file_exists($this->config->getRunTimeLog())) {
			$this->runTimeData->merge(null);
		} else {
			$data = file_get_contents($this->config->getRunTimeLog());
			$data = json_decode($data, true);
			if ($data !== false) {
				$this->runTimeData->merge($data);
			} else {
				$this->runTimeData->merge(null);
			}
		}
	}

	private function checkParentPid($mpid)
	{
		return ProcessHelper::checkMpid($mpid);
	}

	public function run_process(swoole_process &$worker, $parentID)
	{
		$this->initRunTimeData();

		while (true) {
			if (!$this->checkParentPid($parentID)) {

				file_put_contents($this->config->getRunTimeLog(), json_encode($this->runTimeData->getData()));
				$worker->exit();

				return;
			} else {
				$resource = fopen($this->config->getLogName(), "r");
				if (!$resource) {
					//还没有日志文件的产生
					var_dump("no log dir ");
					sleep(10);
					continue;
				}

				//这里不使用foef 来做文件末尾判断是由于其机制有些问题，参见 http://bugs.php.net/bug.php?id=35136&edit=2
				if ($this->runTimeData->isEof() && md5_file($this->config->getLogName()) === $this->runTimeData->getMix()) {
					fclose($resource);

					//转到新的日期
					if ($this->config->getDate() != date("Y-m-d")) {
						var_dump("turn to new date");
						$this->config->setDate(date("Y-m-d"));
						$this->initRunTimeData();
					} else {
						sleep(10);
					}

					continue;
				}

				fseek($resource, $this->runTimeData->getPosition());
				while (( $buffer = fgets($resource, 4096) ) !== false) {
					if ($this->dataHandler($buffer)) {
						$this->runTimeData->setPosition(ftell($resource));
						$this->runTimeData->setMix(md5_file($this->config->getLogName()));
						$this->runTimeData->setEof(false);
						file_put_contents($this->config->getRunTimeLog(), json_encode($this->runTimeData->getData()));
					} else {
						file_put_contents("/data/huya_log/v1/insert_failed.log",$buffer, FILE_APPEND);
					}
				}

				$this->runTimeData->setEof(true);
				file_put_contents($this->config->getRunTimeLog(), json_encode($this->runTimeData->getData()));

				fclose($resource);
			}

			sleep(1);
		}
	}
}