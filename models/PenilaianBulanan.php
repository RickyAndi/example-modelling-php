<?php

namespace app\models;

class PenilaianBulanan
{
	private $bulan;
	private $nilaiSkp;
	private $telahDinilai;

	public function __construct($bulan)
	{
		$this->bulan = $bulan;
		$this->telahDinilai = 0;
	}

	public function getBulan()
	{
		return $this->bulan;
	}

	public function makeAsAlreadyMarked()
	{
		$this->telahDinilai = 1;
	}

	public function isAlreadyMarked()
	{
		return $this->telahDinilai == 1;
	}
}