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
			$this->load->model('naive_bayes_m');
			$this->naive_bayes_m->train();
			redirect('user');
			exit;
		}

		$this->data['title'] 	= 'Dashboard' . $this->title;
		$this->data['content']	= 'dashboard';
		$this->template($this->data);
	}
}