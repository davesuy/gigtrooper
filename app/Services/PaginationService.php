<?php

namespace Gigtrooper\Services;

class PaginationService 
{
	private $links;
	private $currentPage = 1;
	protected $base;
	private $defaultArgs = array(
		'neighbours'  => 3,
		'ends'        => 2,
		'limit'       => 25,
		'base'        => '',
		'currentPage' => 1
	);

	public function setArgs($inputArgs = array())
	{
		$args = array_merge($this->defaultArgs, $inputArgs);

		$total = $args['total'];
		$totalPages = ceil($total / $args['limit']);

		$currentPage = $args['currentPage'];

		$neighbours = $args['neighbours'];
		$ends       = $args['ends'];

		$links = array();
		if($totalPages > 1)
		{
			$links['base'] = $args['base'] . 'page/';

			//$this->base = $args['base'] . 'page/' . $currentPage;

			$links['query_string'] = '';

			if(!empty($_SERVER['QUERY_STRING']))
			{
				$links['query_string'] = '?' . $_SERVER['QUERY_STRING'];
			}

			$links['first'] = 1;

			if($currentPage > 1)
			{
				$links['previous'] = $currentPage - 1;
			}

			$links['total'] = $totalPages;
			$links['current'] = $currentPage;
			$more = $currentPage + $neighbours;
			$less = $currentPage - $neighbours;
			$more_prev = false;

			if($less > ($ends + 1))
			{
				$links['more_previous'] = 1;
				$more_prev = true;
			}

			$more_next = false;

			if($more < ($totalPages - $ends))
			{
				$links['more_next'] = 1;
				$more_next = true;
			}

			$x = 0;
			for($i = 1; $i <= $totalPages; $i++)
			{

				if($more_prev == true && $i == ($less - 1))
				{
					$links['pages'][$x] = 'more';
					$x++;
				}

				if(($i >= $less && $i <= $more) || ($i <= ($ends + 1)) || ($i >= ($totalPages - $ends)) )
				{

					$links['pages'][$x] = $i;
					$x++;
				}

				if($more_next == true && $i == ($more + 1))
				{
					$links['pages'][$x] = 'more';
					$x++;
				}
			}

			if($currentPage < $totalPages)
			{
				$links['next'] = $currentPage + 1;
			}

			$links['last'] = $totalPages;
		}

		$this->links = $links;
	}

	public function render()
	{
		$links = $this->links;

		return view('partials.pagination', array(
			'links' => $links
		));
	}
}