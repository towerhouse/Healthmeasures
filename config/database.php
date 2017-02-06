<?php

return array(
    
	/*
	|--------------------------------------------------------------------------
	| Table names
	|--------------------------------------------------------------------------
	|
	| Name of the tables where we want to store our data, one for measures and
        | one for values.
	|
	*/
    
        'table_name_measure' => getenv('TABLE_MEASURES'),
        'table_name_value' => getenv('TABLE_VALUES'),
    
        'tables_prefix'   => getenv('TABLES_PREFIX'),

	/*
	|--------------------------------------------------------------------------
	| Default Database Connection Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify which of the database connections below you wish
	| to use as your default connection for all database work.
	|
	*/

	'db_engine' => getenv('DB_CONNECTION'),

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	| Here are each of the database connections setup for your application.
	| Of course, examples of configuring each database platform.
        | All database work in Laravel is done through the PHP PDO facilities
	| so make sure you have the driver for your particular database of
	| choice installed on your machine before you begin development.
	|
	*/

	'connections' => array(

		'sqlite' => array(
			'driver'   => 'sqlite',
			'database' =>  getenv('DB_DATABASE'),	
		),

		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => getenv('DB_HOST'),
                        'database'  => getenv('DB_DATABASE'),
                        'username'  => getenv('DB_USERNAME'),
                        'password'  => getenv('DB_PASSWORD'),
			'charset'   => getenv('DB_CHARSET'),
		)

	),
);