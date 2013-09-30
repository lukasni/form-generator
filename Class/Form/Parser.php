<?php

class Form_Parser {



	public function parseLine($line)
	{
		if ( $line[0] === ':' )
		{
			$this->parseFieldset($line);
		}
		else
		{
			$arrLine = explode(';', $line);

			if (count($arrLine) < 4 || count($arrLine) > 5) 
			{
				throw new InvalidArgumentException('Input does not match any known pattern: '.$line);
			}
			else if ( strpos($arrLine[1], 'input:') !== false )
			{
				$this->parseInput($arrLine);
			}
			else if ( strpos($arrLine[1], 'select:') !== false )
			{
				$this->parseSelect($arrLine);
			}
			else if ( strpos($arrLine[1], 'textarea') !== false )
			{
				$this->parseTextArea($arrLine);
			}
			else
			{
				throw new InvalidArgumentException('Input does not match any known pattern: '.$line);
			}
		}
	}

	public function parseFieldset($data)
	{
		print_r($data);
	}

	public function parseInput(array $data)
	{
		$label = $data[0];
		$name  = strtolower($label);
		$type  = str_replace('input:', '', $data[1]);
		$options = $data[2];
		$required = ($data[3] = 'required') ? true : false;
		$attributes = [];

		if ( isset($data[4]) )
		{
			$attributes = $this->parseAttributes($data[4]);
		}

		if ( $type == 'radio' or $type == 'checkbox' )
		{
			$options = str_replace('[', '', $options);
			$options = str_replace(']', '', $options);

			$options = explode(',', $options);
		}
	}

	public function parseSelect(array $data)
	{
		/*
		
Array
(
    [0] => Dropdown
    [1] => select:single
    [2] => [one|One,two|Two,three|Three,diff|Choices]
    [3] => optional
)
		 */
		print_r($data);
	}

	public function parseTextArea(array $data)
	{
		print_r($data);
	}

	protected function parseAttributes(array $data)
	{
		$attributes_temp = explode(',', $data);
		$result = [];

		foreach ($attributes_temp as $attr)
		{
			list($key, $value) = explode('=',$attr);

			$result[$key] = $value;
		}
		return $result;
	}

}