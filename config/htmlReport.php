<?php

/**
 * Configure here all the css and html you want for your html report
 */

$cssReport = array();

$cssReport['today_day_format'] = 'l jS \of F Y h:i:s A';

$cssReport['row_value'] = <<< HTML
<tr>
    <td class="priority3">{{ key }}</td>
    <td class="number">{{ value }}</td>
</tr>
HTML;

$cssReport['row_stat'] = <<< HTML
<tr>
    <td class="priority2">{{ key }}</td>
    <td class="number">{{ value }}</td>
</tr>
HTML;

$cssReport['html'] = <<< HTML
<html>
   <head>
      <title>{{ report_title }}</title>
      <style>{{ css }}</style>
      <body>
        <h1>{{ report_title }}</h1>
        <div class="metadata">
           <table>
              <tbody>
                 <tr>
                    <td class="em">Report title:</td>
                    <td>{{ report_title }}</td>
                 </tr>
                 <tr>
                    <td class="em">Date:</td>
                    <td>{{ today_date }}</td>
                 </tr>
                 <tr>
                    <td class="em">Generated with:</td>
                    <td><a href="https://github.com/towerhouse/Healthmeasures">HealthMeasures by THS</a></td>
                 </tr>
              </tbody>
           </table>
        </div>
        <div class="summary">
            <h2>Graph</h2>
            <img src="{{ graph_image }}">
        </div>
        <div class="summary">
            <h2>Stat values</h2>
            <table>
                <tbody>
                    <tr class="tableHeader">
                        <th>Stat</th>
                        <th>Value</th>
                    </tr>
                    {{ info_values }}
                </tbody>
            </table>
        </div>
        <div class="summary">
            <h2>Summary</h2>
            <table>
                <tbody>
                    <tr class="tableHeader">
                        <th>Date</th>
                        <th>Value</th>
                    </tr>
                    {{ values }}
                </tbody>
            </table>
        </div>
    </body>
</html>
HTML;

$cssReport['css'] = <<< CSS
body {
    font-family: Arial, sans-serif;
    margin: 20px 20px 20px 30px;
}
h1,
h2,
h3 {
    font-weight: bold;
}
h1 {
    width: 400px;
    text-align: center;
    color: white;
    background-color: #557799;
    padding: 10px;
    -moz-box-shadow: 3px 3px 4px #AAA;
    -webkit-box-shadow: 3px 3px 4px #AAA;
    box-shadow: 3px 3px 4px #AAA;
    border-radius: 10px;
    -moz-border-radius: 10px;
    text-shadow: 2px 2px 2px black;
}
h2 {
    font-size: 150%;
    margin-top: 40px;
    padding-top: 5px;
    border-top: 5px solid lightgray;
}
h3 {
    margin-left: 10px;
    margin-top: 30px;
}
a {
    text-decoration: underline;
    color: #D93544;
}
.logo {
    float: right;
}
.metadata {} .summary {
    margin-bottom: 20px;
}
.reportInfo {
    font-size: 110%;
}
.allPackages {
    font-weight: bold;
}
.fileHeader {
    font-size: 120%;
    font-weight: bold;
}
.tableHeader {
    font-weight: bold;
}
.number {
    text-align: center;
}
.priority1,
.priority2,
.priority3,
.priority4 {
    font-weight: bold;
    text-align: center;
    color: #990000;
}
.priority1 {
    background-color: #FFAAAA;
}
.priority2 {
    background-color: #FFCCAA;
}
.priority3 {
    background-color: #FFEEAA;
}
.ruleName {
    font-weight: bold;
    color: black;
    text-align: left;
}
.violationInfo {
    margin-bottom: 2px;
    margin-top: 2px;
}
.violationInfoPrefix {
    font-size: 60%;
    width: 30px;
    color: #a9a9a9;
    padding-right: 4px;
}
.sourceCode {
    font-family: Arial, sans-serif;
    font-size: 80%;
    color: #444444;
}
.violationMessage {
    font-style: italic;
    font-size: 80%;
    color: black;
}
.ruleDescriptions {
    font-size: 85%;
}
.version {
    margin-top: 1px;
}
.buttons button {
    margin-right: 10px;
    margin-bottom: 10px;
}
table {
    border: 2px solid gray;
    border-collapse: collapse;
    -moz-box-shadow: 3px 3px 4px #AAA;
    -webkit-box-shadow: 3px 3px 4px #AAA;
    box-shadow: 3px 3px 4px #AAA;
}
td,
th {
    border: 1px solid #D3D3D3;
    padding: 4px 15px 4px 15px;
    margin: 20px 15px 20px 15px;
}
th {
    text-shadow: 2px 2px 2px white;
}
th {
    border-bottom: 1px solid gray;
    background-color: #DDDDFF;
}
em,
.em {
    font-weight: bold;
}
CSS;

return $cssReport;

