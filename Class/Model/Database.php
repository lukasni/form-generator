<?php

/**
 * Database Model. Offers basic information about the database and tables.
 *
 * @author Lukas Niederberger <lukas.niederberger@gibmit.ch>
 */
class Model_Database {

	/**
	 * @var PDO Database object
	 */
	protected $dbh;

	/**
	 * @var string selected Database
	 */
	protected $database;

	/**
	 * @var Databases that will be ignored by showDatabases()
	 */
	protected $ignored_dbs = [
		'information_schema',
		'performance_schema',
		'mysql',
		'phpmyadmin',
		'webauth',
	];

	/**
	 * Constructor, sets up the database connecton
	 * @param string $host     Database host url
	 * @param string $user     Database authentication user
	 * @param string $password Database authentication password
	 * @param string $database Schema that will be used. Can be left empty and  added with useDatabasse() later on.
	 */
	public function __construct($host, $user, $password, $database = null)
	{
		$dsn = 'mysql:host='.$host;

		// Add Database to DSN if it has been passed.
		if ( ! is_null($database) ) 
		{
			$dsn .= ';dbname='.$database;
		}

		$this->dbh = new PDO($dsn, $user, $password);
		$this->database = $database;
	}

	/**
	 * Use a specific Schema
	 * @param  string $database Name of the Schema that will be used
	 */
	public function useDatabase($database)
	{
		$this->dbh->query('USE '.$database);
		$this->database = $database;
	}

	/**
	 * Returns a list of all databases, excluding those set in Model_Database::$ignored_dbs
	 * @return array Array containing all valid databases.
	 */
	public function showDatabases()
	{
		$qery_result = $this->dbh->query('SHOW DATABASES');

		$reqult = [];

		foreach ($qery_result as $q)
		{
			// Skip of database is listed in ignored_dbs
			if ( in_array($q['Database'], $this->ignored_dbs))
			{
				continue;
			}
			$result[] = $q['Database'];
		}

		return $result;
	}

	/**
	 * Shows all tables in selected database. Throws an exception if no database has been selected.
	 * @return array Contains all tables in selected Database.
	 */
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

	/**
	 * Show the fields in one table. Throws an exception if no database has been selected.
	 * @param  string $table name of the table or view that will be described
	 * @return array         Associative array containing all fields in the requested table.
	 */
	public function describe($table)
	{
		if ( is_null($this->database) )
		{
			throw new Exception("No Database has been selected");
		}

		$stmt = $this->dbh->query('DESCRIBE '.$table);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

}