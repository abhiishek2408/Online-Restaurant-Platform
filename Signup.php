<?php
session_start();
include('config/db.php');

// Initialize error variables for each field
$username_error = $email_error = $image_error = $db_error = '';

if (isset($_POST['register'])) {
    // Collect form data
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $role = 'customer';
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $bio = $_POST['bio'];
    $created_at = date('Y-m-d H:i:s');
    $otp = rand(100000, 999999);
    $imageData = null;

    // Profile image validation
    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['profile_img']['type'];

        if (in_array($fileType, $allowedTypes)) {
            $imageData = file_get_contents($_FILES['profile_img']['tmp_name']);
        } else {
            $image_error = "Invalid file type. Please upload a JPEG, PNG, or GIF image.";
        }
    } else {
        $image_error = "No image uploaded or there was an upload error.";
    }


    // Enable MySQLi error reporting
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    if (isset($_POST['register'])) {
        // Collect form data as before
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $email = $_POST['email'];
        $role = 'customer';
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $bio = $_POST['bio'];
        $created_at = date('Y-m-d H:i:s');

        $imageData = null;

        // Profile image validation as before
        if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $_FILES['profile_img']['type'];

            if (in_array($fileType, $allowedTypes)) {
                $imageData = file_get_contents($_FILES['profile_img']['tmp_name']);
            } else {
                $image_error = "Invalid file type. Please upload a JPEG, PNG, or GIF image.";
            }
        } else {
            $image_error = "No image uploaded or there was an upload error.";
        }

        // Check for existing username
        if (empty($username_error) && empty($image_error)) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            if ($stmt) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->bind_result($usernameCount);
                $stmt->fetch();
                $stmt->close();

                if ($usernameCount > 0) {
                    $username_error = "Username already taken. Please choose a different username.";
                }
            } else {
                $db_error = "Error preparing statement: " . $conn->error;
            }
        }

        // Check for existing email
        if (empty($email_error) && empty($username_error)) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->bind_result($emailCount);
                $stmt->fetch();
                $stmt->close();

                if ($emailCount > 0) {
                    $email_error = "Email already in use. Please use a different email.";
                }
            } else {
                $db_error = "Error preparing statement: " . $conn->error;
            }
        }

        // Insert data if no errors
        if (empty($username_error) && empty($email_error) && empty($image_error)) {
            $stmt = $conn->prepare("INSERT INTO users (username, password, email, role, phone, address, bio, profile_img, created_at) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sssssssss", $username, $password, $email, $role, $phone, $address, $bio, $imageData, $created_at);

                if ($stmt->execute()) {
                    $success_message = "Registration successful!";
                } else {
                    $db_error = "Error executing statement: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $db_error = "Error preparing insert statement: " . $conn->error;
            }
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bistrofy - Sign Up</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Georgia', serif;
        }

          
        @keyframes containerFadeIn {
            0% {
                opacity: 0;
                transform: scale(0.9);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

  
        @keyframes buttonBounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-5px);
            }

            60% {
                transform: translateY(-3px);
            }
        }
        
        .container {
            display: flex;
            height: 100vh;
            background-color: #ffe8d6;
            transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
            animation: containerFadeIn 0.8s ease-out forwards;
            
        }

        .container:hover {
    
    transform: scale(1.02); 
   
}

        /* Left Panel */
        .left-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            color: #5b3a29;
            background-color: #FF7518;
        }

        .left-panel h1 {
            font-size: 3rem;
            color: #3d3d3d;
            margin-bottom: 0.5rem;
            font-weight: bold;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }

        .left-panel p {
            font-size: 1.3rem;
            color: #3d3d3d;
            text-align: center;
            margin-bottom: 2rem;
        }

        /* Decorative Icons */
        .icons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .icon {
            font-size: 2.5rem;
            color: #ffffff;
        }

        /* Button Style */
        .btn {
            background-color: #fff;
            color: #FF7518;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-size: 1.1rem;
            margin-top: 1rem;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #000;
            color: #fff;
        }

        /* Right Panel (Sign-up Form) */
        .right-panel {
            flex: 1;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;

        }


        /* Left Form Panel */
        .form-panel {
            width: 100%;
            height: 100%;
            background-color: #2c2c2c;
            display: flex;
            color: white;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .form-content {
            max-width: 400px;
            text-align: center;
        }

        .logo {
            width: 100px;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 2rem;
            color: white;
        }

        p {
            color: white;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }

        .input-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .input-group label {
            margin-bottom: 1px;
            color: white;
            font-weight: bold;
            text-align: left;
        }

        .input-group input,
        .input-group textarea {
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .input-group input:focus,
        .input-group textarea:focus {
            border-color: #6a0dad;
            outline: none;
        }

        .input-half {
            width: 48%;
            /* Use 48% to leave space for margin */
        }

        .continue-btn {
            width: 50%;
            padding: 10px;
            background-color: #FF7518;
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 5px;
        }

        .continue-btn:hover {
            background-color: #fff;
            color: #FF7518;
            border: 2px solid #FF7518;
        }

        .signin-link {
            color: white;
            font-size: 0.9rem;
            margin-top: 20px;
        }

        .signin-link a {
            color: #6a0dad;
            text-decoration: none;
        }

        .signin-link a:hover {
            text-decoration: underline;
        }

        textarea {
            resize: none;
            height: 40px;
        }




        /* Footer Text */
        .footer-text {
            text-align: center;
            font-size: 0.9rem;
            color: #555;
            margin-top: 1rem;
        }

        .footer-text a {
            color: #333;
            text-decoration: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .left-panel h1 {
                font-size: 2.5rem;
            }

            .left-panel p {
                font-size: 1.1rem;
            }
        }

        .success-message {
            color: green;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        .error {
            color: red;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="left-panel">
            <h1>Welcome to Bistrofy</h1>
            <p>Your gateway to delicious culinary experiences. Fresh ingredients, artisan recipes, and a passion for flavor come together here.</p>


            <div class="icons">
                <div class="icon">🍲</div>
                <div class="icon">🍕</div>
                <div class="icon">🍹</div>
                <div class="icon">🍜</div>
            </div>


            <a href="#menu" class="btn">Explore Our Menu</a>
        </div>


        <div class="right-panel">

            <div class="form-panel">
                <div class="form-content">
                    <h2>Sign up</h2>
                    <p>Create an account to start posting jobs and build your remote team with us</p>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="input-row">
                            <div class="input-group input-half">
                                <label for="username">Username:</label>
                                <input type="text" name="username" required>
                                <?php if (!empty($username_error)) echo "<div class='error'>$username_error</div>"; ?>
                            </div>
                            <div class="input-group input-half">
                                <label for="email">Email:</label>
                                <input type="email" name="email" required>
                                <?php if (!empty($email_error)) echo "<div class='error'>$email_error</div>"; ?>
                            </div>
                        </div>

                        <div class="input-row">
                            <div class="input-group input-half">
                                <label for="password">Password:</label>
                                <input type="password" name="password" required>
                            </div>
                            <div class="input-group input-half">
                                <label for="confirm_password">Confirm Password:</label>
                                <input type="password" name="confirm_password" required>
                                <div id="password_error" class="error"></div>
                            </div>
                        </div>

                        <div class="input-row">
                            <div class="input-group input-half">
                                <label for="phone">Phone:</label>
                                <input type="text" name="phone">
                            </div>
                            <div class="input-group input-half">
                                <label for="address">Address:</label>
                                <input type="text" name="address">
                            </div>
                        </div>

                        <div class="input-row">
                            <div class="input-group input-half">
                                <label for="bio">Bio:</label>
                                <textarea name="bio"></textarea>
                            </div>
                            <div class="input-group input-half">
                                <label for="profile_img">Profile Image:</label>
                                <input type="file" name="profile_img" accept="image/*">
                                <?php if (!empty($image_error)) echo "<div class='error'>$image_error</div>"; ?>
                            </div>
                        </div>

                        <button type="submit" name="register" class="continue-btn">Register</button>
                        <?php if (!empty($db_error)) echo "<div class='error'>$db_error</div>"; ?>
                        <?php
                        if (!empty($success_message)) echo "<div class='success-message'>$success_message</div>";
                        if (!empty($db_error)) echo "<div class='error'>$db_error</div>";
                        ?>
                    </form>
                    <p class="signin-link">Already have an account? <a href="login.php">Sign in</a></p>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
            const passwordErrorDiv = document.getElementById('password_error');

            if (password !== confirmPassword) {
                passwordErrorDiv.textContent = "Passwords do not match!";
                event.preventDefault();
            } else {
                passwordErrorDiv.textContent = "";
            }
        });
    </script>
</body>

</html>