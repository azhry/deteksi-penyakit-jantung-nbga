<?php 
defined('BASEPATH') or exit('You are not allowed to access this directly');

class Attribute_m extends MY_Model 
{
	public function __construct()
	{
		parent::__construct();
		$this->data['table_name'] 	= 'attribute';
		$this->data['primary_key']	= 'id_attribute';
	}
}