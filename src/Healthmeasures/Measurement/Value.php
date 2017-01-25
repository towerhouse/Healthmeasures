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

    
    public function __construct($id, Measure $measure, $date = null)
    {
        parent::__construct();
        $this->owner_id = $id;
        $this->measure_id = $measure->getId();
        $this->created_at = !$date ? date("Y-m-d H:i:s") : $date;
    }

    public function getSaveProperties()
    {
        return array('id', 'owner_id', 'measure_id', 'created_at');
    }

    public function setId()
    {
        $this->id = md5($this->owner_id . $this->measure_id);
    }

}
