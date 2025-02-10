<?php
require_once "../config/config.php";

header("Content-Type: application/json");

if (!isset($_SESSION["access_token"])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    echo json_encode(["error" => "Missing author ID"]);
    exit();
}

$authorId = intval($_GET["id"]);
$api_url = API_AUTHORS_URL_1 . "/" . $authorId;

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $_SESSION["access_token"],
    "Accept: application/json"
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 403) {
    echo json_encode(["error" => "Unauthorized access. Check API token."]);
    exit();
}

$author = json_decode($response, true);
if (!$author || isset($author["error"])) {
    echo json_encode(["error" => "Invalid response from API", "raw_response" => $response]);
    exit();
}
echo json_encode($author);
exit();
?>
