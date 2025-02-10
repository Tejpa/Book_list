<?php
require_once "../config/config.php";

header("Content-Type: application/json");

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!is_array($data)) {
    echo json_encode(["error" => "Invalid JSON input", "raw_input" => $input]);
    exit();
}

// Validate fields
if (empty($data["author"]["id"]) || empty($data["title"])) {
    echo json_encode(["error" => "Missing required fields", "received_data" => $data]);
    exit();
}

// Convert array to JSON
$json_data = json_encode($data, JSON_UNESCAPED_SLASHES);


$ch = curl_init(API_BASE_URL . "books");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $_SESSION["access_token"],
    "Accept: application/json",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo json_encode(["error" => "cURL Error: $error"]);
    exit();
}

$response_data = json_decode($response, true);
if (isset($response_data["id"])) {
    echo json_encode([
        "success" => "Book added successfully",
        "book" => $response_data
    ]);
    exit();
}

echo json_encode([
    "error" => "Failed to add book",
    "status" => $http_code,
    "api_raw_response" => $response,
    "api_response" => $response_data
]);
exit();
?>
