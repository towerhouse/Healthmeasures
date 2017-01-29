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
        $randDate = '2010-10-10 10:10:10';
        $m = $this->getDefaultMeasure();
        $v1 = new Value(self::DEFAULT_OWNER_ID, $m->getId(), $randDate, "64.96");
        $v1->save();
        $id = $v1->getId();
        $this->assertEquals($v1->getId(), $v1->getById($id)->getId());

        $v2 = new Value(self::DEFAULT_OWNER_ID, $m->getId(), $randDate, "64.96");
        $v2->save();
        $id2 = $v2->getId();
        $this->assertEquals($id, $id2);
        $this->assertEquals(1, count($v1->getValuesByDate(self::DEFAULT_OWNER_ID, $m->getId(), $randDate, $randDate))); 
    }
    
    public function testSaveConstraintsAreMet()
    {
        $v = new Value();
        //Empty object
        $this->setExpectedException('\Exception');
        $v->save();
        
        //No value
        $v2 = new Value();
        $v2->owner_id = 2;
        $v2->measure_id = $this->getDefaultMeasure()->getId();
        $v2->created_at = "2017-01-01 08:30:32";
        $this->setExpectedException('\Exception');
        $v2->save();
        
        //No owner
        $v3 = new Value();
        $v3->value = 2;
        $v3->measure_id = $this->getDefaultMeasure()->getId();
        $v3->created_at = "2017-01-01 08:30:32";
        $this->setExpectedException('\Exception');
        $v3->save();
        
        //No measure
        $v4 = new Value();
        $v4->owner_id = 2;
        $v4->value = 50;
        $v4->created_at = "2017-01-01 08:30:32";
        $this->setExpectedException('\Exception');
        $v4->save();
        
        //No date
        $v5 = new Value();
        $v5->measure_id = $this->getDefaultMeasure()->getId();
        $v5->owner_id = 2;
        $v5->value = 50;
        $this->setExpectedException('\Exception');
        $v5->save();
    }
      
    public function testGetValuesByDateWithCrazyDatesNotAllowed()
    {
        $v = new Value();
        $this->setExpectedException('\Exception');
        $v->getValuesByDate(1, $this->getDefaultMeasure()->getId(), 'SQL INJECTION', 'DELETE FROM TABLE');
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

