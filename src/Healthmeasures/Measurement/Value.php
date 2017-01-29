<?php

namespace Healthmeasures\Measurement;

class Value extends Persistance
{
    /**
     *
     * @var type Measure
     */
    public $measure_id;
    
    /**
     * Owner of the measure
     * @var string
     */
    public $owner_id;
    
    public $value;
    
    public $created_at;

    
    public function __construct($owner_id = null, $measure_id = null, $created_at = null, $value = null)
    {
        parent::__construct();
        $this->owner_id = $owner_id;
        $this->measure_id = $measure_id;
        $this->value = $value;
        $this->created_at = !$created_at ? date("Y-m-d H:i:s") : $created_at;
        $this->setId();
    }
       
    /** Static getters to retrieve collections or one object **/
    
    public function getValueById($id)
    {
        return $this->getObjectsByCriteria(array('id' => $id));
    }
    
    /**
     * Returns a list of values ordered by date desc
     * @param datetime $start
     * @param datetime $end
     * @return Array[Value]
     */
    public function getValuesByDate($owner_id, $measure_id, $start, $end = "now")
    {
        if (!strtotime($start) || ($end != "now" && !strtotime($end))) {
            throw new \Exception("Date parameters are not clean");
        }
        
        $end = ($end == "now") ? date("Y-m-d H:i:s") : $end;
        return $this->getObjectsByCriteria(
                array('owner_id' => $owner_id, 'measure_id' => $measure_id), 
                array("created_at BETWEEN '$start' AND '$end' ORDER BY created_at ASC")
        );
    }
      
    public function getSaveProperties()
    {
        return array('id', 'owner_id', 'measure_id', 'value', 'created_at');
    }
    
    public function setId()
    {
        $this->id = md5($this->owner_id . $this->measure_id . $this->value . $this->created_at);
    }
    
    public function save()
    {
        if ($this->value === null) {
            throw new \Exception("The value in a Value object cannot be null");
        }
        if ($this->owner_id === null) {
            throw new \Exception("The owner_id in a Value object cannot be null");
        }
        if ($this->measure_id === null) {
            throw new \Exception("The measure_id in a Value object cannot be null");
        }
        if ($this->created_at === null) {
            throw new \Exception("The date taken in a Value object cannot be null");
        }
        return parent::save();
    }

}
