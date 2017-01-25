<?php

namespace Healthmeasures\Measurement;

class Measure extends Persistance
{
    protected $id;
    
    protected $name;
	
    protected $unit;
	
    /**The language this measure is taken (en, es, fr, etc)**/
    protected $lang;
    
    protected static $default_language = "en";
    
    public function __construct($name, $unit, $lang = null)
    {
        parent::__construct();
        $this->name = $name;
        $this->unit = $unit;
	$this->lang = !$lang ? static::$default_language : $lang;
        $this->setId();
    }
    
    /**
     * Receives the path to a cvs file with 2 mandatory columns: name, unit.
     * If you don't want to specify the language use the static method 
     * setDefaultLang($lang).
     * A measure is unique if their 3 properties are different, otherwise one is discarded.
     * @param string filepath to a $cvs_file_no_header
     * @return Array[Measure]
     */
    public static function bulkConstructor($cvs_file_no_header)
    {
        
    }
    
    /**
     * Sets the default language for every input
     * @param string $lang
     */
    public static function setDefaultLanguage($lang)
    {
        static::$default_language = $lang;
    }
    
    /** Static getters to retrieve collections **/
    
    /**
     * Returns the measures with that name. 
     * @param string $name
     * @return Array[Measure]
     */
    public static function getMeasuresByName($name)
    {
        
    }

    /**
     * Returns the measures with that unit. 
     * @param string $unit
     * @return Array[Measure]
     */
    public static function getMeasuresByUnit($unit)
    {
        
    }  
    
    /**
     * Returns the measures with that lang. 
     * @param string $lang
     * @return Array[Measure]
     */
    public static function getMeasuresByLang($lang)
    {
        
    }
            
    public function getSaveProperties()
    {
        return array('id', 'name', 'unit', 'lang');
    }

    protected function setId()
    {
        $this->id = md5($this->name . $this->unit. $this->lang);
    }

}

