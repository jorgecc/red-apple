<?php

use eftec\bladeone\BladeOne;
use eftec\PdoOne;
use eftec\SpiderOne\SpiderETL;
use eftec\SpiderOne\SpiderOne;

include "../vendor/autoload.php";

$blade=new BladeOne();


$pdo=new PdoOne('mysql','127.0.0.1','root','abc.123','weather');
$pdo->logLevel=3;
$pdo->open();

$year=@$_GET['year'];
if (!$year) $year=2019;

$stationList=$pdo->select('*')->from('stations')->toList();

use Phpml\Regression\LeastSquares;

$x = [[323], [373], [423], [473], [523], [573]];
$y = [.038, .046, .053, .062, .071, .080];
var_dump($x);

$regression = new LeastSquares();
$regression->train($x, $y);
//echo $regression->predict([428]);

$placemarks="";
foreach($stationList as &$stat) {
    $stat['Name']=str_replace("'"," ",$stat['Name']);
    $stationID=$stat['StationID'];
    $items=$pdo->select("SELECT YEAR(MAX(DATE))-1 MAXDATE,YEAR(MIN(DATE))+1 MINDATE FROM WeatherStat where stationid='$stationID'")->first();
    $min=$items['MINDATE'];
    $max=$items['MAXDATE'];
    $stat['currentMedia']='Missing data';
    $stat['Predict'] = "Not enough data";
    $stat['Class']='bg-secondary';
    if($max!==null) {
        $stat['listTemp']=$pdo->select("SELECT year(date) year,AVG(Avg) avg FROM WeatherStat WHERE stationid='$stationID' 
                AND YEAR(DATE) BETWEEN $min AND $max group by year(date) order by year(date)")->toList();
        $x=[];
        $y=[];
        if(count($stat['listTemp'])>3) {
            foreach ($stat['listTemp'] as $rowtemp) {
                $x[] = [$rowtemp['year']+0];
                $y[] = $rowtemp['avg']+0;
            }

            $regression = new LeastSquares();
            $regression->train($x, $y);
            $stat['Predict'] = $regression->predict([$year]);
        } 
        

        $media = $pdo->select("SELECT AVG(Avg) PROMEDIO FROM WeatherStat WHERE stationid='$stationID'
                AND YEAR(DATE) BETWEEN $min AND $max")->firstScalar()+0;
        $currentMedia = $pdo->select("SELECT AVG(Avg) PROMEDIO FROM WeatherStat WHERE stationid='$stationID'
                AND YEAR(DATE)=$max")->firstScalar()+0;
        $stat['currentMedia']=$currentMedia;
        
        if ($stat['Predict']!="Not enough data") {
            if ($stat['Predict']<$currentMedia) {
                $stat['Class'] = 'bg-primary';
            } else {
                $stat['Class'] = 'bg-danger';
            }
        }
        
        /*echo "<pre>";
        var_dump($stat);
        var_dump($items);
        echo "</pre>";
        */
        $delta=abs($media-$currentMedia);
        if ($media<$currentMedia) {
            $placemarks.= "addPlacemark({$stat['Lat']},{$stat['Lon']},'{$stat['Name']}\\n{$delta}','sunmini.png');\n";
        } else {
            $placemarks.= "addPlacemark({$stat['Lat']},{$stat['Lon']},'{$stat['Name']}\\n{$delta}','snowflakemini.png');\n";
        }
    }
    
}

//var_dump($stationList);
//die(1);
/*
 *   array(5) {
    ["StationID"]=>
    string(5) "02022"
    ["Name"]=>
    string(6) "Abisko"
    ["Location"]=>
    string(27) "68-21N    018-49E"
    ["Lat"]=>
    string(5) "68.21"
    ["Lon"]=>
    string(5) "18.49"
  }
  [1]=>
 */

echo $blade->run('dashboard',['placemarks'=>$placemarks,'stationList'=>$stationList,'year'=>$year]);