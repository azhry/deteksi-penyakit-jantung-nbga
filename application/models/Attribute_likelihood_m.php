<?php 
defined('BASEPATH') or exit('You are not allowed to access this directly');

class Attribute_likelihood_m extends MY_Model 
{
	public function __construct()
	{
		parent::__construct();
		$this->data['table_name'] 	= 'attribute_likelihood';
		$this->data['primary_key']	= 'id_likelihood';
	}

	public function get_likelihood($cond)
	{
		$this->db->select('*');
		$this->db->from($this->data['table_name']);
		$this->db->join('attribute', 'attribute_likelihood.id_attribute = attribute.id_attribute');
		$this->db->where($cond);
		$query = $this->db->get();
		return $query->result();
	}
}