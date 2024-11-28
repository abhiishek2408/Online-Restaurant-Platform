<?php
session_start();
include('config/db.php'); // Include database configuration file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header('Location: login.php');
    exit();
}

// Get logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

// Fetch user data from the database
$sql = "SELECT * FROM users WHERE user_id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch user data
    $user = $result->fetch_assoc(); 
} else {
    echo "User not found.";
    exit;
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            display: flex;
            height: 100vh;
            background: linear-gradient(135deg, #f9f9f9, #eaeaea);
            color: #333;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background: linear-gradient(145deg, #ffffff, #e6e6e6);
            box-shadow: 7px 7px 15px rgba(0, 0, 0, 0.1), -7px -7px 15px rgba(255, 255, 255, 0.7);
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .sidebar h1 {
            font-size: 24px;
            color: #FF7518;
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
            position: relative;
        }

        .sidebar ul li a {
            text-decoration: none;
            font-weight: 500;
            color: #555;
            padding: 10px 15px;
            display: block;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.1), -3px -3px 8px rgba(255, 255, 255, 0.7);
        }

        .sidebar ul li a:hover {
            color: white;
            background-color: #FF7518;
            box-shadow: 0px 4px 10px rgba(255, 117, 24, 0.3);
        }

        /* User Profile in Sidebar */
        .user-profile {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.1), -3px -3px 8px rgba(255, 255, 255, 0.7);
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .user-profile h3 {
            font-size: 16px;
            margin: 0;
            color: #333;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            left: 10px;
            top: 80px;
            background: #ffffff;
            box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.1), -3px -3px 8px rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            padding: 10px 0;
            z-index: 10;
        }

        .dropdown-menu li {
            list-style: none;
        }

        .dropdown-menu a {
            text-decoration: none;
            color: #555;
            padding: 10px 20px;
            display: block;
            transition: background 0.3s ease;
        }

        .dropdown-menu a:hover {
            background-color: #FF7518;
            color: white;
        }

        /* Main Content Styling */
        .main-content {
            flex: 1;
            padding: 30px;
            display: flex;
            flex-direction: column;
        }


          /* Circular button container */
          .back-button-container {
            position: fixed;
            bottom: 20px;
            left: 20px;
            width: 60px;
            height: 60px;
            background-color: white;
            border-radius: 50%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            z-index: 1000; /* Ensure it appears above all content */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .back-button-container:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
        }

        /* Arrow Icon */
        .back-button-container svg {
            width: 30px;
            height: 30px;
            fill: #333; /* Icon color */
        }

        /* For demonstration purposes: long content */
        .content {
            height: 2000px;
            padding: 20px;
            background: linear-gradient(to bottom, #f9f9f9, #eaeaea);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h1>Dashboard</h1>
        <ul>
            <li><a href="orders.php" target="content-frame">Orders</a></li>
            <li><a href="table.php" target="content-frame">Table</a></li>
            <li><a href="events.php" target="content-frame">Events</a></li>
            <li><a href="settings.php" target="content-frame">Settings</a></li>
            <li>
                <div class="user-profile" onclick="toggleDropdown()">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($user['profile_img']); ?>" alt="Profile">
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                </div>
                <ul class="dropdown-menu">
                    <li><a href="profile.php" target="content-frame">Open Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <iframe src="myprofile.php" name="content-frame" style="width: 100%; height: 100%; border: none;"></iframe>
    </div>

    <div class="back-button-container" onclick="goBack()">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path d="M15.41 16.58 10.83 12l4.58-4.59L14 6l-6 6 6 6z"/>
    </svg>
</div>


    <script>
        function toggleDropdown() {
            const menu = document.querySelector('.dropdown-menu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }

        // Close dropdown if clicked outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.user-profile')) {
                document.querySelector('.dropdown-menu').style.display = 'none';
            }
        });

        function goBack() {
        window.history.back();
    }
    </script>
</body>
</html>
