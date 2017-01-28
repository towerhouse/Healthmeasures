<?php

namespace Healthmeasures\Test\Measurement;

use Healthmeasures\Measurement\Measure;
use Healthmeasures\Configuration\Application;

class MeasureTest extends \PHPUnit_Framework_TestCase
{
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
    
    public function testBulkConstructor()
    {
        Measure::setDefaultLanguage('es');
        $mm = new Measure();
        
        //$this->cleanDb();
        $mm->bulkConstructor(__DIR__ . '/../CSV/Measure.csv');
        $mm->getAll();
    }
    
    public function testCleanSqliteDb()
    {
        //Remove sqlite database and reconstruct
        $app = new Application();
        $app->config->set('database.db_engine', 'sqlite');
        $this->assertEquals($app->config->get('database.db_engine'), 'sqlite');
        $file = $app->config->get('database.connections.sqlite.database');
        if (file_exists($file)) {
            unlink($file);
            $this->assertEquals(file_exists($file), false);
        } else {
            $this->assertTrue(true);
        }
    }
    
    public function testCreateSqliteDb()
    {
        //Remove sqlite database and reconstruct
        $app = new Application();
        $app->config->set('database.db_engine', 'sqlite');
        $file = $app->config->get('database.connections.sqlite.database');
        
        $m = new Measure();
        $m->save();
        $this->assertEquals(file_exists($file), true, "$file is supposed to exist");
    }
}

