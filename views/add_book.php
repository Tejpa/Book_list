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
    <title>Add New Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">

    <div class="container">
        <h2 class="text-center mb-4">Add a New Book</h2>
        <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

        <form id="addBookForm">
            <div class="mb-3">
                <label for="author" class="form-label">Select Author</label>
                <select id="author" name="author_id" class="form-control" required>
                    <option value="">Loading authors...</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">Book Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>

            <div class="mb-3">
                <label for="release_date" class="form-label">Release Date</label>
                <input type="date" class="form-control" id="release_date" name="release_date" required>
            </div>

            <div class="mb-3">
                <label for="isbn" class="form-label">ISBN</label>
                <input type="text" class="form-control" id="isbn" name="isbn" required>
            </div>

            <div class="mb-3">
                <label for="format" class="form-label">Format</label>
                <input type="text" class="form-control" id="format" name="format" required>
            </div>

            <div class="mb-3">
                <label for="pages" class="form-label">Number of Pages</label>
                <input type="number" class="form-control" id="pages" name="number_of_pages" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Add Book</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Load authors into dropdown
            $.get("../actions/fetch_authors.php", function(response) {
                console.log(response.data);
                
                let authors = response.data;
              
                $("#author").html('<option value="" disabled selected>Select an author</option>');

                authors.forEach(author => {
                    $("#author").append(`<option value="${author.id}">${author.first_name} ${author.last_name}</option>`);
                });
            });

           
           
       

          
        $("#addBookForm").submit(function(event) {
            event.preventDefault();

            let formData = {
                author: { id: parseInt($("#author").val(), 10) },
                title: $("#title").val(),
                release_date: $("#release_date").val(),
                isbn: $("#isbn").val(),
                format: $("#format").val(),
                number_of_pages: parseInt($("#pages").val(), 10),
                description: $("#description").val()
            };

            console.log("Sent Data:", formData);

            $.ajax({
                url: "../actions/submit_book.php",
                type: "POST",
                data: JSON.stringify(formData),
                contentType: "application/json",
                dataType: "json",
                success: function(response) {
                    console.log("Server Response:", response);

                    if (response.error) {
                        alert("Error: " + response.error);
                    } else {
                        alert("Success: " + response.success);
                        window.location.href = "dashboard.php";
                    }
                },
                error: function(xhr) {
                    console.log("AJAX Error:", xhr.responseText);
                    alert("An error occurred: " + xhr.responseText);
                }
            });
        });

});
    </script>
</body>
</html>
