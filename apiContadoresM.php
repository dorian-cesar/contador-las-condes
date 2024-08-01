<?php 

//header("Refresh:60");

include "conexion.php";

$user = "lasCondes";
$pasw = "123";

$consulta = "SELECT hash FROM masgps.hash WHERE user='$user' AND pasw='$pasw'";
$resultado = mysqli_query($conex, $consulta);
$data = mysqli_fetch_array($resultado);
$hashed = $data['hash'];

//$hashed="68d5c08e6e4d5b6c33ce47cc488a62e7";

date_default_timezone_set('America/Santiago');
$hoy = date("Y-m-d");
$hoylog = date("Y-m-d H:i:s");

include "ikcount.php";
include "lista_tracker.php";

$mh = curl_multi_init();
$curl_array = array();
$tracker_data = array();

foreach ($ids as $id) {
    $id_tracker = $id->id;
    $patente2 = $id->label;

    $curl_array[$id_tracker] = curl_init();
    curl_setopt_array($curl_array[$id_tracker], array(
        CURLOPT_URL => 'http://www.trackermasgps.com/api-v2/tracker/get_state',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{"hash": "'.$hashed.'", "tracker_id":'.$id_tracker.'}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    curl_multi_add_handle($mh, $curl_array[$id_tracker]);

    $tracker_data[$id_tracker] = [
        'patente2' => $patente2
    ];
}

$running = null;
do {
    curl_multi_exec($mh, $running);
    curl_multi_select($mh);
} while ($running > 0);

$total = array();

foreach ($ids as $id) {
    $id_tracker = $id->id;

    $estados = curl_multi_getcontent($curl_array[$id_tracker]);
    curl_multi_remove_handle($mh, $curl_array[$id_tracker]);
    curl_close($curl_array[$id_tracker]);

    $array3 = json_decode($estados);
    
    $lat = $array3->state->gps->location->lat ?? null;
    $lng = $array3->state->gps->location->lng ?? null;
    $fecha = $array3->state->gps->updated ?? null;
    $status = $array3->state->movement_status ?? null;
    $speed = $array3->state->gps->speed ?? null;
    $direccion = $array3->state->gps->heading ?? null;

    $patente2 = $tracker_data[$id_tracker]['patente2'];

    $fila = array_values(
        array_filter(
            $contadores,
            function ($ite) use ($patente2) {
                return $ite['patente'] == $patente2;
            }
        )
    );

    if ($fila) {
        $entrada = $fila[0]['entrada'];
        $salida = $fila[0]['salida'];
    } else {
        $entrada = 0;
        $salida = 0;
    }

    $json = [
        'id_tracker' => $id_tracker,
        'patente' => $patente2,
        'latitud' => $lat,
        'longitud' => $lng,
        'speed' => $speed,
        'direction' => $direccion,
        'Timestamp' => $fecha,
        'estatus' => $status,
        'entradas' => $entrada,
        'salidas' => $salida
    ];

    $total[] = $json;
}

curl_multi_close($mh);

echo json_encode($total);

?>
