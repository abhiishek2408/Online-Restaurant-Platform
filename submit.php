<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJAX Form Submission Example</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery -->
    <style>
        /* Simple styling for better visuals */
        body {
            font-family: Arial, sans-serif;
        }
        #contact-form {
            display: none; /* Initially hide the form */
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        button {
            margin-top: 10px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Contact Form</h1>
    
    <div id="contact-form">
        <form id="myForm" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <br>
            <input type="submit" value="Submit">
        </form>
        <div id="message"></div> <!-- Div to display success or error messages -->
    </div>

    <button id="toggle-form">Show/Hide Form</button>

    <script>
        $(document).ready(function() {
            // Toggle the form visibility when the button is clicked
            $("#toggle-form").click(function() {
                $("#contact-form").toggle(); // Toggle visibility of the form
            });

            // Handle form submission with AJAX
            $("#myForm").submit(function(event) {
                event.preventDefault(); // Prevent the default form submission
                
                $.ajax({
                    type: "POST",
                    url: "", // Send the request to the same page
                    data: $(this).serialize(), // Serialize the form data
                    success: function(response) {
                        // Display success or error messages
                        $("#message").html(response);
                        $("#myForm")[0].reset(); // Reset the form
                    },
                    error: function() {
                        $("#message").html("<p class='error'>An error occurred. Please try again.</p>");
                    }
                });
            });
        });
    </script>

    <?php
    // Initialize variables
    $name = "";
    $email = "";
    $errors = [];

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Collect and sanitize input data
        $name = htmlspecialchars(trim($_POST['name']));
        $email = htmlspecialchars(trim($_POST['email']));

        // Validate input data
        if (empty($name)) {
            $errors[] = "Name is required.";
        }

        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        // If no errors, process the data
        if (empty($errors)) {
            // Here you could add code to save to a database or send an email
            echo "<p class='success'>Submission Successful! Name: $name, Email: $email</p>";
            exit; // Stop further processing
        } else {
            // Display errors
            foreach ($errors as $error) {
                echo "<p class='error'>$error</p>";
            }
            exit; // Stop further processing
        }
    }
    ?>
</body>
</html>
