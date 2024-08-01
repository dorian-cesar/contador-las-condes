<?php

//header("Refresh:60");

include "conexion.php";

$user = "Camara";

$pasw = "123";

include "conexion.php";

$consulta = "SELECT hash FROM masgps.hash where user='$user' and pasw='$pasw'";

$resutaldo = mysqli_query($conex, $consulta);

$data = mysqli_fetch_array($resutaldo);

$hashed = $data['hash'];


//$hashed="68d5c08e6e4d5b6c33ce47cc488a62e7";

date_default_timezone_set('America/Santiago');
$hoy = date("Y-m-d");
$hoylog = date("Y-m-d  H:i:s");

include "ikcount.php";


include "lista_tracker.php";



$i = 0;

foreach ($ids as $id) {



    $patente2 = $id->label;


    $id_tracker = $id->id;

    if ($id_tracker > 10197060) {




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
            CURLOPT_POSTFIELDS => '{"hash": "' . $hashed . '", "tracker_id":' . $id_tracker . ' }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $estados = curl_exec($curl);

        curl_close($curl);

        $array3 = json_decode($estados);

        $lat = $array3->state->gps->location->lat;
        $lng = $array3->state->gps->location->lng;
        $fecha = $array3->state->gps->updated;
        $status = $array3->state->movement_status;

        //$plate = $array3->label;

        $speed = $array3->state->gps->speed;

        $direccion = $array3->state->gps->heading;




     

        $fila = array_values(

            array_filter(

                $contadores,
                function ($ite) use ($patente2) {

                    return $ite['patente'] == substr($patente2, 0, 7);;
                }
            )
        );

       // echo  $row=json_encode($fila);



        if ($fila) {
            $entrada = $fila[0]['entrada'];

            $salida = $fila[0]['salida'];
        } else {
            $entrada = 0;

            $salida = 0;
        }

        //echo "  id_tracker: $id_tracker Fecha : $fecha, Estatus: $status, Entradas: $entrada, Salidas : $salida, Consulta: $hoylog";

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
           // 'front' => $front_camera,
           //  'inside' => $inside_camera


        ];

        $total[$i] = $json;

        $i++;
    }
}

echo json_encode($total);
