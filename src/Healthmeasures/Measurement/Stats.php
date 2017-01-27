<?php
//Documentation: http://jpgraph.net/download/manuals/chunkhtml/ch14s10.html

namespace Healthmeasures\Measurement;
use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;
use Amenadiel\JpGraph\Graph\DateLine;

class Stats
{
    /** Graph types to choose from **/
    const GRAPH_LINEAR = "linear";
    const GRAPH_BARS = "bars";
    
    /** Data receieved to make the graph **/
    protected $data;
    /** created_at values **/
    protected $xAxis;
    protected $createdAts;
    /** Values from the measures **/
    protected $yAxis;
    /** Max value in the measures **/
    public $max_value;
    /** Min value in the measures **/
    public $min_value;
    /** avg or mean value in the measures **/
    public $avg_value;
    /** Median value in the measures (the value that gets in the middle of the y axis **/
    public $median_value;
    /** Most repeated value in the measures **/
    public $mode_value;
    /** Measure object used in the values range **/
    public $measure;
    /** Title for the graph **/
    public $title;
     /** Legend for the graph **/
    public $legend;
    /** Color for the graph, use color hexas or color names **/
    public $color = '#1E90FF';
    /**Size of the graph**/
    public $graph_width = 1024;
    public $graph_height = 534;
    public $image_path;

    public function __construct($data)
    {
        $this->data = $data;
        $this->setAxis();
        $this->setMeasure();
        $this->setSimpleStats();
    }
    
    protected function setAxis()
    {
        $datay = array();
        $datax = array();
        $createdAts = array();
        
        foreach ($this->data as $val) {
            $datax[] = strtotime(explode(" ", $val->created_at)[0]); //only leave the date part
            $createdAts[] = $val->created_at;
            $datay[] = $val->value;
        }
        
        $this->xAxis = $datax;
        $this->createdAts = $createdAts;
        $this->yAxis = $datay;
    }
    
    protected function setSimpleStats()
    {
        $this->max_value = max($this->yAxis);
        $this->min_value = min($this->yAxis);
        $this->avg_value = array_sum($this->yAxis) / count($this->yAxis);
        
        $values = array_count_values($this->yAxis); 
        $this->mode_value = array_search(max($values), $values);
        $this->median_value = rsort($values)[0];
    }
    
    protected function setMeasure()
    {
        $val = $this->data[0];
        $m = new Measure(); 
        $this->measure = $m->getById($val->measure_id);
    }


    public function getDefaultTitle()
    {
        return $this->measure->name . " ({$this->measure->unit})";
    }
    
    public function getTitle()
    {
        return $this->title ? $this->title : $this->getDefaultTitle();
    }
 
    /**
     * Stores the image of a graph in the image_path place, if you leave it empty you render it to the browser.
     * The default graph type is "linear" but you can choose bars too
     * @see constants
     * @return bitmap
     */    
    public function generateDateMeasureGraph($image_path = "", $graph_type = "linear")
    {
        $this->image_path = $image_path;
        
        DEFINE('NDATAPOINTS', count($this->yAxis));
        DEFINE('SAMPLERATE', 100);
        $start = $this->xAxis[0]; 
        $end = $start + NDATAPOINTS * SAMPLERATE;
        
        // Create the new Graph\Graph
        $graph = new Graph\Graph($this->graph_width, $this->graph_height);

        // Fix the Y-scale to go between [0,100] and use date for the x-axis
        $graph->SetScale('datlin', 0, $this->max_value + 20);
        //Set title
        $graph->title->Set($this->getTitle());
        
        // Set the angle for the labels to 90 degrees
        $graph->xaxis->SetLabelAngle(90);

        // The automatic format string for dates can be overridden
        $graph->xaxis->scale->SetDateFormat('Y-m-d');
        
        switch ($graph_type) {
            case static::GRAPH_BARS:
                $graph->SetShadow();
                $graph->img->SetMargin(30, 40, 20, 110);
                $b1plot = new Plot\BarPlot($this->yAxis, $this->xAxis);
                $b1plot->SetFillColor($this->color);
                $b1plot->value->Show();
                $graph->Add($b1plot);
                break;
            default:
                $line = new Plot\LinePlot($this->yAxis, $this->xAxis);
                $line->SetLegend($this->legend);
                // Slightly larger than normal margins at the bottom to have room for
                // the x-axis labels
                $graph->SetMargin(30, 30, 20, 110);
                $line->SetColor($this->color);
                $graph->Add($line); 
        }
        
        $graph->Stroke($image_path);
    }
    
    /**
     * It returns a table with X and Y values interpolated
     * and all the important stat measures.
     */
    public function getCompleteStatsInformation()
    {
        $info = array();
        $info['Data Table'] = array_combine($this->createdAts, $this->yAxis);
        $info['Max'] = $this->max_value;
        $info['Min'] = $this->min_value;
        $info['Avg'] = $this->avg_value;
        $info['Mode'] = $this->mode_value;
        $info['Median'] = $this->median_value;
        
        return $info;
    }
    
    public function getHtmlReport()
    {
        static::$app = new Application();
    }
}
