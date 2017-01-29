<?php

namespace Healthmeasures\Test\Value;

use Healthmeasures\Measurement\Value;
use Healthmeasures\Measurement\Measure;

class ValueTest extends \PHPUnit_Framework_TestCase
{
    const DEFAULT_OWNER_ID = 12;
    
    protected function getDefaultMeasure()
    {
        $m = new Measure("height", "inch", "en");
        $m->save();
        return $m;
    }
    
    public function testCheckEnv()
    {
        $env = \Dotenv::load(__DIR__ . '/../../');
        $this->assertEquals('test', getenv('APP_ENV'), "Your env is $env ");
    }
        
    public function testStoreSingleObjectDb()
    {
        $m = $this->getDefaultMeasure();
        $v = new Value(self::DEFAULT_OWNER_ID, $m->getId(), date("Y-m-d H:i:s"), "64.96");
        $this->assertNotNull($v->getId(), "Not saved value has id " . $v->getId());
        $h = $v->save(); //$h is the same object as $v
        $this->assertNotNull($h->getId(), "Saved value has id " . $h->getId());
        $this->assertGreaterThan(0, $v->countAll());
        $this->assertEquals($v->getId(), $h->getId(), "The height measure value was stored on the database with id " . $v->getId());
    }
    
    public function testBulkConstructor()
    {
        Measure::setDefaultLanguage('es');
        $v = new Value();

        $path = __DIR__ . '/../CSV/Value.csv';
        $v->bulkConstructor($path);
        $v->getAll();
        $csv_rows_count = $this->countCsvFileLines($path);
        $this->assertGreaterThanOrEqual($csv_rows_count, $v->countAll());
    }
    
    public function testSameValuesAreNotStoredTwice()
    {
    }
    
    public function testSaveConstraintsAreMet()
    {
        
    }
      
    public function testValuesByDateWithCrazyDatesNotAllowed()
    {
        
    }
    
    public function testValuesWithDefaultMeasureAreReturned()
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
}

