<?php 
defined('BASEPATH') or exit('You are not allowed to access this directly');

class Raw_patient_m extends MY_Model 
{
	public function __construct()
	{
		parent::__construct();
		$this->data['table_name'] 	= 'raw_patient';
		$this->data['primary_key']	= 'id_patient';
	}

	public function import($file_name, $tag_name = 'userfile')
	{
		if ($_FILES[$tag_name])
		{
			$file_name = $file_name . '.txt';
			$upload_path	= realpath(APPPATH . '../assets/files/');
			$config = [
				'file_name'		=> $file_name,
				'allowed_types'	=> 'txt',
				'upload_path'	=> $upload_path
			];
			$this->load->library('upload');
			$this->upload->initialize($config);
			$this->upload->do_upload($tag_name);
			chmod($upload_path . '/' . $file_name, 0755);
			
			$this->load->model('attribute_m');
			$fields = $this->attribute_m->get();

			$this->load->model('preprocess_m');

			$this->db->query('SET foreign_key_checks = 0');
			$this->preprocess_m->delete_all();
			$this->delete_all();
			$this->db->query('SET foreign_key_checks = 1');

			if ($file = fopen($upload_path . '/' . $file_name, 'r'))
			{
				do
				{
					$line = fgets($file);
					$attrs = explode(',', $line);
					$record = [];
					$fields_num = count($fields);
					if (count($attrs) >= $fields_num)
					{
						for ($i = 0; $i < $fields_num; $i++)
						{
							if ($attrs[$i] == '?')
							{
								$record[$fields[$i]->name] = NULL;
							}
							else
							{
								$record[$fields[$i]->name] = $attrs[$i];
							}
						}
						$this->insert($record);
					}
				}
				while (!feof($file));

				fclose($file);
			}

			@unlink($upload_path . '/' . $file_name);
		}

		return false;
	}
}
