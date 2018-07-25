<?php

$data = array(
    array(2, 3, 'a'), 
    array(1, 1, 'b'),  
    array(0, 2, 'c'), 
    array(3, 3, 'd'),
    array(1, 4, 'e'),
    array(7, 2, 'f'),
);
$k=3;
$distances = $data;
array_walk($distances, 'ManhattenDistance', $data);
print_r($distances);
$nk=array();
$maxValue=array();
$neighbors=array();
$lrd=array();
$lof=array();
//get the neighbors
for($i=0;$i<count($distances);$i++){
    $neighbors[$i] = getNearestNeighbors($distances, $i, $k);
    $maxValue[$i]=max($neighbors[$i]);
    
}
//Nth K values
for($i=0;$i<count($distances);$i++){
    $nk[$i]=NthK($neighbors[$i],$maxValue[$i]);
}

//Lrd
for($i=0;$i<count($distances);$i++){
    $lrd[$i]=Lrdk($maxValue,$nk,$distances,$neighbors[$i],$i);
  
}
print_r($lrd);
//LOF
for($i=0;$i<count($distances);$i++){
    $lof[$i]=Lof($lrd,$maxValue,$nk,$distances,$neighbors[$i],$i);
  
}
$outlier = max(array_keys($lof));
echo $data[$outlier][2];

function ManhattenDistance(&$sourceCoords, $sourceKey, $data)
{   
    //print_r($data);
    $distances = array();
    list ($x1, $y1) = $sourceCoords;
    foreach ($data as $destinationKey => $destinationCoords) {
        // Same point, ignore
        if ($sourceKey == $destinationKey) {
            continue;
        }
        list ($x2, $y2) = $destinationCoords;
        $distances[$destinationKey] = abs($x1-$x2)+abs($y1-$y2);
    }
    asort($distances);
    $sourceCoords = $distances;
}

function getNearestNeighbors($distances, $key, $num)
{
    return array_slice($distances[$key], 0, $num, true);
}



function NthK($neighbors,$nk){
    $count=0;
    foreach ($neighbors as $key=>$value) {
        if($value<=$nk){
            $count++;
            //echo "[".$key."] =>".$value." ";
        }
    }
    return $count;
}

function Lrdk($maxValue,$nk,$distances,$neighbors,$i){
 $sum=0;
 foreach($neighbors as $key=>$value){
    $sum+=max($maxValue[$key],$distances[$key][$i]);
 }
 
   return round($nk[$i]/$sum,3);
}

function Lof($lrd,$maxValue,$nk,$distances,$neighbors,$i){
    $sum1=0;
    $sum2=0;
    foreach($neighbors as $key=>$value){
        $sum1+=$lrd[$key];
    }
    foreach($neighbors as $key=>$value){
       $sum2+=max($maxValue[$key],$distances[$key][$i]);
    }
    return $sum1*$sum2;
}