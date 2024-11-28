<?php
session_start();
include('../config/db.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Management System</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <style>
     
body {
    margin: 0;
    font-family: 'Roboto', sans-serif;
    background-color: #f4f4f9;
}

/* Sidebar Container */
.sidebar {
    position: fixed;
    height: 100%;
    width: 250px;
    background-color: #2c2c22;
    color: #333;
    display: flex;
    flex-direction: column;
    padding-top: 20px;
    box-shadow: 8px 8px 15px rgba(200, 200, 200, 0.2) /* Neumorphism effect */
}

/* Sidebar Title - Cursive Font with Tomato color */
/* Sidebar Title - Cursive Font with Tomato and Golden Yellow Gradient */
.sidebar-title {
    font-size: 2rem;
    text-align: center;
    margin-bottom: 20px;
    font-family: 'Pacifico', cursive; /* Cursive font for the title */
    background-image: linear-gradient(45deg, #ff7518, #ffcc00); /* Tomato and Golden Yellow gradient */
    -webkit-background-clip: text;
    color: transparent;
    text-shadow: 2px 2px 4px rgba(0, 0, 0.2, 0.2); /* Soft shadow for title */
}

/* Sidebar Menu */
.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-menu li {
    margin: 10px 0;
}

.sidebar-menu a {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #ffffff;
    padding: 10px 20px;
    border-radius: 12px;
    transition: all 0.3s ease;
    font-size: 1rem;
    /* Neumorphism for the menu items */
}

.sidebar-menu a:hover {
    background-color: #2c2c22;
    transform: translateX(5px);
}

.sidebar-menu i {
    margin-right: 10px;
    font-size: 1.2rem;
}

/* Dropdown Menu */
.dropdown-menu {
    display: none;
    list-style: none;
    padding-left: 20px;
}

.dropdown-toggle:hover + .dropdown-menu,
.dropdown-menu:hover {
    display: block;
}

.dropdown-menu li {
    margin: 5px 0;
}

.dropdown-menu a {
    font-size: 0.9rem;
}

/* Content Area */
#main-content {
    margin-left: 260px;
    padding: 20px;
    background: #fff;
    min-height: 100vh;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

/* Loading Spinner (Dots Animation) */
#loading {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.7);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.dot-loader {
    display: flex;
    justify-content: center;
    align-items: center;
}

.dot-loader div {
    width: 15px;
    height: 15px;
    margin: 5px;
    border-radius: 50%;
    background-color: #3498db;
    animation: bounce 1.5s infinite ease-in-out;
}

.dot-loader div:nth-child(1) {
    animation-delay: 0s;
}

.dot-loader div:nth-child(2) {
    animation-delay: 0.3s;
}

.dot-loader div:nth-child(3) {
    animation-delay: 0.6s;
}

@keyframes bounce {
    0%, 100% {
        transform: scale(0.6);
    }
    50% {
        transform: scale(1.2);
    }
}

/* Responsive Adjustments */
@media screen and (max-width: 768px) {
    .sidebar {
        width: 200px;
    }

    .sidebar-menu a {
        font-size: 0.9rem;
    }

    .sidebar-title {
        font-size: 1.2rem;
    }
}

    </style>
</head>

<body>
  
    <div class="sidebar">
        <h1 class="sidebar-title">Bistrofy Admin</h1>
        <ul class="sidebar-menu">
            <li><a href="#" data-url="adminhome.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#" data-url="user.php"><i class="fas fa-users"></i> User Management</a></li>
            <li><a href="#" data-url="ManageMenu.php"><i class="fas fa-utensils"></i> Menu Management</a></li>
            <li><a href="#" data-url="manage-orders.php"><i class="fas fa-receipt"></i> Order Management</a></li>
            <li>
                <a href="#" class="dropdown-toggle"><i class="fas fa-user"></i> Account</a>
                <ul class="dropdown-menu">
                    <li><a href="#" data-url="login.php"><i class="fas fa-sign-in-alt"></i> Sign In</a></li>
                    <li><a href="#" data-url="../Signup.php"><i class="fas fa-user-plus"></i> Sign Up</a></li>
                    <?php if (isset($_SESSION['user'])): ?>
                        <li><a href="#" data-url="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    </div>

    
    <div id="main-content">
    
    </div>


    <div id="loading">
        <div class="dot-loader">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

    <?php include('../backtoprev.php')  ?>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Load the homepage by default
            loadPage('adminhome.php');

            // Handle sidebar link clicks
            $('.sidebar-menu a, .dropdown-menu a').on('click', function (e) {
                e.preventDefault(); // Prevent default link behavior
                const url = $(this).data('url');
                loadPage(url);
            });

            function loadPage(url) {
                $('#loading').show(); // Show loading spinner
                $('#main-content').load(url, function (response, status, xhr) {
                    $('#loading').hide(); // Hide loading spinner
                    if (status === 'error') {
                        $('#main-content').html('<p>Error loading page. Please try again later.</p>');
                    }
                });
            }
        });
    </script>
</body>

</html>
