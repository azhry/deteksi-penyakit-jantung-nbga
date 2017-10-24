<?php 
defined('BASEPATH') or exit('You are not allowed to access this directly');

class Naive_bayes_m extends MY_Model 
{
	private static $train_data;
	private static $test_data;

	private $precision_pos;
	private $precision_neg;
	private $recall_pos;
	private $recall_neg;

	public function __construct()
	{
		parent::__construct();
		$this->data['table_name'] 	= 'attribute_likelihood';
		$this->data['primary_key']	= 'id_likelihood';
		
		self::$train_data 	= $this->session->userdata('train_data');
		self::$test_data 	= $this->session->userdata('test_data');

		$this->precision_pos 	= 0;
		$this->precision_neg 	= 0;
		$this->recall_pos		= 0;
		$this->recall_neg		= 0;
	}

	public function set_validation_data($train_data, $test_data)
	{
		self::$train_data 	= $train_data;
		self::$test_data 	= $test_data;
	}

	public function get_c_matrix()
	{
		$data['precision_pos'] 	= $this->precision_pos;
		$data['precision_neg'] 	= $this->precision_neg;
		$data['recall_pos']		= $this->recall_pos;
		$data['recall_neg']		= $this->recall_neg;
		return $data;
	}

	public function train()
	{
		$this->compute_prior();

		$this->load->model('attribute_m');
		$attribute = $this->attribute_m->get();
		foreach ($attribute as $attr)
		{
			if ($attr->name != 'num')
			{
				$this->compute_likelihood($attr->name, 1); // yes
				$this->compute_likelihood($attr->name, 0); // no
			}
		}
	}

	public function test($slice_attr = [])
	{
		$this->load->model('preprocess_m');
		$data = self::$test_data;
		$total = count($data);
		$cm['tp'] = 0;
		$cm['tn'] = 0;
		$cm['fp'] = 0;
		$cm['fn'] = 0;
		foreach ($data as $row)
		{
			$row = (array)$row;
			$class = $this->classify($row, $slice_attr);
			arsort($class);
			$class = array_keys($class);
			if ($class[0] == 'yes')
			{
				$class = 1;
			}
			else
			{
				$class = 0;
			}

			if ($class == $row['num'])
			{
				if ($class == 1)
				{
					$cm['tp']++;
				}
				else
				{
					$cm['tn']++;
				}
			}
			else
			{
				if ($class == 1)
				{
					$cm['fp']++;
				}
				else
				{
					$cm['fn']++;
				}
			}
		}
		$this->precision_pos 	= ($cm['tp'] + $cm['fp']) > 0 ? $cm['tp'] / ($cm['tp'] + $cm['fp']) : 0;
		$this->precision_neg 	= ($cm['tn'] + $cm['fn']) > 0 ? $cm['tn'] / ($cm['tn'] + $cm['fn']) : 0;
		$this->recall_pos		= ($cm['tp'] + $cm['fn']) > 0 ? $cm['tp'] / ($cm['tp'] + $cm['fn']) : 0;
		$this->recall_neg		= ($cm['tn'] + $cm['fp']) > 0 ? $cm['tn'] / ($cm['tn'] + $cm['fp']) : 0;

		$accuracy = ($cm['tp'] + $cm['tn']) / $total;
		return $accuracy;
	}

	public function classify($data, $slice_attr = [])
	{
		$this->load->model('class_prior_m');
		$class = [
			'yes' 	=> $this->class_prior_m->get_row(['class' => 1])->value,
			'no'	=> $this->class_prior_m->get_row(['class' => 0])->value
		];

		$this->load->model('preprocess_m');
		$this->load->model('attribute_m');
		foreach ($data as $key => $value)
		{
			if ($key == 'num' or in_array($key, $slice_attr))
			{
				continue;
			}

			$attr = $this->attribute_m->get_row(['name' => $key]);
			if (!$attr)
			{
				continue;
			}

			if ($key == 'age')
			{
				$value = $this->preprocess_m->encode_age($value);
			}
			else if ($key == 'trestbps')
			{
				$value = $this->preprocess_m->encode_trestbps($value);
			}
			else if ($key == 'chol')
			{
				$value = $this->preprocess_m->encode_chol($value);
			}
			else if ($key == 'thalach')
			{
				$value = $this->preprocess_m->encode_thalach($value);
			}
			else if ($key == 'oldpeak')
			{
				$value = $this->preprocess_m->encode_oldpeak($value);
			}

			$likelihood_yes = $this->get_row(['id_attribute' => $attr->id_attribute, 'value' => $value, 'class' => 1]);
			if (!$likelihood_yes)
			{
				continue;
			}
			$likelihood_yes = $likelihood_yes->likelihood;

			$likelihood_no = $this->get_row(['id_attribute' => $attr->id_attribute, 'value' => $value, 'class' => 0]);
			if (!$likelihood_no)
			{
				continue;
			}
			$likelihood_no = $likelihood_no->likelihood;

			$class['yes'] 	*= $likelihood_yes;
			$class['no'] 	*= $likelihood_no;
		}

		return $class;
	}

	private function compute_prior()
	{
		$this->load->model('preprocess_m');
		$y_count = 0;
		$n_count = 0;
		foreach (self::$train_data as $td)
		{
			if ($td->num == 1) $y_count++;
			else $n_count++;
		}
		// $yes = $this->preprocess_m->get(['num' => 1]);
		// $y_count = count($yes);
		// $no = $this->preprocess_m->get(['num' => 0]);
		// $n_count = count($no);

		$total = $y_count + $n_count;
		$y_count = $y_count / $total;
		$n_count = $n_count / $total;
		
		$this->load->model('class_prior_m');
		
		$exist = $this->class_prior_m->get_row(['class' => 1]);
		if ($exist)
		{
			$this->class_prior_m->update($exist->id_prior, ['value' => $y_count]);
		}
		else
		{
			$this->class_prior_m->insert(['class'=> 1, 'value' => $y_count]);
		}

		$exist = $this->class_prior_m->get_row(['class' => 0]);
		if ($exist)
		{
			$this->class_prior_m->update($exist->id_prior, ['value' => $n_count]);
		}
		else
		{
			$this->class_prior_m->insert(['class'=> 0, 'value' => $n_count]);
		}
	}

	private function compute_likelihood($attr, $class)
	{
		$this->load->model('preprocess_m');
		$c_count = 0;
		foreach (self::$train_data as $td)
		{
			if ($td->num == $class) $c_count++;
		}
		// $c_count = $this->preprocess_m->get(['num' => $class]);
		// $c_count = count($c_count);

		$this->db->select($attr);
		$this->db->from('preprocessed_data');
		$this->db->group_by($attr);
		$query = $this->db->get();
		$result = $query->result_array();

		$this->load->model('attribute_m');
		$attribute = $this->attribute_m->get_row(['name' => $attr]);
		$id_attribute = $attribute->id_attribute;

		foreach ($result as $row)
		{
			$likelihood = 0;
			foreach (self::$train_data as $td)
			{
				$td = (array)$td;
				if ($td[$attr] == $row[$attr] && $td['num'] == $class) $likelihood++;
			}
			// $likelihood = $this->preprocess_m->get([$attr => $row[$attr], 'num' => $class]);
			// $likelihood = count($likelihood) / $c_count;
			$likelihood = $likelihood / $c_count;
			$record = [
				'id_attribute' 	=> $id_attribute ? $id_attribute : 0,
				'value'			=> $row[$attr],
				'class'			=> $class,
				'likelihood'	=> $likelihood
			];
			$exist = $this->get_row(['id_attribute' => $id_attribute ? $id_attribute : 0, 'class' => $class, 'value' => $row[$attr]]);
			if ($exist)
			{
				$this->update($exist->id_likelihood, $record);
			}
			else
			{
				$this->insert($record);
			}
		}
	}
}