<?php

class Form_Writer {

	const INDENT = "\t";

	protected $indent = 0;
	protected $fieldset = false;

	public $output = '';

	/**
	 * Initialize a HTML form.
	 * @param  string $action The form action. Default is none.
	 * @param  string $method Form submission method. Default is POST
	 *
	 * @return  Form_Writer returns the parent Object for method chaining.
	 */
	public function init($action = '', $method='POST')
	{
		$this->putLine(HTML::tag('form', ['action' => $action, 'method' => $method]));
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

		return $this;
	}

	/**
	 * Open a new Fieldset. Nested fieldsets are currently not supported.
	 * @param  string $legend The fieldset legend.
	 * @return Form_Writer         returns the parent Object for method chaining.
	 */
	public function fieldset($legend = "Fieldset")
	{
		if ( $this->fieldset)
		{
			$this->indent--;
			$this->putLine('</fieldset>');
			$this->fieldset = false;
		}
		$this->fieldset = true;
		$this->putLine('<fieldset>');
		$this->indent++;
		$this->putLine('<legend>'.$legend.'</legend>');

		return $this;
	}

	public function select(array $options, $multiple = false, $attributes = array())
	{
		if ( ! array_key_exists('multiple', $attributes) && $multiple)
		{
			$attributes['multiple'] = 'multiple';
		}

		$this->putLine(HTML::tag('select', $attributes));
		$this->indent++;

		foreach ($options as $value => $text)
		{
			$this->putLine('<option value="'.trim($value).'"">'.trim($text).'</option>');
		}

		$this->indent--;
		$this->putLine('</select>');
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

}