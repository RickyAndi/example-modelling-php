<?php

namespace app\models;

class SkpItemMilestone
{
	private $bulan;

	public function __construct($bulan)
	{
		$this->bulan = $bulan;
	}

	public function getBulan()
	{
		return $this->bulan;
	}
}