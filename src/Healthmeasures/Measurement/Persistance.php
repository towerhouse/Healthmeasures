<?php

namespace Healthmeasures\Measurement;
use Healthmeasures\Configuration\Application;

abstract class Persistance
{ 
    protected $id;
    protected static $connection = null;
    protected static $app = null;
    protected $engine;
    protected $fetch_style;


    public function __construct()
    {
        if (!static::$app) {
            static::$app = new Application();
        }
        
        $this->engine = static::$app->config->get('database.db_engine');
        $this->fetch_style = static::$app->config->get('database.fetch');
    }
        
    /**
     * Receives the path to a cvs file with the object columns.
     * If you don't want to specify the language use the static method 
     * setDefaultLang($lang).
     * @param string filepath to a $cvs_file_with_header
     * @param boolean set store to true if you want persistance
     * @return Array[Object]
     */
    public function bulkConstructor($cvs_file_with_header, $store = true)
    {
        $csvFile = file($cvs_file_with_header);
        $data = array();
        foreach ($csvFile as $line) {
            $data[] = str_getcsv($line);
        }
        
        if ($store) {
            //Header must always be present on your csv file!
            /** Sample
             * array (
                0 => 
                array (
                  0 => 'name',
                  1 => 'unit',
                ),
                1 => 
                array (
                  0 => 'sistole',
                  1 => '',
                ),
             )
             */
            $class = get_class($this);
            $storage = array();
            
            $header = $data[0];
            if (count(($data > 1))) {
                for($i = 1; $i < count($data); $i++) {
                    $object = new $class();
                    foreach ($header as $index => $prop) {
                        $object->$prop = $data[$i][$index];
                    }
                    $object->setId();
                    $object->save();
                    $storage[] = $object;
                }
                return $storage;
            }
        }
        
        return $data;
    }
    
    /**
     * Saves any persistable record using reflection.
     */
    public function save()
    {
        //Metadata
        $this->checkTables();
        $table_name = $this->getTableName(get_class($this));
        $props = $this->getSaveProperties();
        $values = array();
        
        //Handle created_at attribute
        if (!in_array('created_at', $props)) {
            $props[] = 'created_at';
            $this->created_at = date("Y-m-d H:i:s");
        }
        
        //Values for the attributes of the tables
        foreach ($props as $p) {
            $values[] = $this->$p;
        }

        //Terms to form the pdo query
        $prop_names = '(`' . implode('`,`', $props) . '`)';
        $placeholders = '(' . implode(',', array_fill(0, count($props), '?')) . ')';
        $sql = "REPLACE INTO $table_name $prop_names VALUES $placeholders";
        ///echo $sql; var_export($values);
        $statement = static::$connection->prepare($sql);
        $statement->execute($values);
        return $this;
    }
        
    public function getObjectsByCriteria(Array $conditions, Array $raw_conditions = array())
    {
        //Metadata
        $this->checkTables();
        $table_name = $this->getTableName(get_class($this));
        $conditions_array = array();
        $values = array();
        foreach ($conditions as $name => $val) {
            $conditions_array[] = "`$name` = ?";
            $values[] = $val;
        }
        $sql = "SELECT * FROM $table_name"; 
        if (count($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions_array);
        }
        
        if (count($raw_conditions)) {
            $connector = count($conditions) ? ' AND ' : ' WHERE ';
            $sql .= $connector . implode('AND', $raw_conditions);
        }

        $statement = static::$connection->prepare(trim($sql));
        $statement->execute($values);
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $objects = array();
        //Hydratation of properties to create objects
        $classname = get_class($this);
        foreach ($data as $d) {
            $o = new $classname();
            $objects[] = $o;
            foreach ($d as $key => $val) {
                $o->$key = $val;
            }
        }
        return $objects;
    }
    
    public function getById($id)
    {
        $all = $this->getObjectsByCriteria(array('id' => $id));
        return count($all) >= 1 ? $all[0] : null;
    }
    
    public function getAll()
    {
        $all = $this->getObjectsByCriteria(array());
        return $all;
    }
    
    public function countAll()
    {
        $table = $this->getTableName(get_class($this));
        $sql = 'SELECT COUNT(1) FROM ' . $table;
        $result = static::$connection->prepare($sql); 
        $result->execute(); 
        $number_of_rows = $result->fetchColumn();
        return $number_of_rows;
    }
    
    /**
     * Gets the table name for this class using the configuration file and
     * the name of the class.
     * @param string $classname
     * @return string
     */
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
    
    /**
     * Declares the attributes of the objects that should be saved using an array.
     */
    public abstract function getSaveProperties();
    
    /**
     * Sets an string id to identify the record.
     */
    protected abstract function setId();
 
    public function getId()
    {
        return $this->id;
    }
    
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
            PRIMARY KEY(id)
        )";
        
        $values_create = "CREATE TABLE IF NOT EXISTS $values_table (
            id VARCHAR(100) NOT NULL,
            owner_id VARCHAR(100) NOT NULL,
            measure_id VARCHAR(100) NOT NULL,
            `value` VARCHAR(100),
            created_at DATETIME NOT NULL,
            PRIMARY KEY(id)
        )";
        
        $this->setConnection();
        
        static::$connection->exec($measures_create);
        static::$connection->exec($values_create);

    }

    /**
     * Instantiates a connection according to the driver declared on the
     * configuration file.
     */
    private function setConnection()
    {
        if (!static::$connection) {
            if ($this->engine == 'mysql') {
                $data = static::$app->config->get('database.connections.mysql');
                $dbh = new \PDO("mysql:host={$data['host']};dbname={$data['database']};charset={$data['charset']}", $data['username'], $data['password']);
            } else {
                $data = static::$app->config->get('database.connections.mysql');
                $dbh = new \PDO('sqlite:' . $data['database']);    
            }
            static::$connection = $dbh;
        }
    }
    
    public function getLastConnectionError()
    {
        return static::$connection->errorInfo();
    }
    
    public function __get($property) {
        if (property_exists($this, $property)) {
          return $this->$property;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }
}