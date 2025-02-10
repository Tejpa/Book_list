<?php
require_once "../config/config.php";

if (!isset($_SESSION["access_token"])) {
    header("Location: ../views/login.php");
    exit();
}

function fetchData($api_url, $token) {
    $options = [
        "http" => [
            "header" => "Authorization: Bearer " . $token . "\r\nContent-Type: application/json",
            "method" => "GET"
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($api_url, false, $context);
    return json_decode($result, true);
}

$api1_data = fetchData(API_1_URL, $_SESSION["access_token"]);
$api2_data = fetchData(API_2_URL, $_SESSION["access_token"]);
$api3_data = fetchData(API_3_URL, $_SESSION["access_token"]);
?>
