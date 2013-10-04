<?php

class Input_Database {

	protected $dbc;

	public $table;

	public function __construct($dbname, $dbhost, $dbuser, $dbpass)
	{
		$dbc = new PDO("mysql:dbname=$dbname;host=$dbhost", $dbuser, $dbpass);
	}

	public function setTable($table_name)
	{
		$this->table = $table;
	}

	public function describe()
	{
		$sql = 'DESCRIBE :table';

		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(':table', $this->table);
		$stmt->execute();

		return $stmt->fetchAll();
	}

}