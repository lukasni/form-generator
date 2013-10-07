<?php

class Input_Database {

	protected $dbc;

	public $table;

	public function __construct(PDO $dbc, $table = '')
	{
		$this->dbc = $dbc;
		$this->table = $table;
	}

	public function setTable($table_name)
	{
		$this->table = $table;
	}

	public function getFields()
	{
		return $this->describe();
	}

	protected function describe()
	{
		$sql = 'DESCRIBE '.$this->table;

		$stmt = $this->dbc->prepare($sql);
		$stmt->execute();

		return $stmt->fetchAll();
	}



}