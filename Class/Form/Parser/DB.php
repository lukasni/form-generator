<?php

/**
 * Form Parser for database input. 
 * 
 * Will read the output generated by Input_Database and parse it into
 * an array readable by Form_Writer
 *
 * @author  Lukas Niederberger <lukas.niederberger@gibmit.ch>
 */
class Form_Parser_DB {

	/**
	 * Parse a single line to evaluate what kind of form element it is.
	 *
	 * Calls the appropriate parsing method after determining the type.
	 *
	 * @throws  InvalidArgumentException If the passed array is not formatted correctly
	 * @param  array $line  Output from Input_Database, describes a single table column.
	 * @return array        An array representing the parsed data.
	 */
	public function parseLine($line)
	{
		// Determine if the input array is formatted correctly
		if ( ! is_array($line) )
		{
			throw new InvalidArgumentException('Input is not an array.');
		}
		else
		{
			if ( ! array_key_exists('Type', $line) ||
				 ! array_key_exists('Extra', $line) ||
				 ! array_key_exists('Field', $line) ||
				 ! array_key_exists('Null', $line)
			)
			{
				throw new InvalidArgumentException('Invalid input array.');
			}
		}

		// Determine the data type of the passed table column.
		$type = strtolower(explode('(', $line['Type'])[0]);

		// Skip field if it is an auto_increment.
		if ($line['Extra'] === 'auto_increment')
		{
			return false;
		}

		// Call the appropriate parsing function for the determined data type.
		switch ($type)
		{
			case 'enum':
				return $this->parseEnum($line);

			case 'text':
				return $this->parseText($line);

			case 'blob':
				return $this->parseBlob($line);

			case 'date':
			case 'datetime':
			case 'timestamp':
			case 'time':
			case 'year':
				return $this->parseDateTime($line, $type);

			case 'int':
			case 'tinyint':
			case 'smallint':
			case 'mediumint':
			case 'bigint':
			case 'float':
			case 'double':
			case 'decimal':
				return $this->parseNum($line, $type);
			
			default:
				return $this->parseInput($line);
		}
	}

	protected function parseEnum(array $line)
	{
		$enum_options = str_replace('enum(', '', str_replace(')', '', $line['Type']));
		$options = str_getcsv($enum_options, ',', "'");

		$result = $this->prepareResult($line);

		foreach ($options as $key => $o)
		{
			$result['options'][$key]['label'] = $o;
			$result['options'][$key]['value'] = $o;
		}
		
		if ( count($options) < 5)
		{
			// Less than 5 options -> treat as radio button
			$result['type'] = 'input';
			$result['attributes']['type'] = 'radio';
		}
		else
		{
			// More than 5 options -> treat as dropdown select.
			$result['type'] = 'select';
		}

		return $result;
	}

	protected function parseText(array $line)
	{
		$result = $this->prepareResult($line);
		$result['type'] = 'textarea';

		return $result;
	}

	protected function parseBlob(array $line)
	{
		$result = $this->prepareResult($line);

		$result['attributes'] = ['type' => 'file'];

		return $result;
	}

	protected function parseDateTime(array $line, $type)
	{
		$result = $this->prepareResult($line);

		if ($type == 'datetime' || $type == 'timestame')
		{
			$result['attributes']['type'] = 'datetime';
		} 
		else 
		{
			$result['attributes']['type'] = 'date';
		}

		return $result;
	}

	protected function parseNum(array $line, $type)
	{
		$result = $this->prepareResult($line);

		$result['attributes']['type'] = 'number';

		$unsigned = (strpos($line['Type'], 'unsigned') !== false);

		switch ($type) {
			case 'int':
				$result['attributes']['min'] = $unsigned ? 0 : -2147483648;
				$result['attributes']['max'] = $unsigned ? 4294967295 : 2147483647;
				break;
			case 'tinyint':
				$result['attributes']['min'] = $unsigned ? 0 : -128;
				$result['attributes']['max'] = $unsigned ? 255 : 127;
				break;
			case 'smallint':
				$result['attributes']['min'] = $unsigned ? 0 : -32768;
				$result['attributes']['max'] = $unsigned ? 65535 : 32787;
				break;
			case 'mediumint':
				$result['attributes']['min'] = $unsigned ? 0 : -8388608;
				$result['attributes']['max'] = $unsigned ? 16777215 : 8388607;
				break;
			case 'bigint':
				$result['attributes']['min'] = $unsigned ? 0 : -9223372036854775808;
				$result['attributes']['max'] = $unsigned ? 18446744073709551615 : 9223372036854775807;
				break;
		}

		return $result;
	}

	protected function parseInput(array $line)
	{
		$result = $this->prepareResult($line);

		$result['attributes']['type'] = 'text';

		$start = strpos($line['Type'], '(')+1;
		$length = strpos($line['Type'], ')')-$start;
		$maxlength = substr($line['Type'], $start, $length);

		if ( is_numeric($maxlength) )
		{
			$result['attributes']['maxlength'] = $maxlength;
		}

		return $result;
	}

	/**
	 * Prepare a basic result array that will satisfy Form_Writer
	 *
	 * Also handles data from the input common to all data types.
	 * 
	 * @param  array  $line Input data
	 * @return array        Associative array that will satisfy Form_Writer.
	 */
	protected function prepareResult(array $line)
	{
		$result['type'] = 'input';
		$result['label'] = ucfirst(str_replace('_', ' ', $line['Field']));
		$result['name'] = $line['Field'];
		$result['required'] = ($line['Null'] === 'NO');
		$result['options'] = [];
		$result['attributes'] = [];

		return $result;
	}

}