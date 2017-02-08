<?php

namespace Healthmeasures\Test\Measurement;

use Dotenv\Dotenv;
use Healthmeasures\Measurement\Measure;
use Healthmeasures\Measurement\Value;
use Healthmeasures\Measurement\Stats;


class StatsTest extends \PHPUnit_Framework_TestCase
{
    protected $stats;
    
    public function testCheckEnv()
    {
        $dotenv = new Dotenv(__DIR__ . '/../../');
        $dotenv->load();
        $this->assertEquals('test', getenv('APP_ENV'));
    }

    protected function getDefaultMeasure()
    {
        $m = new Measure("statscsv", "inch", "en");
        $m->save();
        return $m;
    } 
    
    protected function getDefaultOwnerId()
    {
        return 'test';
    }
    
    protected function getDefaultStats()
    {
        if (!$this->stats) {
            $v = new Value();
            $values = $v->getValuesByDate($this->getDefaultOwnerId(), $this->getDefaultMeasure()->getId(), '2016-01-01');
            $this->stats = new Stats($values);
        }
        return $this->stats;
    }
    
    public function testBulkConstructor()
    {
        $mm = $this->getDefaultMeasure();
        $path = __DIR__ . '/../CSV/StatsValues.csv';
        //Replace measure id placeholder
        $this->replaceCsvFirstColumnIds($path, $mm->getId());
        $v = new Value();
        $v->bulkConstructor($path);
        $v->getAll();
        $csv_rows_count = $this->countCsvFileLines($path);
        $db_rows_count = count($v->getValuesByDate($this->getDefaultOwnerId(), $this->getDefaultMeasure()->getId(), '2016-01-01'));
        
        //Exclude the header value from $csv_rows_count
        $this->assertEquals($csv_rows_count - 1, $db_rows_count, "$csv_rows_count is equal than $db_rows_count");
        
        //Run again and the number of values in the database should be the same
        $v2 = new Value();
        $v2->bulkConstructor($path);
        $v2->getAll();
        $db_rows_count2 = count($v->getValuesByDate($this->getDefaultOwnerId(), $this->getDefaultMeasure()->getId(), '2016-01-01'));
        $this->assertEquals($db_rows_count, $db_rows_count2, "Run again and the number of values in the database should be the same $db_rows_count == $db_rows_count2");
    }
    
    public function testAxisCountIsTheSameAsTestDataCount()
    {
        $stats = $this->getDefaultStats();
        $count = count($stats->data);
        $this->assertTrue($count > 10);
        $this->assertEquals($count, count($stats->xAxis));
        $this->assertEquals($count, count($stats->yAxis));
    }
    
    public function testMedian()
    {
        $stats = $this->getDefaultStats();
        $this->assertEquals(97, $stats->median_value);
    }
    
    public function testMax()
    {
        $stats = $this->getDefaultStats();
        $this->assertEquals(98, $stats->max_value);
    }

    public function testMin()
    {
        $stats = $this->getDefaultStats();
        $this->assertEquals(93, $stats->min_value);
    }
    
    public function testAvg()
    {
        $stats = $this->getDefaultStats();
        $this->assertEquals(round(96.35), round($stats->avg_value));
    }

    public function testMode()
    {
        $stats = $this->getDefaultStats();
        $this->assertEquals(98, $stats->mode_value);
    }
    
    public function testDefaultTitle()
    {
        $m = $this->getDefaultMeasure();
        $stats = $this->getDefaultStats();
        return $this->assertEquals($m->name . " ({$m->unit})", $stats->getDefaultTitle());
    }
    
    public function testGraphImageIsGeneratedLinear()
    {
        $stats = $this->getDefaultStats();
        $rand = time() + rand(500, 600);
        $stats->image_path = "test_linear_$rand.jpg";
        $stats->generateDateMeasureGraph(Stats::GRAPH_LINEAR);
        $this->assertTrue(file_exists($stats->image_path));
        unlink($stats->image_path);
        
        $stats->image_path = "test_bar_$rand.jpg";
        $stats->generateDateMeasureGraph(Stats::GRAPH_BARS);
        $this->assertTrue(file_exists($stats->image_path));
        unlink($stats->image_path);
    }
    
    public function graphTitleDefaultsToReportTitle()
    {
        $stats = $this->getDefaultStats();
        $stats->setTitle("Name number 1");
        $stats->generateDateMeasureGraph();
        $this->assertEquals($stats->getGraphTitle(), $stats->getTitle());
        $stats->setTitle("Name number 2");
        $stats->generateDateMeasureGraph();
        $this->assertEquals($stats->getGraphTitle(), $stats->getTitle());
    }
    
    protected function countCsvFileLines($csv)
    {
        $c =0;
        $fp = fopen($csv, "r");
        if ($fp) {
            while (!feof($fp)) {
                $content = fgets($fp);
                if ($content) {
                    $c++;
                }
            }
        }
        fclose($fp);
        return $c;
    }
    
    protected function replaceCsvFirstColumnIds($csv_path, $new_id)
    {
        $input = fopen($csv_path, 'r');  //open for reading
        $output = fopen($csv_path . '2', 'w'); //open for writing
        while (false !== ($data = fgetcsv($input))){  //read each line as an array
            //Dont modify the header
            if ($data[0] != $new_id && $data[0] != 'measure_id') {
                $data[0] = $new_id;
            }
            //write modified data to new file
            fputcsv($output, $data);
        }

        //close both files
        fclose($input);
        fclose($output);

        //clean up
        unlink($csv_path);// Delete obsolete csv
        rename($csv_path . '2', $csv_path); //Rename temporary to new
    }
}

