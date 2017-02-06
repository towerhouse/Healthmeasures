# Health Measures

[![towerhousestudio](http://towerhousestudio.com/wp-content/uploads/2016/04/nuevo-logo-towerhouse2-1s-300x296.png)](http://towerhousestudio.com)

- This library gives you the ability to generate health measures given a name and a unit, for instance: weight / pound or kilo. 
- You can configure where to store them and them gather to collect their values by date given a username or other identifier. 
- The library gives you the ability to generate graphs or other stats information. It's totally configurable, methods are well documented and comes with unit testing.

### Tech

Health Measures uses a number of open source projects to work properly:

* [PHP] - <= 5.3
* [illuminate/config] - 5 . This is the config handler that environments like laravel 4.2 used to have.
* [amenadiel/jpgraph"] - ^3.6 . This guy ported all the jpgraph library to packagist, thank you.

### Installation

- composer require towerhouse/healthmeasures
- Include the autoloader. 
- A file on the test's folder test-creation.php will give you a quick review of what the library does.
- Merge the values from .env.example you want to use in your env or just rename the example to .env.
- This package should work standalone.

### Example of use

Select your storage method on the .env file

Enter your measures one by one or using the bulk method

```php
//One by one
$mm = new Measure("waist", "cm", "en");
$mm->save();
$mm2 = new Measure("imc", "kg/m2", "es");
$mm2->save();
$mm3 = new Measure("systole", "", "en");
$mm3->save();

//Using the bulk method with a CSV file that has a header
//Don't worry about duplicates

Measure::setDefaultLanguage('es'); //All my csv measures are in spanish
$mm->bulkConstructor(__DIR__ . '/CSV/Measure.csv');
```

Example of content in Measure.csv

```
name,unit
sistole,	
diastole,
pulso,
peso,kilo
altura,cm
azucar en sangre,mg/dl
Saturación de oxígeno en sangre,SaO2
temperatura,ºC
```

Now enter your values, it's the same as measures so, for simplicity we will use the bulk method

```php
$v = new Value();
$v->bulkConstructor(__DIR__ . '/CSV/Value.csv');
//Again, don't worry about duplicates
```

Example of content in Value.csv
```
measure_id,value,owner_id,created_at
2577a46ca60e3ff293ccb0113e6a59c0,97,1,2016-12-29 07:00:00
2577a46ca60e3ff293ccb0113e6a59c0,98,1,2016-12-30 08:00:00
2577a46ca60e3ff293ccb0113e6a59c0,98,1,2016-12-31 06:30:00
2577a46ca60e3ff293ccb0113e6a59c0,98,1,2017-01-01 07:00:00
2577a46ca60e3ff293ccb0113e6a59c0,98,1,2017-01-02 07:00:00
2577a46ca60e3ff293ccb0113e6a59c0,95,1,2017-01-10 11:00:00
2577a46ca60e3ff293ccb0113e6a59c0,95,1,2017-01-15 07:00:00
2577a46ca60e3ff293ccb0113e6a59c0,97,1,2017-01-25 11:00:00
```

Notice that the owner_id comes from an external system, while measure_id belongs to Healthmeasures. In this case measure_id "2577a46ca60e3ff293ccb0113e6a59c0" it's the id for the measure "waist".

Finally we retrieve all the values that the person with identifier 1 had for his waist starting from "2016-01-01" and ending in the most recent date (that would be today, otherwise you specify it on another last parameter)

```
$vals = $v->getValuesByDate(1, '2577a46ca60e3ff293ccb0113e6a59c0', "2016-01-01");
```

Let's create a Stats object and pass the values to generate a linear graph. You can specify a path and the picture will be saved there, otherwise it will be rendered to the client through the browser.

```
$stats = new Stats($vals);
$stats->image_path = "linear_sample.jpg";
$stats->generateDateMeasureGraph(Stats::GRAPH_LINEAR);
```

Finally, you can render a nice and simple html report to your browser with all the details

```
$stats = new Stats($vals);
$stats->image_path = "linear_bar.jpg";
if (!file_exists("linear_bar.jpg")) {
    $stats->generateDateMeasureGraph(Stats::GRAPH_LINEAR);
}
echo $stats->getHtmlReport();
```

This is a pdf I took from the html page.
https://github.com/towerhouse/Healthmeasures/tree/master/test/report.pdf

...and this is the image of the graph
![Alt text](https://raw.githubusercontent.com/towerhouse/Healthmeasures/master/test/linear_bar.jpg?raw=true)

### Unit testing

There is an article about unit testing I wrote for this library documented here:
http://towerhousestudio.com/easy-unit-testing-for-composer-package

