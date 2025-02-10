<?php
require_once "../config/config.php";

header("Content-Type: application/json");

if (!isset($_SESSION["access_token"])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['length']) ? intval($_GET['length']) : 12; 

/* Build API request URL dynamically */
$api_url = API_AUTHORS_URL . $page;

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $_SESSION["access_token"],
    "Accept: application/json"
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(["error" => "API Error", "status" => $http_code, "response" => $response]);
    exit();
}

$authors = json_decode($response, true);

if (!isset($authors["items"])) {
    echo json_encode(["error" => "Invalid response format", "raw_response" => $response]);
    exit();
}

echo json_encode([
    "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
    "recordsTotal" => $authors["total_results"],
    "recordsFiltered" => count($authors["items"]),
    "data" => $authors["items"]
]);
?>
