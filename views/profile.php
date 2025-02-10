<?php
require_once "../config/config.php";

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["access_token"])) {
    die("Error: User is not authenticated. Please log in.");
}

$user_id = $_SESSION["user_id"];
$api_url = API_BASE_URL . "users/" . $user_id;
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: application/json",
    "Cache-Control: no-cache, private",
    "Content-Type: application/json",
    "Authorization: Bearer " . $_SESSION["access_token"]
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

$user_data = json_decode($response, true);

if ($http_code !== 200 || !$user_data || isset($user_data["error"])) {
    echo "<div class='alert alert-danger'>Failed to load user data.</div>";
    echo "<pre>API Response Debugging:\n";
    print_r($response);
    echo "</pre>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">User Profile</h4>
                </div>
                <div class="card-body">
                    <?php if ($user_data): ?>
                        <p><strong>Name:</strong> <?= htmlspecialchars($user_data["first_name"] . " " . $user_data["last_name"]) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user_data["email"]) ?></p>
                        <p><strong>Gender:</strong> <?= ucfirst(htmlspecialchars($user_data["gender"])) ?></p>
                        <p><strong>Status:</strong> 
                            <span class="badge <?= $user_data["active"] ? 'bg-success' : 'bg-danger' ?>">
                                <?= $user_data["active"] ? "Active" : "Inactive" ?>
                            </span>
                        </p>
                        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
                    <?php else: ?>
                        <div class="alert alert-danger">Failed to load user data.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
