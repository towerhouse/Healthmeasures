<?php

namespace Healthmeasures\Measurement;

class Value extends Persistance
{
    /**
     *
     * @var type Measure
     */
    protected $measure_id;
    
    /**
     * Owner of the measure
     * @var string
     */
    protected $owner_id;
    
    protected $created_at;

    
    public function __construct($owner_id = null, $measure_id = null, $date = null, $value = null)
    {
        parent::__construct();
        $this->owner_id = $owner_id;
        $this->measure_id = $measure_id;
        $this->value = $value;
        $this->created_at = !$date ? date("Y-m-d H:i:s") : $date;
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
        $end = ($end == "now") ? date("Y-m-d H:i:s") : $end;
        return $this->getObjectsByCriteria(
                array('owner_id' => $owner_id, 'measure_id' => $measure_id), 
                array('created_at' => "BETWEEN $start AND $end")
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

}
