<?php

namespace Healthmeasures\Test\Measurement;

use Healthmeasures\Measurement\Measure;
use Healthmeasures\Measurement\Value;
use Healthmeasures\Measurement\Stats;


class StatsTest extends \PHPUnit_Framework_TestCase
{
    public function testCheckEnv()
    {
        $env = \Dotenv::load(__DIR__ . '/../../');
        $this->assertEquals('test', getenv('APP_ENV'), "Your env is $env ");
    }

    protected function getDefaultMeasure()
    {
        $m = new Measure("statscsv", "inch", "en");
        $m->save();
        return $m;
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
        $db_rows_count = $v->countAll();
        $this->assertGreaterThanOrEqual($csv_rows_count, $db_rows_count, "$csv_rows_count is greater than $db_rows_count");
        
        //Run again and the number of values in the database should be the same
        $v2 = new Value();
        $v2->bulkConstructor($path);
        $v2->getAll();
        $db_rows_count2 = $v2->countAll();
        $this->assertEquals($db_rows_count, $db_rows_count2, "Run again and the number of values in the database should be the same $db_rows_count == $db_rows_count2");
    }
    
    public function testAxisCountIsTheSameAsTestDataCount()
    {
        
    }
    
    public function testMedian()
    {
        
    }
    
    public function testMax()
    {
        
    }

    public function testMin()
    {
        
    }
    
    public function testAvg()
    {
        
    }

    public function testMode()
    {
        
    }
    
    public function testDefaultTitle()
    {
        
    }
    
    public function testGraphImageIsGenerated()
    {
        
    }
    
    public function testGetCompleteStatsInformation()
    {
        
    }
    
    public function testHtmlReportIsGenerated()
    {
        
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
        unlink($csv_path);// Delete obsolete BD
        rename($csv_path . '2', $csv_path); //Rename temporary to new
    }
}

