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
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile</title>
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
            background-color: #f4f4f4;
        }

        /* Sidebar styling */
        .sidebar {
            width: 240px;
            background-color: #ffffff;
            border-right: 1px solid #ddd;
            padding: 20px;
            position: relative;
        }

        .sidebar h1 {
            font-size: 24px;
            color: #FF7518;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        .sidebar ul li a:hover {
            color: #FF7518;
        }

        /* Main content styling */
        .main-content {
            flex: 1;
            padding: 20px;
        }

        /* Header styling */
        .header {
            background: linear-gradient(135deg, #FF7518, #f9d23a);
            color: white;
            padding: 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin-left: 20px;
            font-size: 28px;
        }

        /* Profile Container */
        .profile-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            padding: 30px;
        }

        .profile-img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            border: 3px solid #FF7518;
            object-fit: cover;
            margin: 0 auto;
            display: block;
            transition: transform 0.3s ease;
        }

        .profile-img:hover {
            transform: scale(1.05);
        }

        .profile-content {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-content h2 {
            font-size: 24px;
            color: #333;
            margin: 15px 0;
        }

        .bio {
            font-size: 15px;
            color: #777;
            margin-bottom: 15px;
        }

        /* Profile Details */
        .profile-details {
            margin-top: 20px;
            color: #555;
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        .profile-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .profile-row label {
            font-size: 16px;
            color: #333;
            width: 30%; /* Label width */
        }

        .profile-row span,
        .profile-row input {
            font-size: 15px;
            width: 70%; /* Input width */
            margin-left: 10px;
        }

        .profile-row input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 6px;
            display: none; /* Hide input fields initially */
        }

        .profile-details button {
            background-color: #FF7518;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 20px; /* Space above the button */
        }

        .profile-details button:hover {
            background-color: #e06a12;
        }

        /* Logout button styling */
        .btn-logout {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            font-size: 15px;
            color: #fff;
            background-color: #FF7518;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .btn-logout:hover {
            background-color: #e06a12;
        }
        .btn-editprofile {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            font-size: 15px;
            color: #FF7518;
            background-color: white;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .btn-editprofile:hover {
            background-color: #e06a12;
        }

    </style>
</head>
<body>

<!-- Sidebar Navigation -->
<div class="sidebar">
    
    <ul>
        <li><a href="dashboard.php">Profile Summary</a></li>
        <li><a href="projects.php">Account Information</a></li>
        <li><a href="tasks.php">Order History</a></li>
        <li><a href="reporting.php">Reservations</a></li>
        <li><a href="users.php">Loyalty Program</a></li>
        <li><a href="users.php">Payment Methods</a></li>
        <li><a href="users.php">Notifications and Alerts</a></li>
        <li><a href="settings.php">Settings</a></li>
    </ul>
</div>


<div class="main-content">
    <div class="header">
        <h2>User Profile</h2>
    </div>

    <div class="profile-container">
        
        <?php if (!empty($user['profile_img'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($user['profile_img']); ?>" class="profile-img" alt="Profile Image">
                        <?php else: ?>
                            <img src="path/to/default-image.jpg" alt="Default Image" class="profile-img"> <!-- Placeholder image -->
                        <?php endif; ?>
        <div class="profile-content">
            <h2><?php echo htmlspecialchars($user['username']); ?></h2>
            <p class="bio"><?php echo htmlspecialchars($user['bio']); ?></p>
        </div>

        <div class="profile-details">
            <div class="profile-row">
                <label for="email">Email:</label>
                <span id="email-display"><?php echo htmlspecialchars($user['email']); ?></span>
                <input type="text" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
            </div>

            <div class="profile-row">
                <label for="phone">Phone:</label>
                <span id="phone-display"><?php echo htmlspecialchars($user['phone']); ?></span>
                <input type="text" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" readonly>
            </div>

            <div class="profile-row">
                <label for="address">Address:</label>
                <span id="address-display"><?php echo htmlspecialchars($user['address']); ?></span>
                <input type="text" id="address" value="<?php echo htmlspecialchars($user['address']); ?>" readonly>
            </div>
        </div>

        <button id="edit-profile-btn" class="btn-editprofile">Edit Profile</button>

        <!-- Logout button -->
        <a href="logout.php" class="btn-logout">Logout</a>
        <a href="index.php" style="color: #333; text-decoration: none; font-size:1rm;margin-top:18px;">Back to home page!</a>
    </div>
</div>

<script>
    document.getElementById('edit-profile-btn').addEventListener('click', function() {
        var inputs = document.querySelectorAll('.profile-details input');
        var displays = document.querySelectorAll('.profile-row span');
        if (this.textContent === 'Edit Profile') {
            // Switch to edit mode
            inputs.forEach(function(input, index) {
                input.style.display = 'block'; // Show input fields
                displays[index].style.display = 'none'; // Hide text display
                input.readOnly = false; // Enable editing
            });
            this.textContent = 'Save Changes'; // Change button text
        } else {
            // Save changes
            inputs.forEach(function(input, index) {
                displays[index].textContent = input.value; // Update displayed text
                input.style.display = 'none'; // Hide input fields
                displays[index].style.display = 'block'; // Show updated text
                input.readOnly = true; // Disable editing
            });
            this.textContent = 'Edit Profile'; // Reset button text
        }
    });
</script>

</body>
</html>
