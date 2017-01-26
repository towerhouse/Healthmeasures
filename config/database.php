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
    
        'table_names' => array(
            'Measure' => 'measures',
            'Value' => 'values',
        ),
    
        'tables_prefix'   => 'healthmeasures_',

	/*
	|--------------------------------------------------------------------------
	| PDO Fetch Style
	|--------------------------------------------------------------------------
	|
	| By default, database results will be returned as instances of the PHP
	| arrays.
	|
	*/

	'fetch' => PDO::FETCH_ASSOC,

	/*
	|--------------------------------------------------------------------------
	| Default Database Connection Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify which of the database connections below you wish
	| to use as your default connection for all database work.
	|
	*/

	'db_engine' => 'mysql',

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
			'database' => __DIR__.'/../database/healthmeasures.sqlite',	
		),

		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => 'localhost',
                        'database'  => 'mbhealth',
                        'username'  => 'root',
                        'password'  => '',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
		)

	),
);