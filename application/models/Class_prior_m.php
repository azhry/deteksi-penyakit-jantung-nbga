<?php 
defined('BASEPATH') or exit('You are not allowed to access this directly');

class Class_prior_m extends MY_Model 
{
	public function __construct()
	{
		parent::__construct();
		$this->data['table_name'] 	= 'class_prior';
		$this->data['primary_key']	= 'id_prior';
	}
}