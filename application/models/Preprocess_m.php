<?php 
defined('BASEPATH') or exit('You are not allowed to access this directly');

class Preprocess_m extends MY_Model 
{
	public function __construct()
	{
		parent::__construct();
		$this->data['table_name'] 	= 'preprocessed_data';
		$this->data['primary_key']	= 'id_data';
	}

	public function execute()
	{
		$this->load->model('raw_patient_m');
		$data = $this->raw_patient_m->get();
		foreach ($data as $row)
		{
			$row = (array)$row;
			$record = [];
			foreach ($row as $key => $value)
			{
				if ($key == 'id_patient')
				{
					$record[$key] = $value;
					continue;
				}

				// fill in missing values
				if ($value == null)
				{
					if ($key == 'trestbps' or $key == 'thalach' or $key == 'oldpeak' or $key == 'chol')
					{
						$value = $this->attr_avg($key);
					}
					else
					{
						$value = $this->attr_mode($key);
					}
				}

				// data discretization
				if ($key == 'age')
				{
					$value = $this->encode_age($value);
				}
				else if ($key == 'trestbps')
				{
					$value = $this->encode_trestbps($value);
				}
				else if ($key == 'chol')
				{
					$value = $this->encode_chol($value);
				}
				else if ($key == 'thalach')
				{
					$value = $this->encode_thalach($value);
				}
				else if ($key == 'oldpeak')
				{
					$value = $this->encode_oldpeak($value);
				}
				else if ($key == 'num')
				{
					$value = $this->encode_num($value);
				}

				$record[$key] = $value;
			}

			$exist = $this->get_row(['id_patient' => $record['id_patient']]);
			if ($exist)
			{
				$this->update($exist->id_data, $record);
			}
			else
			{
				$this->insert($record);
			}
		}
	}

	private function encode_age($age)
	{
		return $age > 60 ? 1 : 0;
	}

	private function encode_trestbps($trestbps)
	{
		return $trestbps > 130 ? 1 : 0;
	}

	private function encode_thalach($thalach)
	{
		return $thalach > 150 ? 1 : 0;
	}

	private function encode_oldpeak($oldpeak)
	{
		return $oldpeak < 3 ? 1 : 0;
	}

	private function encode_chol($chol)
	{
		return $chol > 200 ? 1 : 0;
	}

	private function encode_num($num)
	{
		return $num > 0 ? 1 : 0;
	}

	private function attr_avg($attr)
	{
		$this->db->select_avg($attr, 'avg');
		$this->db->from('raw_patient');
		$query = $this->db->get();
		return $query->row()->avg;
	}

	private function attr_mode($attr)
	{
		$sql = 'SELECT ' . $attr . ', COUNT(*) AS count FROM raw_patient GROUP BY ' . $attr . ' ORDER BY count DESC';
		$query = $this->db->query($sql);
		$result = $query->row_array();
		return $result[$attr];
	}
}