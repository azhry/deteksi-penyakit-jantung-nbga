<?php 
defined('BASEPATH') or exit('You are not allowed to access this directly');

class Genetic_algorithm_m extends MY_Model 
{
	private $mutation_rate;
	private $chromosome_length;
	private $num_generations;
	private $num_populations;
	private $chromosomes;
	private $stopping_criteria;

	public function __construct()
	{
		parent::__construct();
		$this->data['table_name'] 	= 'attribute';
		$this->data['primary_key']	= 'id_attribute';
	}

	public function set_params($mutation_rate, $num_generations, $num_populations, $stopping_criteria = ['set' => false])
	{
		$this->chromosome_length 	= 13;
		$this->mutation_rate 		= $mutation_rate;
		$this->num_generations 		= $num_generations;
		$this->num_populations 		= $num_populations;
		$this->stopping_criteria	= $stopping_criteria;
	}

	public function execute()
	{
		$computed_population = [];
		$fittest_chromosomes = [];
		
		for ($i = 0; $i < $this->num_generations; $i++)
		{
			$this->initialize_population();
			$computed_population = $this->compute_fitness();
			$this->reproduction($computed_population);
			$this->mutation();
			$arr = [];
			foreach ($computed_population as $key => $value)
			{
				$arr[$key] = $value['fitness'];
			}

			array_multisort($arr, SORT_DESC, $computed_population);
			
			$fittest_chromosomes []= $computed_population[0];
			if ($this->stopping_criteria['set'] == true)
			{
				if ($computed_population[0]['fitness'] >= $this->stopping_criteria['fitness'])
					break;
			}
		}

		return $fittest_chromosomes;
	}

	private function initialize_population()
	{
		$this->chromosomes = [];
		for ($i = 0; $i < $this->num_populations; $i++)
		{
			$this->chromosomes []= [rand(0, 1), rand(0, 1), rand(0, 1), rand(0, 1), rand(0, 1), rand(0, 1), rand(0, 1), rand(0, 1), rand(0, 1), rand(0, 1), rand(0, 1), rand(0, 1), rand(0, 1)];
		}
	}

	private function compute_fitness()
	{
		ini_set('memory_limit', -1);
		ini_set('max_execution_time', 0);
		$computed_population = [];
		$this->load->model('naive_bayes_m');
		for ($i = 0; $i < $this->num_populations; $i++)
		{
			$filter_attr = [];
			for ($j = 0; $j < count($this->chromosomes[$i]); $j++)
			{
				if ($this->chromosomes[$i][$j] == 0)
				{
					$attr = $this->get_row(['id_attribute' => ($j + 1)]);
					$filter_attr []= $attr->name;
				}
			}
			$accuracy = $this->naive_bayes_m->test($filter_attr);
			$c_matrix = $this->naive_bayes_m->get_c_matrix();
			$computed_population []= [
				'chromosomes'	=> $this->chromosomes[$i],
				'fitness'		=> $accuracy,
				'cm'			=> $c_matrix
			];
		}
		return $computed_population;
	}

	private function reproduction($computed_population)
	{
		$mating_pool = $this->create_mating_pool($computed_population);
		$pool_size = count($mating_pool);
		for ($i = 0; $i < $this->num_populations; $i++)
		{
			$parent_a = $mating_pool[rand(0, $pool_size - 1)];
			$parent_b = $mating_pool[rand(0, $pool_size - 1)];
			$this->chromosomes[$i] = $this->crossover($parent_a, $parent_b);
		}
	}

	private function create_mating_pool($computed_population)
	{
		$mating_pool = [];
		foreach ($computed_population as $genes) 
		{
			$n = (int)($genes['fitness'] * 100);
			for ($i = 0; $i < $n; $i++)
			{
				$mating_pool []= $genes['chromosomes'];
			}
		}
		return $mating_pool;
	}

	private function crossover($parent_a, $parent_b)
	{
		$child = [];
		$midpoint = rand(0, $this->chromosome_length - 1);
		for ($i = 0; $i < $this->chromosome_length; $i++)
		{
			if ($i > $midpoint)
			{
				$child []= $parent_a[$i];
			}
			else
			{
				$child []= $parent_b[$i];
			}
		}
		return $child;
	}

	private function mutation()
	{
		for ($i = 0; $i < $this->num_populations; $i++)
		{
			for ($j = 0; $j < count($this->chromosomes[$i]); $j++)
			{
				if ($this->mutation_rate > rand(0, 100) / 100)
				{
					$this->chromosomes[$i][$j] = $this->chromosomes[$i][$j] ^ 1;
				}
			}
		}
	}
}