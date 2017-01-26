<?php

//Documentation: http://jpgraph.net/download/manuals/chunkhtml/ch14s10.html

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';

use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;
use Amenadiel\JpGraph\Graph\DateLine;
use Healthmeasures\Measurement\Value;
use Healthmeasures\Measurement\Measure;

$m = new Measure();
$m = $m->getById('2577a46ca60e3ff293ccb0113e6a59c0');

$v = new Value();
$vals = $v->getValuesByDate(1, '2577a46ca60e3ff293ccb0113e6a59c0', "2016-01-01");

$datay = array();
$datax = array();

foreach ($vals as $val) {
    $datax[] = strtotime($val->created_at);
    $datay[] = $val->value;
}

DEFINE('NDATAPOINTS', count($datay));
DEFINE('SAMPLERATE', 100);
$start = $datax[0]; //time();
$end = $start + NDATAPOINTS * SAMPLERATE;

// Create the new Graph\Graph
$graph = new Graph\Graph(540, 300);

// Slightly larger than normal margins at the bottom to have room for
// the x-axis labels
$graph->SetMargin(30, 30, 20, 110);

// Fix the Y-scale to go between [0,100] and use date for the x-axis
$graph->SetScale('datlin', 0, 150);
$graph->title->Set($m->name);

// Set the angle for the labels to 90 degrees
$graph->xaxis->SetLabelAngle(90);

// The automatic format string for dates can be overridden
$graph->xaxis->scale->SetDateFormat('Y-d-m');

// Adjust the start/end to a specific alignment
///$graph->xaxis->scale->SetTimeAlign(HOURADJ_1);

$line = new Plot\LinePlot($datay, $datax);
//$line->SetLegend('Years 2016 - 2017');
$line->SetColor('#1E90FF');
$graph->Add($line);
$graph->Stroke();
