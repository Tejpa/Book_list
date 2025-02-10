<?php
session_start();
if (!isset($_SESSION["access_token"]) || !isset($_GET["id"])) {
    header("Location: dashboard.php");
    exit();
}

$authorId = intval($_GET["id"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Author Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container">
        <h2>Author Details</h2>
        <div id="authorDetails" class="mb-4"></div>

        <h3>Books</h3>
        <table id="booksTable" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Release Date</th>
                    <th>ISBN</th>
                    <th>Format</th>
                    <th>Pages</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <button id="deleteAuthor" class="btn btn-danger mt-3">Delete Author</button>
        <a href="dashboard.php" class="btn btn-secondary mt-3">Back</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let authorId = <?= $authorId ?>;
        let books = [];

        $(document).ready(function() {
            $.get("../actions/fetch_author.php?id=" + authorId, function(response) {
                if (response.error) {
                    alert("Error: " + response.error);
                    return;
                }

                $("#authorDetails").html(`
                    <p><strong>ID:</strong> ${response.id}</p>
                    <p><strong>Name:</strong> ${response.first_name} ${response.last_name}</p>
                    <p><strong>Birthday:</strong> ${new Date(response.birthday).toLocaleDateString()}</p>
                    <p><strong>Gender:</strong> ${response.gender}</p>
                    <p><strong>Place of Birth:</strong> ${response.place_of_birth}</p>
                    
                `);

                books = response.books;
                books.forEach(book => {
                    $("#booksTable tbody").append(`
                        <tr>
                            <td>${book.id}</td>
                            <td>${book.title}</td>
                            <td>${new Date(book.release_date).toLocaleDateString()}</td>
                            <td>${book.isbn}</td>
                            <td>${book.format}</td>
                            <td>${book.number_of_pages}</td>
                            <td>
                                <button class="btn btn-danger btn-sm delete-book" data-id="${book.id}">Delete</button>
                            </td>
                        </tr>
                    `);
                });

                if (books.length === 0) {
                    $("#deleteAuthor").show();
                } else {
                    $("#deleteAuthor").hide();
                }
            });

            $(document).on("click", ".delete-book", function() {
                let bookId = $(this).data("id");
                $.post("../actions/delete_book.php", { id: bookId }, function(response) {
                    alert(response);
                    location.reload();
                });
            });

            $("#deleteAuthor").click(function() {
                if (confirm("Are you sure you want to delete this author?")) {
                    $.post("../actions/delete_author.php", { id: authorId }, function(response) {
                        alert(response);
                        window.location.href = "dashboard.php";
                    });
                }
            });
        });
    </script>
</body>
</html>
