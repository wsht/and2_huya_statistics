<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 2018/3/22
 * Time: 下午5:37
 */

namespace statisticHelper;

class RunTimeData
{
	private $data = [];

	/**
	 * @return int
	 */
	public function getPosition()
	{
		return $this->data["position"];
	}

	/**
	 * @param int $position
	 */
	public function setPosition($position)
	{
		$this->data['position'] = $position;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getMix()
	{
		return $this->data['mix'];
	}

	/**
	 * @param string $mix
	 */
	public function setMix($mix)
	{
		$this->data['mix'] = $mix;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isEof()
	{
		return $this->data['eof'];
	}

	/**
	 * @param bool $eof
	 */
	public function setEof($eof)
	{
		$this->data['eof'] = $eof;

		return $this;
	}

	public function defaultData()
	{
		return [
			'position' => 0,
			'mix'      => '',
			'eof'      => false
		];
	}

	public function merge(array $data = null)
	{
		if (is_null($data) || !is_array($data)) {
			$data = [];
		}

		$this->data = array_merge($this->defaultData(), $data);
	}

	public function getData()
	{
		return $this->data;
	}
}
