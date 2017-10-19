<?php 
defined('BASEPATH') or exit('You are not allowed to access this directly');

class Naive_bayes_m extends MY_Model 
{
	public function __construct()
	{
		parent::__construct();
		$this->data['table_name'] 	= 'attribute_likelihood';
		$this->data['primary_key']	= 'id_likelihood';
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
				$this->compute_likelihood($attr->name, 0); // yes
				$this->compute_likelihood($attr->name, 1); // no
			}
		}
	}

	public function test()
	{

	}

	public function classify()
	{

	}

	private function compute_prior()
	{
		$this->load->model('preprocess_m');
		$yes = $this->preprocess_m->get(['num' => 0]);
		$y_count = count($yes);
		$no = $this->preprocess_m->get(['num' => 1]);
		$n_count = count($no);

		$total = $y_count + $n_count;
		$y_count = $y_count / $total;
		$n_count = $n_count / $total;
		
		$this->load->model('class_prior_m');
		
		$exist = $this->class_prior_m->get_row(['class' => 0]);
		if ($exist)
		{
			$this->class_prior_m->update($exist->id_prior, ['value' => $y_count]);
		}
		else
		{
			$this->class_prior_m->insert(['class'=> 0, 'value' => $y_count]);
		}

		$exist = $this->class_prior_m->get_row(['class' => 1]);
		if ($exist)
		{
			$this->class_prior_m->update($exist->id_prior, ['value' => $n_count]);
		}
		else
		{
			$this->class_prior_m->insert(['class'=> 1, 'value' => $n_count]);
		}
	}

	private function compute_likelihood($attr, $class)
	{
		$this->load->model('preprocess_m');
		$c_count = $this->preprocess_m->get(['num' => $class]);
		$c_count = count($c_count);

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
			$likelihood = $this->preprocess_m->get([$attr => $row[$attr], 'num' => $class]);
			$likelihood = count($likelihood) / $c_count;
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