<?php

namespace Healthmeasures\Measurement;

class Value
{
    /**
     *
     * @var type Measure
     */
    protected $measure;
    
    /**
     * Identifier for the unique value of a measure
     * @var string
     */
    protected $id;
    
    /**
     * Date when the measure was taken
     * @var timestamp 
     */
    protected $date;
    
    public function __construct($id, Measure $measure, $date = null)
    {
        $this->id = $id;
        $this->measure = $measure;
        $this->date = !$date ? date("Y-m-d H:i:s") : $date;
    }
    
    public function save()
    {
        
    }
}
