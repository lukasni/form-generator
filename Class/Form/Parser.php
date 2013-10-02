<?php

class Form_Parser {


	/**
	 * Parse a single line to evaluate what kind of form element it is.
	 *
	 * @throws  InvalidArgumentException If the passed string is not formatted correctly
	 * @param  string $line single line string representing a single form element
	 * @return array        An array representing the parsed data.
	 */
	public function parseLine($line)
	{
		// Colon designates a new fieldset.
		if ( $line[0] === ':' )
		{
			return $this->parseFieldset(explode(':', $line));
		}
		// No colon at beginning of line, form control is assumed.
		else
		{
			$arrLine = explode(';', $line);

			if (count($arrLine) < 4 || count($arrLine) > 5) 
			{
				// Input format does not meet requirements. 
				// TODO: This might be too limiting
				throw new InvalidArgumentException('Input does not match any known pattern: '.$line);
			}
			else if ( strpos($arrLine[1], 'input:') !== false )
			{
				return $this->parseInput($arrLine);
			}
			else if ( strpos($arrLine[1], 'select:') !== false )
			{
				return $this->parseSelect($arrLine);
			}
			else if ( strpos($arrLine[1], 'textarea') !== false )
			{
				return $this->parseTextArea($arrLine);
			}
			else if ( strpos($arrLine[1], 'button:') !== false)
			{
				return $this->parseButton($arrLine);
			}
			else
			{
				// If no fitting format is found. Additional validationmight be required.
				throw new InvalidArgumentException('Input does not match any known pattern: '.$line);
			}
		}
	}

	/**
	 * Parse a line as a fieldset.
	 * @param  array  $data The line passed by parseLine
	 * @return array        Currently returns the unmodified legend (everything after the colon)
	 */
	public function parseFieldset(array $data)
	{
		$legend = $data[1];

		$result = [];
		$result['legend'] = $legend;
		$result['type'] = 'fieldset';

		return $result;
	}

	/**
	 * Parse one line as an input type. All types are supported through custom attributes
	 * Radiobuttons and Checkboxes allow for options.
	 * @param  array  $data The line passed by parseLine
	 * @return array        Parsed data as an associative array
	 */
	public function parseInput(array $data)
	{
		$label = $data[0];
		$name = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $label));
		$type  = str_replace('input:', '', $data[1]);
		$options = str_replace('[', '', str_replace(']', '', $data[2]));
		$required = ($data[3] == 'required');
		$attributes = [];
		$arrOptions = [];

		if ( isset($data[4]) )
		{
			$attributes = $this->parseAttributes($data[4]);
		}

		if ( $type == 'radio' or $type == 'checkbox' )
		{
			$options = str_getcsv($options,',', "'");

			foreach ($options as $o)
			{
				list ($value, $oLabel) = explode('|',$o);

				if ( is_null($oLabel) )
				{
					$oLabel = $value;
				}

				$arrOptions[] = ['value' => $value, 'label' => $oLabel];
			}
		}

		HTML::addAttribute('type', $type, $attributes);

		$result = [];

		$result['label'] = $label;
		$result['name'] = $name;
		$result['type'] = 'input';
		$result['required'] = $required;
		$result['options'] = $arrOptions;
		$result['attributes'] = $attributes;

		return $result;
	}

	/**
	 * Parse one line as a select box. Single- and Multiple selection is supported.
	 * No custom attributes on options are enabled.
	 * @param  array  $data The line passed by parseLine
	 * @return array        Parsed data as an associative array.
	 */
	public function parseSelect(array $data)
	{
		$label = $data[0];
		$name = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $label));
		$type = str_replace('select:', '', $data[1]);
		$options = str_replace('[', '', str_replace(']', '', $data[2]));
		$required = ($data[3] == 'required');
		$attributes = [];

		// Check if any custom attributes have been passed
		if ( isset($data[4]) )
		{
			$attributes = $this->parseAttributes($data[4]);
		}

		// Add multiple="multiple" if it is a multi select.
		if ( $type == 'multiple' )
		{
			$attributes['multiple'] = 'multiple';
		}

		// Evaluate all select options passed.
		$options = str_getcsv($options,',', "'");

		foreach ($options as $o)
		{
			list ($value, $oLabel) = explode('|',$o);

			if ( is_null($oLabel) )
			{
				$oLabel = $value;
			}

			$arrOptions[] = ['value' => $value, 'label' => $oLabel];
		}

		$result = [];

		$result['label'] = $label;
		$result['name'] = $name;
		$result['type'] = 'select';
		$result['required'] = $required;
		$result['options'] = $arrOptions;
		$result['attributes'] = $attributes;

		return $result;
	}

	/**
	 * Parse one line as a textarea. 
	 * @param  array  $data The line passed by parseLine
	 * @return array        Parsed data as an associative array
	 */
	public function parseTextArea(array $data)
	{
		$label = $data[0];
		$name = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $label));
		$required = ($data[3] == 'required');
		$attributes = [];

		// Check if any custom attributes have been passed
		if ( isset($data[4]) )
		{
			$attributes = $this->parseAttributes($data[4]);
		}

		$result['label'] = $label;
		$result['name'] = $name;
		$result['type'] = 'textarea';
		$result['required'] = $required;
		$result['attributes'] = $attributes;

		return $result;
	}

	/**
	 * Parse one line as a Button
	 * @param  array  $data The line passed by parseLine.
	 * @return array         Parsed data as an associative array.
	 */
	public function parseButton(array $data)
	{
		$label = $data[0];
		$name = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $label));
		$type  = str_replace('button:', '', $data[1]);
		$value = empty($data[3]) ? $name : $data[3];
		$attributes = [];

		if ( isset($data[4]) )
		{
			$attributes = $this->parseAttributes($data[4]);
		}

		HTML::addAttribute('type', $type, $attributes);

		$result = [];

		$result['label'] = $label;
		$result['name'] = $name;
		$result['type'] = 'button';
		$result['attributes'] = $attributes;

		return $result;
	}

	/**
	 * Parses passed attribute strings into a format readable by HTML::attributes()
	 *
	 * @see HTML::attributes();
	 * @param  string $data String representing the attributes.
	 * @return array        Key/value array readable by HTML::attributes().
	 */
	protected function parseAttributes($data)
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