<?php

class Model_Database {

	protected $dbh;

	protected $database;

	protected $ignored_dbs = [
		'information_schema',
		'performance_schema',
		'mysql',
		'phpmyadmin',
		'webauth',
	];

	public function __construct($host, $user, $password, $database = null)
	{
		$dsn = 'mysql:host='.$host;

		if ( ! is_null($database) ) 
		{
			$dsn .= ';dbname='.$database;
		}

		$this->dbh = new PDO($dsn, $user, $password);
		$this->database = $database;
	}

	public function useDatabase($database)
	{
		$this->dbh->query('USE '.$database);
		$this->database = $database;
	}

	public function showDatabases()
	{
		$qery_result = $this->dbh->query('SHOW DATABASES');

		$reqult = [];

		foreach ($qery_result as $q)
		{
			if ( in_array($q['Database'], $this->ignored_dbs))
			{
				continue;
			}
			$result[] = $q['Database'];
		}

		return $result;
	}

	public function showTables()
	{
		if ( is_null($this->database) )
		{
			throw new Exception("No Database has been selected");
		}

		$query_result = $this->dbh->query('SHOW TABLES')->fetchAll();

		$result = [];

		foreach ($query_result as $q)
		{
			$result[] = $q[0];
		}

		return $result;
	}

	public function describe($table)
	{
		$stmt = $this->dbh->query('DESCRIBE '.$table);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

}