<?php

namespace Ldg;

class Search
{
	protected $index = null;
	protected $indexFile = null;

	function __construct($indexFile = null)
	{
		if ($indexFile === null)
		{
			$indexFile = BASE_DIR . '/data/search.json';
		}

		$this->indexFile = $indexFile;

		if (!file_exists($this->indexFile))
		{
			if (!file_put_contents($this->indexFile, json_encode([],JSON_PRETTY_PRINT)))
			{
				throw new \Exception('Could not create index file: ' . $this->indexFile);
			}
		}

		if (!$this->loadIndex())
		{
			throw new \Exception('Could not read index file: ' . $this->indexFile);
		}
	}

	function resetIndex()
	{
		$this->index = [];
	}

	function loadIndex()
	{
		$index = json_decode(file_get_contents($this->indexFile), true);

		if ($index === false)
		{
			return false;
		}

		$this->index = $index;

		return true;

	}

	function setEntry($key, $value)
	{
		$this->index[$key] = $value;
	}

	function search($q)
	{
		$results = [];

		foreach ($this->index as $key => $value)
		{
			if (stripos($value, $q) !== false)
			{
				$results[$key] = $value;
			}
		}

		return $results;
	}

	function save()
	{
		file_put_contents($this->indexFile, json_encode($this->index, JSON_PRETTY_PRINT));
	}
}