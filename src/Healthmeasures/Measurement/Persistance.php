<?php

namespace Healthmeasures\Measurement;
use Healthmeasures\Configuration\Application;

abstract class Persistance
{ 
    protected static $connection = null;
    protected static $app = null;
    protected $engine;
    
    public function __construct()
    {
        if (!static::$app) {
            static::$app = new Application();
        }
        
        $this->engine = static::$app->config->get('database.db_engine');
    }
    
    public function save()
    {
        //Metadata
        $this->checkTables();
        $table_name = $this->getTableName(get_class($this));
        $props = $this->getSaveProperties();
        $values = array();
        
        //Values for the attributes of the tables
        foreach ($props as $p) {
            $values[] = $this->$p;
        }
        
        //Handle created_at attribute
        if (!in_array('created_at', $props)) {
            $props[] = 'created_at`';
            $values[] = date("Y-m-d H:i:s");
        }
        
        //Terms to form the pdo query
        $prop_names = '(`' . implode('`,`', $props) . ')';
        $placeholders = '(' . implode(',', array_fill(0, count($props), '?')) . ')';
        $query = "REPLACE INTO $table_name $prop_names VALUES $placeholders";
        $statement = static::$connection->prepare($query);
        print_r(static::$connection->errorInfo());
        $statement->execute($values);
        print_r(static::$connection->errorInfo());
    }
    
    public function getTableName($classname)
    {
        $table_names = static::$app->config->get('database.table_names'); 
        $prefix = static::$app->config->get('database.tables_prefix'); 
        $class = $classname;

        if (strpos($classname, '\\')) {
            $parts = explode('\\', $classname);
            $class = $parts[count($parts) - 1];
        }
        
        $table = $prefix . $table_names[$class];
        return $table;
    }
    
    public abstract function getSaveProperties();
    
    protected abstract function setId();
        
    /**
     * Checks if the persistance tables are there
     * otherwise create them.
     * @throws \Exception
     */
    protected function checkTables()
    { 
        $measures_table = $this->getTableName('Measure');
        $values_table   = $this->getTableName('Value');
        
        $measures_create = "CREATE TABLE IF NOT EXISTS $measures_table (
            id VARCHAR(100) NOT NULL,
            name VARCHAR(100),
            unit VARCHAR(100),
            lang VARCHAR(4),
            created_at DATETIME NOT NULL,
            updated_at DATETIME NULL,
            PRIMARY KEY(id)
        )";
        
        $values_create = "CREATE TABLE IF NOT EXISTS $values_table (
            id VARCHAR(100) NOT NULL,
            owner_id VARCHAR(100) NOT NULL,
            measure_id int NOT NULL,
            `value` VARCHAR(100),
            created_at DATETIME NOT NULL,
            updated_at DATETIME NULL,
            PRIMARY KEY(id)
        )";
        
        $this->setConnection();
        
        static::$connection->exec($measures_create);
        static::$connection->exec($values_create);print_r(static::$connection->errorInfo());

    }

    
    private function setConnection()
    {
        if (!static::$connection) {
            if ($this->engine == 'mysql') {
                $data = static::$app->config->get('database.connections.mysql');
                $dbh = new \PDO("mysql:host={$data['host']};dbname={$data['database']}", $data['username'], $data['password']);
            } else {
                $data = static::$app->config->get('database.connections.mysql');
                $dbh = new \PDO('sqlite:' . $data['database']);    
            }
            static::$connection = $dbh;
        }
    }
}