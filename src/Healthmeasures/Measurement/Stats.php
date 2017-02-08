<?php
//Documentation: http://jpgraph.net/download/manuals/chunkhtml/ch14s10.html

namespace Healthmeasures\Measurement;
use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;
use Amenadiel\JpGraph\Graph\DateLine;
use Healthmeasures\Configuration\Application;

class Stats
{
    /** Graph types to choose from **/
    const GRAPH_LINEAR = "linear";
    const GRAPH_BARS = "bars";
    
    public $id;
    /** 
     * @type Value
     * Data receieved to make the graph 
     * **/
    public $data;
    /** created_at values **/
    public $xAxis;
    public $createdAts;
    /** Values from the measures **/
    public $yAxis;
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
    /**Use this var if you want to store your graph as jpg instead of render it to the browser**/
    public $image_path;
    public $url_image_path;

    public function __construct($data)
    {
        $this->data = $data;
        $this->setAxis();
        $this->setMeasure();
        $this->setSimpleStats();
        $this->setTitle();
    }
    
    public function setTitle($title = null)
    {
        $t = $title ? $title : $this->getDefaultTitle();
        $this->title = rawurldecode($t);
        if ($this->id) {
            $this->resetId();
        }
    }
 
    public function getDefaultTitle()
    {
        return $this->measure ? $this->measure->name . " ({$this->measure->unit})" : "";
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function resetId()
    {
        $this->id = md5($this->id . $this->getTitle());
    }
    
    protected function setId(Array $ids)
    {
        $this->id = md5(implode(',', $ids) . $this->getTitle());
    }
    
    protected function setAxis()
    {
        $datay = array();
        $datax = array();
        $createdAts = array();
        $ids = array();
        $id = null;
        
        foreach ($this->data as $val) {
            $datax[] = strtotime(explode(" ", $val->created_at)[0]); //only leave the date part
            $createdAts[] = $val->created_at;
            $datay[] = $val->value;
            $ids[] = $val->id;
        }
        
        $this->xAxis = $datax;
        $this->createdAts = $createdAts;
        $this->yAxis = $datay;
        $this->setId($ids);
    }
    
    protected function getMedian()
    {
        $vals = $this->yAxis;
        $count = count($vals);
        sort($vals);
        $mid = floor(($count-1)/2);
        return ($vals[$mid] + $vals[$mid + 1 - $count % 2]) / 2;
    }

    protected function setSimpleStats()
    {
        $this->max_value = max($this->yAxis);
        $this->min_value = min($this->yAxis);
        $this->avg_value = array_sum($this->yAxis) / count($this->yAxis);
        
        $values = array_count_values($this->yAxis);
        $this->mode_value = array_search(max($values), $values);

        $this->median_value = $this->getMedian();
    }
    
    protected function setMeasure()
    {
        $val = $this->data[0];
        $m = new Measure(); 
        $this->measure = $m->getById($val->measure_id);
        if (!$this->measure) {
            throw new \Exception("Measure with id `{$val->measure_id}` not found, this stats report will be unstable");
        }
    }
 
    /**
     * Stores the image of a graph in the image_path place, if you leave it empty you render it to the browser.
     * The default graph type is "linear" but you can choose bars too
     * @see constants
     * @return bitmap
     */    
    public function generateDateMeasureGraph($graph_type = "linear")
    {    
        if (!defined('NDATAPOINTS')) {
            DEFINE('NDATAPOINTS', count($this->yAxis));
        }
        if (!defined('SAMPLERATE')) {
            DEFINE('SAMPLERATE', 100);
        }
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
        
        $path = $this->image_path ? $this->image_path : '';
        $graph->Stroke($path);
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
        $app = new Application();
        $info_values = array();
        $pattern1 = $app->config->get('htmlReport.row_stat');
        $pattern2 = $app->config->get('htmlReport.row_value');
        $all = $this->getCompleteStatsInformation();
        
        $date_value = $all['Data Table'];
        unset($all['Data Table']);
        foreach ($all as $key => $val) {
            $info_values[] = str_replace(array('{{ key }}', '{{ value }}'), array($key, $val), $pattern1);
        }
        
        foreach ($date_value as $key => $val) {
            $values[] = str_replace(array('{{ key }}', '{{ value }}'), array($key, $val), $pattern2);
        }

        $image = $this->getPreferredImagePath(); 
        $html = $app->config->get('htmlReport.html');
        $html = str_replace(
                array('{{ report_title }}', '{{ css }}', '{{ today_date }}', '{{ graph_image }}', '{{ info_values }}', '{{ values }}',),
                array($this->getTitle(), $app->config->get('htmlReport.css'), date($app->config->get('htmlReport.today_day_format')),
                    $image, implode('', $info_values), implode('', $values)
                ), $html
        );
        return $html;
    }
    
    public function getPreferredImagePath()
    {
        return $this->url_image_path ? $this->url_image_path : $this->image_path;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function toArray()
    {
        return 
            ['type' => 'Stats', 
             'id' => $this->getId(), 
             'attributes' => [
                'xAxis' => $this->xAxis,
                'yAxis' => $this->yAxis,
                'max_value' => $this->max_value,
                'min_value' => $this->min_value,
                'avg_value' => $this->avg_value,
                'median_value' => $this->median_value,
                'mode_value' => $this->mode_value,
                'title' => $this->title,
                'legend' => $this->legend,
                'color' => $this->color,
                'graph_width' => $this->graph_width,
                'graph_height' => $this->graph_height,
                'image_path' => $this->getPreferredImagePath()
             ],
             'relationships' => [
                 'measure' => $this->measure,
                 'data' => $this->data
             ],
             "links" => [
                "report" => ""
              ]
            ];

    } 
}

