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

			$this->load->model('preprocess_m');
			$this->preprocess_m->execute();
		}

		if ($this->POST('train'))
		{
			$this->load->model('preprocess_m');
			$data = $this->preprocess_m->get_data(0.7);
			$this->load->model('naive_bayes_m');
			$this->naive_bayes_m->set_validation_data($data['training'], $data['testing']);
			$this->naive_bayes_m->train();

			redirect('user');
			exit;
		}

		$this->data['title'] 	= 'Dashboard' . $this->title;
		$this->data['content']	= 'dashboard';
		$this->template($this->data);
	}

	public function test()
	{
		// $this->load->model('naive_bayes_m');
		// $accuracy = $this->naive_bayes_m->test();
		// echo (number_format($accuracy, 4) * 100) . '%';
		$this->load->model('genetic_algorithm_m');
		$this->genetic_algorithm_m->set_params(13, 0.01, 1000000, 5, ['set' => true, 'fitness' => 0.9]);
		$this->genetic_algorithm_m->execute();
		

		// $data = [
		// 	'age' 		=> 61,
		// 	'sex'		=> 0,
		// 	'cp'		=> 2,
		// 	'trestbps'	=> 131,
		// 	'chol'		=> 201,
		// 	'fbs'		=> 0,
		// 	'restecg'	=> 2,
		// 	'thalach'	=> 151,
		// 	'exang'		=> 0,
		// 	'oldpeak'	=> 2,
		// 	'slope'		=> 2,
		// 	'ca'		=> 2,
		// 	'thal'		=> 6
		// ];

		// $this->load->model('naive_bayes_m');
		// $class = $this->naive_bayes_m->classify($data);
		// arsort($class);
		// $this->dump($class);
		// echo '<h3>Accuracy</h3>';
		// echo ($this->naive_bayes_m->test() * 100) . '%';
	}
}