<?php
require_once "../config/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $data = json_encode([
        "email" => $email,
        "password" => $password
    ]);

    $ch = curl_init(API_LOGIN_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept: application/json",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200 && $result) {
        $response = json_decode($result, true);

        if (isset($response["token_key"])) {
            session_start();
            $_SESSION["user_id"] = $response['user']["id"];
            $_SESSION["first_name"] = $response['user']["first_name"];
            $_SESSION["last_name"] = $response['user']["last_name"];
            $_SESSION["access_token"] = $response["token_key"];
            header("Location: ../views/dashboard.php");
            exit();
        } else {
            header("Location: ../views/login.php?error=Invalid response from server.");
            exit();
        }
    } else {
        header("Location: ../views/login.php?error=Login failed. Try again.");
        exit();
    }
}
?>
