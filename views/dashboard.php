<?php
session_start();
if (!isset($_SESSION["access_token"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Authors List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">

    <div class="container">
        <h2 class="text-center mb-4">Authors List</h2>
        <p>Welcome, <strong><?= htmlspecialchars($_SESSION["first_name"] . " " . $_SESSION["last_name"]) ?></strong></p>

        <a href="../actions/logout.php" class="btn btn-danger mb-3">Logout</a>
        <a href="add_book.php" class="btn btn-success mb-3">Add New Book</a>
        <a href="profile.php" class="btn btn-primary mb-3">View Profile</a>

        <table id="authorsTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Birthday</th>
                    <th>Gender</th>
                    <th>Place of Birth</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
     $(document).ready(function() {
        $('#authorsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "../actions/fetch_authors.php",
                "type": "GET",
                "dataSrc": function(json) {
                    console.log("API Response:", json);
                    if (json.error) {
                        alert("Error: " + json.error);
                        return [];
                    }
                    return json.data || [];
                },
                "error": function(xhr, status, error) {
                    console.log("AJAX Error:", xhr.responseText);
                    alert("Failed to load data. Check console.");
                }
            },
            "columns": [
                { "data": "id", "defaultContent": "-" },
                { "data": "first_name", "defaultContent": "-" },
                { "data": "last_name", "defaultContent": "-" },
                { 
                    "data": "birthday", 
                    "defaultContent": "-",
                    "render": function(data, type, row) {
                        if (!data) return "-";
                        
                        let date = new Date(data);
                        let options = { day: '2-digit', month: 'long', year: 'numeric' };
                        return date.toLocaleDateString('en-US', options);
                    }
                },
                { "data": "gender", "defaultContent": "-" },
                { "data": "place_of_birth", "defaultContent": "-" },
                { 
                    "data": null,
                    "render": function(data, type, row) {
                        return `
                            <a href="author.php?id=${row.id}" class="btn btn-info btn-sm">View</a>
                            <button class="btn btn-danger btn-sm delete-author" data-id="${row.id}">Delete</button>
                        `;
                    }
                }
            ]
        });

        // Handle Author Deletion
        $(document).on("click", ".delete-author", function() {
            let authorId = $(this).data("id");

            if (confirm("Are you sure you want to delete this author? This is only possible if they have no books.")) {
                $.post("../actions/delete_author.php", { id: authorId }, function(response) {
                    alert(response);
                    location.reload();
                });
            }
        });
    });
    </script>
</body>
</html>
