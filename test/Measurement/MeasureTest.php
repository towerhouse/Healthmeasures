<?php

namespace Healthmeasures\Test\Measurement;

use Healthmeasures\Measurement\Measure;


class MeasureTest extends \PHPUnit_Framework_TestCase
{
    public function testCheckEnv()
    {
        $env = \Dotenv::load(__DIR__ . '/../../');
        $this->assertEquals('test', getenv('APP_ENV'), "Your env is $env ");
    }

    /**
     * Checks if the language change for every object after
     * using the static method setDefaultLanguage
     */
    public function testChangeDefaultLanguage()
    {
        $this->assertEquals('en', Measure::getDefaultLanguage());
        
        Measure::setDefaultLanguage('es');
        $this->assertEquals('es', Measure::getDefaultLanguage());
        $m = new Measure();
        $this->assertEquals('es', $m->lang);
    }
  
        
    public function testStoreSingleObjectDb()
    {
        $m = new Measure("waist", "cms", "en");
        $this->assertNotNull($m->getId(), "Not saved measure has id " . $m->getId());
        $w = $m->save(); //$w is the same object as $m
        $this->assertNotNull($w->getId(), "Saved measure has id " . $w->getId());
        $this->assertGreaterThan(0, $m->countAll());
        $this->assertEquals($m->getId(), $w->getId(), "The waist measure was stored on the database with id " . $m->getId());
    }
    
    public function testBulkConstructor()
    {
        Measure::setDefaultLanguage('es');
        $mm = new Measure();

        $path = __DIR__ . '/../CSV/Measure.csv';
        $mm->bulkConstructor($path);
        $mm->getAll();
        $csv_rows_count = $this->countCsvFileLines($path);
        $this->assertGreaterThanOrEqual($csv_rows_count, $mm->countAll());
    }
    
    public function testSameMeasuresAreNotStoredTwice()
    {
        $randName = "DummyMeasure " . strtotime('YmdHis');
        $m1 = new Measure($randName, "cm", "en");
        $m1->save();
        $id = $m1->getId();
        $this->assertEquals($m1->getId(), $m1->getById($id)->getId());

        $m2 = new Measure($randName, "cm", "en");
        $m2->save();
        $id2 = $m2->getId();
        $this->assertEquals($id, $id2);
        $this->assertEquals(1, count($m1->getMeasuresByName($randName)));
    }
    
    public function testSaveConstraintsAreMet()
    {
        $m = new Measure('', 'L', 'es');
        //Empty object
        $this->setExpectedException('\Exception');
        $m->save();
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

