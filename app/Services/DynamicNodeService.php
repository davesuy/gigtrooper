<?php

namespace Gigtrooper\Services;

use Gigtrooper\Traits\Queryable;

class DynamicNodeService 
{
	use Queryable;

	private $queryString;
	private $queryArgs;

	public function createNode($attributes, $label)
	{
		$queryString = $this->getSaveModelQuery($attributes, $label);
		$this->addQueryString($queryString, $attributes);

		return $this->save();
	}

	public function save($returnResult = false)
	{

		$queryString = $this->queryString;
		$queryArgs = $this->queryArgs;

		$result =  \Neo4jQuery::getResultSet($queryString, $queryArgs);

		if($returnResult)
		{
			return $result;
		}

		if ($result->count())
		{
			$node = $result[0]['n'];
			$this->node = $node;
			return $node;
		}
		else
		{
			return false;
		}
	}
}