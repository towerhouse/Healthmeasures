<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';
use Healthmeasures\Measurement\Measure;
use Healthmeasures\Measurement\Value;
use Healthmeasures\Measurement\Stats;

$mm = new Measure("cintura", "cm", "es");
$mm->save();
$vv = new Value();

$mm2 = new Measure("imc", "kg/m2", "es");
$mm2->save();
$mm3 = new Measure("systole", "", "en");
$mm3->save();


//     'owner_id' => '1',
//     'measure_id' => '2577a46ca60e3ff293ccb0113e6a59c0',
//     'created_at' => '2016-12-30 08:00:00',
//     'value' => '98',

$vv2 = new Value(1, '1ecf550e3e22e6f96cb9c1d8105118d2', '2016-12-30 08:00:00', 98);
$vv2->save();

$mm->getMeasuresByLang('es');

$mm->getAll();

Measure::setDefaultLanguage('es');
$mm->bulkConstructor(__DIR__ . '/CVS/Measure.csv');
$vv->bulkConstructor(__DIR__ . '/CVS/Value.csv');

$v = new Value();
$vals = $v->getValuesByDate(1, '2577a46ca60e3ff293ccb0113e6a59c0', "2016-01-01");
$stats = new Stats($vals);
$stats->generateDateMeasureGraph("", Stats::GRAPH_LINEAR);
//$stats->generateDateMeasureGraph("", Stats::GRAPH_BARS);
