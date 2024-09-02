<?php 
//$hashed='517f95d2f26dfd844a8d7e07e3aa45b1';
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://www.trackermasgps.com/api-v2/tracker/list',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{"hash": "'.$hashed.'"}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$list = curl_exec($curl);

curl_close($curl);

$array2=json_decode($list);

$ids=$array2->list;

?>