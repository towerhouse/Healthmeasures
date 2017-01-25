<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';

use Healthmeasures\Measurement\Measure;

$mm = new Measure("cintura", "cm", "es");
$mm->save();
$mm2 = new Measure("peso", "kilo", "es");
$mm2->save();
$mm3 = new Measure("systole", "", "en");
$mm3->save();


var_export($mm->getMeasuresByLang('es'));

var_export($mm->getAll());

