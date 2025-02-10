<?php
require_once "../config/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $bookId = intval($_POST["id"]);
    $api_url = API_BASE_URL . "books/" . $bookId;

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $_SESSION["access_token"],
        "Accept: application/json"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    echo "Book deleted successfully.";
}
?>
