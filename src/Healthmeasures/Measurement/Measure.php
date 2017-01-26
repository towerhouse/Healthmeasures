<?php

namespace Healthmeasures\Measurement;

class Measure extends Persistance
{
    public $name;
	
    public $unit;
	
    /**The language this measure is taken (en, es, fr, etc)**/
    public $lang;
    
    protected static $default_language = "en";
    
    public function __construct($name = null, $unit = null, $lang = null)
    {
        parent::__construct();
        $this->name = $name;
        $this->unit = $unit;
	$this->lang = !$lang ? static::$default_language : $lang;
        $this->setId();
    }
    
    
        
    /**
     * Sets the default language for every input
     * @param string $lang
     */
    public static function setDefaultLanguage($lang)
    {
        static::$default_language = $lang;
    }
    
    /** Static getters to retrieve collections or one object **/
        
    /**
     * Returns the measures with that name. 
     * @param string $name
     * @return Array[Measure]
     */
    public function getMeasuresByName($name)
    {
        return $this->getObjectsByCriteria(array('name' => $name));
    }

    /**
     * Returns the measures with that unit. 
     * @param string $unit
     * @return Array[Measure]
     */
    public function getMeasuresByUnit($unit)
    {
        return $this->getObjectsByCriteria(array('unit' => $unit));
    }  
    
    /**
     * Returns the measures with that lang. 
     * @param string $lang
     * @return Array[Measure]
     */
    public function getMeasuresByLang($lang)
    {
        return $this->getObjectsByCriteria(array('lang' => $lang));
    }
            
    public function getSaveProperties()
    {
        return array('id', 'name', 'unit', 'lang');
    }
    
    public function setId()
    {
        $this->id = md5($this->name . $this->unit. $this->lang);
    }

}

