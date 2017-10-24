<?php 
defined('BASEPATH') or exit('You are not allowed to access this directly');

class User extends MY_Controller
{
	protected $data;

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		if ($this->POST('import'))
		{
			$file_name = 'raw_data';
			$this->load->model('raw_patient_m');
			$this->raw_patient_m->import($file_name, 'file');

			$response = $this->raw_patient_m->get();
			echo json_encode($response);
			exit;
		}

		if ($this->POST('preprocess'))
		{
			$this->load->model('preprocess_m');
			$this->preprocess_m->execute();

			$response = $this->preprocess_m->get();
			echo json_encode($response);
			exit;
		}

		$this->data['title'] 	= 'Dashboard' . $this->title;
		$this->data['content']	= 'dashboard';
		$this->template($this->data);
	}

	public function train()
	{
		$this->load->model('attribute_m');

		if ($this->POST('train'))
		{
			$response['error'] = false;

			$split_ratio = $this->POST('split_ratio');
			if (!isset($split_ratio))
			{
				$response['error'] = true;
				echo json_encode($response);
				exit;
			}

			$this->load->model('preprocess_m');
			$data = $this->preprocess_m->get_data($split_ratio);
			$this->load->model('naive_bayes_m');
			$this->naive_bayes_m->set_validation_data($data['training'], $data['testing']);
			$this->naive_bayes_m->train();

			$this->load->model('class_prior_m');
			$response['prior'] = $this->class_prior_m->get();

			$this->load->model('attribute_likelihood_m');
			$response['likelihood_pos'] = $this->attribute_likelihood_m->get_likelihood(['class' => 1]);
			$response['likelihood_neg'] = $this->attribute_likelihood_m->get_likelihood(['class' => 0]);

			echo json_encode($response);
			exit;
		}

		$this->data['title'] 	= 'Train Data' . $this->title;
		$this->data['content']	= 'train_data';
		$this->template($this->data);
	}

	public function test()
	{
		if ($this->POST('test'))
		{
			$this->load->model('naive_bayes_m');
			$response['accuracy'] = $this->naive_bayes_m->test();
			$response['cm'] = $this->naive_bayes_m->get_c_matrix();
			echo json_encode($response);
			exit;
		}

		if ($this->POST('optimize'))
		{
			$response['error'] = false;

			$mutation_rate 		= $this->POST('mutation_rate');
			$num_generations	= $this->POST('num_generations');
			$num_populations	= $this->POST('num_populations');

			if (!isset($mutation_rate, $num_populations, $num_generations))
			{
				$response['error'] = true;
				echo json_encode($response);
				exit;
			}

			$set_criteria = $this->POST('set_criteria');
			if (!$set_criteria or empty($set_criteria))
			{
				$set_criteria = ['set' => false];
			}
			else
			{
				$set_criteria = ['set' => true, 'fitness' => $set_criteria];
			}

			$this->load->model('genetic_algorithm_m');
			$this->genetic_algorithm_m->set_params($mutation_rate, $num_generations, $num_populations, $set_criteria);
			$response['fittest_chromosomes'] = $this->genetic_algorithm_m->execute();
			// $fitness = 0;
			$genes = $response['fittest_chromosomes'][count($response['fittest_chromosomes']) - 1]['chromosomes'];
			// foreach ($response['fittest_chromosomes'] as $chromosome)
			// {
			// 	if ($chromosome['fitness'] > $fitness)
			// 	{
			// 		$fitness = $chromosome['fitness'];
			// 		$genes = $chromosome['chromosomes'];
			// 	}
			// }

			$allele = [];
			$this->load->model('attribute_m');
			for ($i = 0; $i < count($genes); $i++)
			{
				$attr = $this->attribute_m->get_row(['id_attribute' => ($i + 1)]);
				if ($attr)
				{
					if ($genes[$i] == 1)
					{
						$allele []= $attr;
					}
					$this->attribute_m->update($attr->id_attribute, ['used' => $genes[$i]]);
				}
			}

			$response['allele'] = $allele;
			echo json_encode($response);
			exit;
		}

		$this->data['title'] 	= 'Test Data' . $this->title;
		$this->data['content']	= 'test_data';
		$this->template($this->data);
	}
}