<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Configurar las credenciales de Basic Auth
$authUser = 'lascondes';
$authPassword = 'wit2024';
echo $_SERVER['PHP_AUTH_USER'];
echo $_SERVER['PHP_AUTH_PW'];

/* Verificar si la cabecera Authorization está presente
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || 
    $_SERVER['PHP_AUTH_USER'] !== $authUser || $_SERVER['PHP_AUTH_PW'] !== $authPassword) {
    header('WWW-Authenticate: Basic realm="Mi API"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'No autorizado';
    exit;
}
    */

//header("Refresh:60");

include "conexion.php";

$user = "monitoreogps_lascondes@wit.la";
$pasw = "123";

$consulta = "SELECT hash FROM masgps.hash2 WHERE user='$user' AND pasw='$pasw'";
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
$curl_array = [];
$tracker_data = [];

foreach ($ids as $id) {
    $id_tracker = $id->id;

    if ($id_tracker > 10197060) {
        $curl_create_stream = curl_init();
        curl_setopt_array($curl_create_stream, [
            CURLOPT_URL => 'http://www.trackermasgps.com/api-v2/tracker/multimedia/video/live_stream/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"tracker_id":' . $id_tracker . ',"cameras":["front_camera","inward_camera"],"hash":"' . $hashed . '"}',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json, text/plain, */*',
                'Accept-Language: es-US,es-419;q=0.9,es;q=0.8',
                'Connection: keep-alive',
                'Content-Type: application/json',
                'Origin: http://www.trackermasgps.com',
                'Referer: http://www.trackermasgps.com/',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36'
            ],
        ]);
        curl_multi_add_handle($mh, $curl_create_stream);
        $curl_array[$id_tracker]['create_stream'] = $curl_create_stream;

        $curl_get_state = curl_init();
        curl_setopt_array($curl_get_state, [
            CURLOPT_URL => 'http://www.trackermasgps.com/api-v2/tracker/get_state',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"hash": "' . $hashed . '", "tracker_id":' . $id_tracker . ' }',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
        ]);
        curl_multi_add_handle($mh, $curl_get_state);
        $curl_array[$id_tracker]['get_state'] = $curl_get_state;

        $tracker_data[$id_tracker] = [
            'patente2' => $id->label
        ];
    }
}

$running = null;
do {
    curl_multi_exec($mh, $running);
    curl_multi_select($mh);
} while ($running > 0);

$total = [];

foreach ($ids as $id) {
    $id_tracker = $id->id;

    if (isset($curl_array[$id_tracker])) {
        $create_stream_response = curl_multi_getcontent($curl_array[$id_tracker]['create_stream']);
        curl_multi_remove_handle($mh, $curl_array[$id_tracker]['create_stream']);
        curl_close($curl_array[$id_tracker]['create_stream']);

        $get_state_response = curl_multi_getcontent($curl_array[$id_tracker]['get_state']);
        curl_multi_remove_handle($mh, $curl_array[$id_tracker]['get_state']);
        curl_close($curl_array[$id_tracker]['get_state']);

        $arreglo = json_decode($create_stream_response);

        $front_camera = 'No Activo';
        $inside_camera = 'No Activo';
        if (isset($arreglo->video_streams[1]->link)) {
            $front_camera = $arreglo->video_streams[1]->link;
            $inside_camera = $arreglo->video_streams[0]->link;
        }

        $array3 = json_decode($get_state_response);

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
                    return $ite['patente'] == substr($patente2, 0, 7);
                }
            )
        );

        $entrada = $fila[0]['entrada'] ?? 0;
        $salida = $fila[0]['salida'] ?? 0;

        $json = [
            'id_tracker' => $id_tracker,
            'patente' => $patente2,
            'latitud' => $lat,
            'longitud' => $lng,
            'speed' => $speed,
            'direction' => $direccion,
            'fecha' => $fecha,
            'estatus' => $status,
            'entradas' => $entrada,
            'salidas' => $salida,
            'Camera_front' => $front_camera,
            'Camera_inside' => $inside_camera
        ];

        $total[] = $json;
    }
}

curl_multi_close($mh);

echo json_encode($total);

?>
