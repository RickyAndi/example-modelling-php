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

	public function addSkpItems($skpItems)
	{
		if(is_array($skpItems)) {
			foreach ($skpItems as $skpItem) {
				array_push($this->skpItems, $skpItem);
			}
		} else {
			array_push($this->skpItems, $skpItems);
		}
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
	
	public function getMilestoneMonthTobeDinilai(
		PeriodeParameter $periodeRealisasiBulanan,
		PeriodeParameter $tenggangWaktuRealisasiBulanan,
		$date = null
	)
	{
		$currentDate = date('Y-n-j');
		
		if(null !== $date) {
			$currentDate = $date;
		}
		
		$currentMonth = date('n', strtotime($currentDate));

		if($this->isMonthHavePenilaian($currentMonth)) {
			$indexOfCurrentMonth = $this->getPositionOfMonthInMilestoneMonth($currentMonth);
			
			if($indexOfCurrentMonth === 0) {
				if(!$this->penilaianSpecificMonthAlreadyMarked($currentMonth)) {
					if($this->canDoAnyRealisasiAccordingToNormalDate($periodeRealisasiBulanan, $currentDate) || $this->canDoAnyRealisasiAccordingToExtendedDate($tenggangWaktuRealisasiBulanan, $periodeRealisasiBulanan, $currentDate, $currentMonth)) {

						return $currentMonth;
					}
					return null;
				}				
			}

			$previousMonthIndex = $indexOfCurrentMonth - 1;
			$previousMonth = $this->getMilestoneMonthByIndex($previousMonthIndex);
			
			if(!$this->penilaianSpecificMonthAlreadyMarked($previousMonth)) {
				if($this->canDoAnyRealisasiAccordingToExtendedDate($tenggangWaktuRealisasiBulanan, $periodeRealisasiBulanan, $currentDate, $previousMonth)) {

					return $previousMonth;
				}
			}

			if(!$this->penilaianSpecificMonthAlreadyMarked($currentMonth)) {
				if($this->canDoAnyRealisasiAccordingToNormalDate($periodeRealisasiBulanan, $currentDate) || $this->canDoAnyRealisasiAccordingToExtendedDate($tenggangWaktuRealisasiBulanan, $periodeRealisasiBulanan, $currentDate, $currentMonth)) {
					return $currentMonth;
				}
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

				if($this->canDoAnyRealisasiAccordingToExtendedDate($tenggangWaktuRealisasiBulanan, $periodeRealisasiBulanan, $currentDate, $previousMonth)) {
					return $previousMonth;
				} 
			}
			
			return null;
		}
	}

	public function canDoAnyRealisasiAccordingToNormalDate(PeriodeParameter $periodeRealisasiBulanan, $date = null)
	{
		$currentDate = date("Y-n-j");

		if(null !== $date) {
			$currentDate = $date;
		}

		$currentMonth = date('n', strtotime($currentDate));
		$currentYear = date('Y', strtotime($currentDate));

		$minPeriodeDate = $periodeRealisasiBulanan->value1;
		$maxPeriodeDate = $periodeRealisasiBulanan->value2;

		$periodeMin = "{$currentYear}-{$currentMonth}-{$minPeriodeDate}";
		$periodeMax = "{$currentYear}-{$currentMonth}-{$maxPeriodeDate}";

		if(!checkdate($currentMonth, $maxPeriodeDate, $currentYear)) {
			$maxPeriodeDate = date('t');
			$periodeMax = "{$currentYear}-{$currentMonth}-{$maxPeriodeDate}";
		}
		
		return strtotime($currentDate) >= strtotime($periodeMin) && strtotime($currentDate) <= strtotime($periodeMax); 
	}

	public function canDoAnyRealisasiAccordingToExtendedDate(
		PeriodeParameter $tenggangWaktuRealisasiBulanan,
		PeriodeParameter $periodeRealisasiBulanan,
		$date = null,
		$month = null
	)
	{
		$currentDate = date("Y-n-j");

		if(null !== $date) {
			$currentDate = $date;
		}
		
		$toBeCheckedMonth = date('n');
		if(null !== $month) {
			$toBeCheckedMonth = $month;
		}

		$currentYear = date('Y', strtotime($currentDate));

		$maxPeriodeDate = $periodeRealisasiBulanan->value2;

		$periodeMax = "{$currentYear}-{$toBeCheckedMonth}-{$maxPeriodeDate}";
		
		if(!checkdate($toBeCheckedMonth, $maxPeriodeDate, $currentYear)) {
			$maxPeriodeDate = date('t');
			$periodeMax = "{$currentYear}-{$toBeCheckedMonth}-{$maxPeriodeDate}";
		}

		$extendedDatePeriode = date('Y-n-j', strtotime($periodeMax . "+{$tenggangWaktuRealisasiBulanan->value1} days"));
		
		return strtotime($currentDate) <= strtotime($extendedDatePeriode);
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