<?php

namespace app\models;

class SkpItem
{
	private $nama;
	private $skpItemMilestones;

	public function __construct($nama)
	{
		$this->nama = $nama;
	}
	
	public function setSkpItemMilestones(array $skpItemMilestones)
	{
		$this->skpItemMilestones = $skpItemMilestones;
	}

	public function getSkpItemMilestones()
	{
		return $this->skpItemMilestones;
	}

	public function getAvailableMilestoneMonths()
	{
		return array_map(function($skpItemMilestone) {
			return $skpItemMilestone->getBulan();
		}, $this->getSkpItemMilestones());
	}
}