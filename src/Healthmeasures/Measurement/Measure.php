<?php

namespace Healthmeasures\Measurement;

class Measure
{
    private $name;
	
    private $unit;
	
	/**The language this measure is taken (en, es, fr, etc)**/
	private $lang;
    
    public function __construct($name, $unit, $lang = "en") {
        $this->name = $name;
        $this->unit = $unit;
	$this->lang = $lang;
    }
}

