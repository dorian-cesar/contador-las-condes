<?php 

header("Refresh:60");

include "conexion.php";

$user="dorian";

$pasw="123";

include "conexion.php";

$consulta="SELECT hash FROM masgps.hash where user='$user' and pasw='$pasw'";

$resutaldo= mysqli_query($conex,$consulta);

$data=mysqli_fetch_array($resutaldo);
 
$hashed =$data['hash'];


//$hashed="68d5c08e6e4d5b6c33ce47cc488a62e7";

date_default_timezone_set('America/Santiago');
$hoy = date("Y-m-d");
$hoylog = date("Y-m-d  H:i:s");

include "ikcount.php";


include "lista_tracker.php";

if ( $array2->success==false){


    include "hash.php";

    echo

    $consulta="UPDATE `masgps`.`hash` SET `hash` = '$hashed' WHERE (`id` = '2');";

    $resutaldo= mysqli_query($conex,$consulta);



} else {

    echo "Token ok";
    echo "<br>";
}

$j=0;

foreach ($ids as $id) {

    echo
    $patente2 = $id->label;

    
    $id_tracker = $id->id;



    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://www.trackermasgps.com/api-v2/tracker/get_state',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{"hash": "'.$hashed.'", "tracker_id":'.$id_tracker.' }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));

    $estados = curl_exec($curl);

    curl_close($curl);
    
    $array3=json_decode($estados);
    
    $lat= $array3->state->gps->location->lat;
    $lng= $array3->state->gps->location->lng;
    $fecha= $array3->state->gps->updated;
    $status=$array3->state->movement_status;



    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://www.trackermasgps.com/api-v2/geocoder/search_location',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{"location":{"lat":'.$lat.',"lng":'.$lng.'},"lang":"es","hash":"'.$hashed.'"}',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json, text/plain, */*',
            'Accept-Language: es-419,es;q=0.9,en;q=0.8',
            'Connection: keep-alive',
            'Content-Type: application/json',
            'Cookie: _ga=GA1.2.728367267.1665672802; _gid=GA1.2.343013326.1670594107; locale=es; session_key=7ceae24d013dd694bdbaa06dd8bac781; check_audit=7ceae24d013dd694bdbaa06dd8bac781',
            'Origin: http://www.trackermasgps.com',
            'Referer: http://www.trackermasgps.com/',
            'User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Mobile Safari/537.36'
        ),
    ));

    $direccion = curl_exec($curl);

    curl_close($curl);

    $array4=json_decode($direccion);
    
    $direccion=$array4->value ;


    $fila = array_values(

        array_filter(

            $contadores,
            function ($ite) use ($patente2) {

                return $ite['patente'] == $patente2;
            }
        )
    );

    // $row=json_encode($fila);

    

    if ($fila){
    $entrada = $fila[0]['entrada'];
    
    $salida = $fila[0]['salida'];
    
    } else {
        $entrada = 0;
    
        $salida = 0;
         
    }

     "  id_tracker: $id_tracker Fecha : $fecha, Estatus: $status, Entradas: $entrada, Salidas : $salida, Consulta: $hoylog";
 
   



    $aforo = $entrada - $salida;
    if ($aforo < 0) {
        $aforo = 0;
    
    }

    $consulta="SELECT MAX(entrada) as maximo FROM contador  WHERE patente= '$patente2' AND fechaConsulta='$hoy'" ;

    $consultasalida="SELECT MAX(salida) as maximoS FROM contador  WHERE patente= '$patente2' AND fechaConsulta='$hoy'" ;

    include 'Maximo.php';
    include 'maximosalidas.php';

    $entraAcumula = $entrada - $maxi;
    $salidaAcumula = $salida - $maxisalidas;

    
    $query = "INSERT INTO contador (trakerId,latitud,longitud,direccion,fecha,patente,entrada,entradaT,salida,salidaT,aforo,fechaConsulta,fechafiltro) 
    VALUES ('$id_tracker','$lat','$lng','$direccion','$fecha','$patente2','$entrada','$entraAcumula','$salida','$salidaAcumula','$aforo','$hoy',' $hoylog')";

    echo "<br>";


    if ($maxi != $entrada || $maxisalidas != $salida) {

        //$ejecutar = mysqli_query($conex, $query);

        echo $query;
    } else {

        //no hace nada
    }


}
?>