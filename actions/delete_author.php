<?php
require_once "../config/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $authorId = intval($_POST["id"]);
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

    if ($http_code !== 200) {
        echo "Error fetching author details. Cannot delete.";
        exit();
    }

    $author = json_decode($response, true);
    
    if (isset($author["books"]) && count($author["books"]) > 0) {
        echo "This author has books and cannot be deleted.";
        exit();
    }

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $_SESSION["access_token"],
        "Accept: application/json"
    ]);

    $deleteResponse = curl_exec($ch);
    curl_close($ch);

    echo "Author deleted successfully.";
}
?>
