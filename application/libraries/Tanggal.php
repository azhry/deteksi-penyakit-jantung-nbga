<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tanggal
{
	private $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
	private $abbreviated_days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
	private $english_days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
	private $abbreviated_english_days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

	private $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
	private $english_months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

	public function __construct()
	{
		date_default_timezone_set('Asia/Jakarta');
	}

	public function now($time = false, $abbreviated = false)
	{
		$date = DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
		if ($abbreviated)
		{
			$day = $this->abbreviated_days[array_search($date->format('D'), $this->abbreviated_english_days)];
		}
		else
		{
			$day = $this->days[array_search($date->format('D'), $this->abbreviated_english_days)];
		}

		$month = $this->months[intval($date->format('m')) - 1];
		$result = $day . ', ' . $date->format('d') . ' ' . $month . ' ' . $date->format('Y');
		if ($time)
		{
			$result = $date->format('H:i A') . ' - ' . $result;
		}

		return $result;
	}

	public function convert_date($date_string, $abbreviated = false)
	{
		$split = explode(' ', $date_string);
		$date = DateTime::createFromFormat('Y-m-d', $split[0]);
		
		if ($abbreviated)
		{
			$day = $this->abbreviated_days[array_search($date->format('D'), $this->abbreviated_english_days)];
		}
		else
		{
			$day = $this->days[array_search($date->format('D'), $this->abbreviated_english_days)];
		}

		$month = $this->months[intval($date->format('m')) - 1];
		$result = $day . ', ' . $date->format('d') . ' ' . $month . ' ' . $date->format('Y');
		if (count($split) > 1)
		{
			$date = DateTime::createFromFormat('Y-m-d H:i:s', $split[0] . ' ' . $split[1]);
			$result = $date->format('H:i A') . ' - ' . $result;
		}

		return $result;
	}
}