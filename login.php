<?php
session_start();
include('config/db.php');

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch the user record based on the username
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify the password using password_verify
        if (password_verify($password, $user['password'])) {

            $_SESSION['user'] = $user;
            $_SESSION['user_id'] = $user['user_id'];

            // Check user role and redirect accordingly
            if ($user['role'] === 'admin') {
                // Redirect to admin panel
                header('Location: admin/admin_dashboard.php');
            } else {
                // Redirect to customer dashboard
                header('Location: index.php');
            }
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that username.";
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
           
    transform: scale(1.02); /* Small scaling effect */
   
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
            transform: scale(1.05); /* Slightly grow the button on hover */
        }

        .btn:active {
    transform: scale(0.95); /* Shrink the button slightly on click */
    transition: transform 0.1s ease; /* Quick transition for the active state */
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
            width: 100%;
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
    transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease; 
}

.continue-btn:hover {
    background-color: #fff;
    color: #FF7518;
    border: 2px solid #FF7518;
    transform: scale(1.05); 
}

.continue-btn:active {
    transform: scale(0.95); 
    transition: transform 0.1s ease; 
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


            <a href="index.php" class="btn">Explore Our Menu</a>
        </div>


        <div class="right-panel">

            <div class="form-panel">
                <div class="form-content">
                    <h2>Login up</h2>
                    <p>Welcome back! Log in to access your account and continue exploring delicious culinary experiences.</p>
                    
                    <form method="POST" action="">
                    <div class="input-group input-half">
                        <label for="username">Username:</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="input-group input-half">
                        <label for="password">Password:</label>
                        <input type="password" name="password" required>
                    </div>
                        <button type="submit" name="login" class="continue-btn">Login</button>
                    </form>
                    <p class="signin-link">If you don't have any account? <a href="Signup.php">Sign up</a></p>
                </div>
            </div>

        </div>
    </div>

   
</body>

</html>