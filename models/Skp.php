<?php

namespace app\models;

use app\models\PeriodeParameter;
use app\models\PenilaianBulanan;

class Skp
{
	private $skpItems = [];
	private $penilaianBulanans = [];

	public function setPenilaianBulanans(array $penilaianBulanans)
	{
		$this->penilaianBulanans = $penilaianBulanans;
	}

	public function addPenilaianBulanans($penilaianBulanans)
	{
		if(is_array($penilaianBulanans)) {
			foreach($penilaianBulanans as $penilaianBulanan) {
				array_push($this->penilaianBulanans, $penilaianBulanan);
			}
			
		} else {
			array_push($this->penilaianBulanans, $penilaianBulanans);
		}
	}

	public function getPenilaianBulanans()
	{
		return $this->penilaianBulanans;
	}

	public function setSkpItems(array $skpItems)
	{
		$this->skpItems = $skpItems;
	}

	public function getSkpItems()
	{
		return $this->skpItems;
	}

	public function getPreviousPenilaianMonth($month)
	{
		$availableMilestoneMonths = $this->getAvailableMilestoneMonths();
	}

	public function canDoAnyPenilaian()
	{
		
	}

	public function isMonthHavePenilaian($month)
	{
		return in_array($month, $this->getAvailableMilestoneMonths());
	}

	public function getPositionOfMonthInMilestoneMonth($month)
	{
		return array_search($month, $this->getAvailableMilestoneMonths());
	}

	public function getMilestoneMonthByIndex($index)
	{
		$milestonesMonths = $this->getAvailableMilestoneMonths();
		return $milestonesMonths[$index];
	}

	public function getAvailableMilestoneMonths()
	{
		$availableMilestoneMonths = [];

		$milestonesMonths = array_map(function($skpItem) {
			return $skpItem->getAvailableMilestoneMonths();
		}, $this->getSkpItems());

		foreach($milestonesMonths as $months) {
			foreach($months as $month) {
				array_push($availableMilestoneMonths, $month);
			}
		}

		$availableMilestoneMonths = array_unique($availableMilestoneMonths);

		sort($availableMilestoneMonths);

		return $availableMilestoneMonths;
	}
	
	public function getMilestoneMonthTobeDinilai($month = null)
	{
		$currentMonth = date('n');

		if(null !== $month) {
			$currentMonth = $month;
		}
		
		if($this->isMonthHavePenilaian($currentMonth)) {
			$indexOfCurrentMonth = $this->getPositionOfMonthInMilestoneMonth($currentMonth);
			
			if($indexOfCurrentMonth === 0) {
				if(!$this->penilaianSpecificMonthAlreadyMarked($currentMonth)) {
					return $currentMonth;	
				}

				return null;				
			}

			$previousMonthIndex = $indexOfCurrentMonth - 1;
			$previousMonth = $this->getMilestoneMonthByIndex($previousMonthIndex);

			if(!$this->penilaianSpecificMonthAlreadyMarked($previousMonth)) {
				return $previousMonth;	
			}

			if(!$this->penilaianSpecificMonthAlreadyMarked($currentMonth)) {
				return $currentMonth;	
			}

			return null;

		} else {

			if($currentMonth == 1) {
				return null;
			}

			$previousMonth = $currentMonth - 1;

			if($this->isMonthHavePenilaian($previousMonth)) {
				if($this->penilaianSpecificMonthAlreadyMarked($previousMonth)) {
					return null;
				}

				return $previousMonth;
			}
			
			return null;
		}
	}

	public function getPenilaianForSpecificMonth($month)
	{
		$filteredPenilaian = array_values(array_filter($this->getPenilaianBulanans(), 
			function($penilaianBulanan) use ($month) {
				return $penilaianBulanan->getBulan() == $month;
		}));

		if(!count($filteredPenilaian)) {
			return null;
		}

		return $filteredPenilaian[0];
	}

	public function hasPenilaianForSpecificMonth($month)
	{
		$penilaian = $this->getPenilaianForSpecificMonth($month);

		if(null == $penilaian) {
			return false;
		}

		return true;
	}

	public function penilaianSpecificMonthAlreadyMarked($month)
	{
		$penilaian = $this->getPenilaianForSpecificMonth($month);

		if(null == $penilaian) {
			return false;
		}

		if(!$penilaian->isAlreadyMarked()) {
			return false;
		}

		return true;
	}
}