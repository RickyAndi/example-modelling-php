<?php

namespace app\models;

class PeriodeParameter
{
	public $value1;
	public $value2;

	public function __construct($value1 = null, $value2 = null)
	{
		$this->value1 = $value1;
		$this->value2 = $value2;
	}

	public static function realisasiBulananPeriode($value1 = null, $value2 = null)
	{
		return new static($value1, $value2);
	}

	public static function tenggangWaktuRealisasiBulanan($value1 = null)
	{
		return new static($value1);
	}
}