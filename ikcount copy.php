<?php 

date_default_timezone_set('America/Santiago');
$hoy = date("Y-m-d");
$hoylog = date("Y-m-d  H:i:s");
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://54.85.73.208/iklab/ikcount/api/countingdataV2?appKey=JDJiJDEwJDdSSVE4ZmVIb0g0Y0M3WGpmRTBJUGVaNVNwZzNYS25QdEo2OFZvelRQNnhsWHRTVEwuZTlTOmFkbWluOklLTEFCMDA1',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,           
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  //CURLOPT_POSTFIELDS =>'{"dataType":"summary","locations":["WITCL001A1L1","WITCL001A1L2","WITCL001A1L3","WITCL001A1L4","WITCL001A1L5","WITCL001A1L6","WITCL001A1L7","WITCL001A1L8"],"filterType":3,"month":5,"year":2023,"detailed":false,"adjEvents":false}',
  CURLOPT_POSTFIELDS =>'{"dataType":"summary","locations":["WITCL001A1L1","WITCL001A1L2","WITCL001A1L3","WITCL001A1L4","WITCL001A1L5","WITCL001A1L6","WITCL001A1L7","WITCL001A1L8","WITCL001A1L9","WITCL001A1L10"],"filterType":1,"day":"'.$hoy.'","detailed":false,"adjEvents":false}',
  CURLOPT_HTTPHEADER => array(
    'Accept: application/json, text/javascript, */*; q=0.01',
    'Accept-Language: es-419,es;q=0.9,en;q=0.8',
    'Connection: keep-alive',
    'Content-Type: application/json; charset=UTF-8',
    'Cookie: iklab_session=emikhnk5sdlgt18bso0nahb0l647pg3u',
    'Origin: http://35.174.17.76',
    'Referer: http://35.174.17.76/ikcount/home',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36',
    'X-Requested-With: XMLHttpRequest'
  ),
));

$response = curl_exec($curl);

curl_close($curl);



$array=json_decode($response);

$data=$array->data ;

$i=0;

foreach ($data as $contador){
    
    $location_id=$contador->location_id ;
    $location_name=$contador->location_name ;
   $entrada=$contador->summary->counting_in;
    $salida=$contador->summary->counting_out;


    $item = array(
        "id_contador"=>$location_id,
        "patente"=>$location_name,
        "entrada"=>$entrada,
        "salida"=>$salida
    );
    
    $contadores[$i]=$item;

    $i++;

}

echo json_encode($contadores);


?>