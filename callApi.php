<?php

// Configurar las credenciales de Basic Auth
$authUser = 'LasCondes';
$authPassword = 'Wit2024';
$authHeader = base64_encode("$authUser:$authPassword");

// Iniciar la sesión cURL
$curl = curl_init();

// Configurar las opciones de cURL
curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://localhost/ContadorLascondes/apiContadoresCamaraM.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => array(
        "Authorization: Basic $authHeader"
    ),
));

// Ejecutar la solicitud cURL
$response = curl_exec($curl);

// Cerrar la sesión cURL
curl_close($curl);

// Mostrar la respuesta
echo $response;
?>






