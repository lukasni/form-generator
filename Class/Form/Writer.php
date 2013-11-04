<?php

class Form_Writer {

	const INDENT = "\t";
	const XML_COMPATIBLE = false;

	protected $indent = 0;
	protected $fieldset = false;
	protected $final = false;

	protected $output = '';

	/**
	 * Initialize a HTML form.
	 * @param  string $action The form action. Default is none.
	 * @param  string $method Form submission method. Default is POST
	 *
	 * @return  Form_Writer returns the parent Object for method chaining.
	 */
	public function init($action = '', $method='POST', $attributes = [])
	{
		if ( ! array_key_exists('action', $attributes) )
		{
			$attributes['action'] = $action;
		}
		if ( ! array_key_exists('method', $attributes) )
		{
			$attributes['method'] = $method;
		}

		$this->putLine(HTML::tag('form', $attributes));
		$this->indent++;

		return $this;
	}

	/**
	 * Finalize the HTML Form.
	 * @return Form_Writer returns the parent Object for method chaining.
	 */
	public function finish()
	{
		if ( $this->fieldset )
		{
			$this->indent--;
			$this->putLine('</fieldset>');
			$this->fieldset = false;
		}

		$this->indent--;
		$this->putLine('</form>');

		$this->final = true;

		return $this;
	}

	/**
	 * Returns the finished output.
	 * @return string complete output of the form.
	 */
	public function render()
	{
		if ( ! $this->final )
		{
			$this->finish();
		}
		
		return $this->output;
	}

	/**
	 * Open a new Fieldset. Nested fieldsets are currently not supported.
	 * @param  string $legend The fieldset legend.
	 * @return Form_Writer         returns the parent Object for method chaining.
	 */
	public function fieldset(array $data)
	{
		if ( $this->fieldset )
		{
			$this->indent--;
			$this->putLine('</fieldset>');
			$this->fieldset = false;
		}
		$this->fieldset = true;
		$this->putLine('<fieldset>');
		$this->indent++;
		$this->putLine('<legend>'.$data['legend'].'</legend>');

		return $this;
	}

	public function textarea(array $data)
	{
		// Add attributes using HTML::addAttribute to ensure user preferences are kept.
		HTML::addAttribute('id', $data['name'], $data['attributes']);
		HTML::addAttribute('name', $data['name'], $data['attributes']);

		// Check if select field is required.
		if ($data['required'] === true)
		{
			HTML::addAttribute('required', 'required', $data['attributes']);
		}
		
		// Generate label and select tag, add to output.
		$label = '<label for="'.$data['attributes']['id'].'">'.$data['label'].'</label>';
		$input = HTML::tag('textarea', $data['attributes']).'</textarea>';

		$this->putLine($label)
			 ->putLine($input);
	}

	public function button(array $data)
	{
		// Add attributes using HTML::addAttribute to ensure user preferences are kept.
		HTML::addAttribute('id', $data['name'], $data['attributes']);
		HTML::addAttribute('name', $data['name'], $data['attributes']);
		
		// Generate label and select tag, add to output.
		$input = HTML::tag('button', $data['attributes']).$data['label'].'</button>';
		
		$this->putLine($input);
	}

	/**
	 * Generate a select tag.
	 * @uses HTML     for generating tags and preparing attributes.
	 * @param  array  $data Data prepared by a field parser
	 */
	public function select(array $data)
	{
		// Add attributes using HTML::addAttribute to ensure user preferences are kept.
		HTML::addAttribute('id', $data['name'], $data['attributes']);
		HTML::addAttribute('name', $data['name'], $data['attributes']);

		// Check if select field is required.
		if ($data['required'] === true)
		{
			HTML::addAttribute('required', 'required', $data['attributes']);

			if ( ! array_key_exists('multiple', $data['attributes']) )
			{
				array_unshift($data['options'], ['value' => '', 'label' => 'Please select...']);
			}
		}
		
		// Generate label and select tag, add to output.
		$label = '<label for="'.$data['attributes']['id'].'">'.$data['label'].'</label>';
		$this->putLine($label)
			 ->putLine(HTML::tag('select', $data['attributes']))
			 ->indent++;

		// Add all option tags
		foreach ($data['options'] as $o)
		{
			$this->putLine('<option value="'.trim($o['value']).'">'.trim($o['label']).'</option>');
		}

		// reduce indent and close select tag.
		$this->indent--;
		$this->putLine('</select>');
	}

	public function input(array $data)
	{
		// Add attributes using HTML::addAttribute to ensure user preferences are kept.
		HTML::addAttribute('id', $data['name'], $data['attributes']);
		HTML::addAttribute('name', $data['name'], $data['attributes']);

		// Check if select field is required.
		if ($data['required'] === true)
		{
			HTML::addAttribute('required', 'required', $data['attributes']);
		}

		if ( count($data['options']) > 1)
		{
			// Multi input processing

			$title = '<label>'.$data['label'].'</label>';
			$this->putLine($title);
			$this->putLine('<div class="inputgroup">')
				 ->indent++;

			foreach ( $data['options'] as $option)
			{
				$data['attributes']['value'] = $option['value'];
				$data['attributes']['id'] = $data['name'].'_'.$option['value'];

				$label_open = '<label for="'.$data['attributes']['id'].'">';
				$label_content = HTML::tag('input', $data['attributes'], self::XML_COMPATIBLE).$option['label'];
				$label_close = '</label>';

				$this->putLine($label_open)
					 ->indent++;
				$this->putLine($label_content)
					 ->indent--;
				$this->putLine($label_close);
			}

			$this->indent--;
			$this->putLine('</div>');
		}
		else
		{
			$label = '<label for="'.$data['attributes']['id'].'">'.$data['label'].'</label>';
			$input = HTML::tag('input', $data['attributes'], self::XML_COMPATIBLE);

			$this->putLine($label)
				 ->putLine($input);
		}
	}

	/**
	 * Write a single line to the output. Keeping track of indentation.
	 * @param  string $line Line that will be written to the output.
	 * @return Form_Writer       Returns the parent Object for method chaining.
	 */
	public function putLine($line)
	{
		$this->output .= "\n";

		for ($i = 0; $i < $this->indent; $i++)
		{
			$this->output .= self::INDENT;
		}

		$this->output .= $line;

		return $this;
	}

	public function __call($name, array $arguments)
	{
		throw new BadMethodCallException('No output parser found for type '.$name);
	}

}