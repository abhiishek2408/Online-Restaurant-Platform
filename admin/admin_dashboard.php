<?php
session_start();
$servername = "localhost";
$username = "root"; // Change if you have a different MySQL username
$password = ""; // Change if you have a different MySQL password
$dbname = "restaurant_management";
$port = 3307; // Specify the port if needed (default is 3306)

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$update_success = false; // Initialize success variable to false

// Handle form submission for updating the state and time_to_reach
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $cart_id = $_POST['cart_id'];
    $new_state = $_POST['state'];
    $new_time_to_reach = $_POST['time_to_reach'];

    // Prepare the UPDATE query
    $update_query = "UPDATE manage_order SET state = ?, time_to_reach = ? WHERE cart_id = ?";

    // Prepare the statement
    if ($stmt = $conn->prepare($update_query)) {
        // Bind the parameters
        $stmt->bind_param("ssi", $new_state, $new_time_to_reach, $cart_id);

        // Execute the statement
        if ($stmt->execute()) {
            $update_success = true; // Set success flag to true
        } else {
            echo "Error updating record: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

// Query to select data from the 'manage_order' table, excluding 'completed' state
$query = "SELECT cart_id, user_id, menu_section_id, quantity, price, state, time_to_reach, added_at, updated_at, item_name, item_image, address FROM manage_order WHERE state != 'completed'";

// Execute the query
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Management System</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    <style>
        body {
            font-family: Roboto, sans-serif;
            background-color: #2c2c2c;
            color: #333;

            transition: background-color 0.3s, color 0.3s;
        }

        h1,
        h2 {
            color: #0056b3;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 30px;
            flex-wrap: wrap;
            background-color: #2C2C2C;
            border-radius: 5px;
            margin-bottom: 5%;
        }

        nav h1 {
            margin-left: 20px;
            font-size: 24px;
            color: #FF7518;
            font-family: 'Playfair Display', serif;
            font-weight: bold;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            line-height: 1.5;
            transition: color 0.3s ease;
          
        }

        .nav-links {
            display: flex;
            align-items: center;
            margin-left: 20px;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            margin-right: 20%;
            
        }

        nav ul li {
            margin-right: 15px;
            position: relative;
            /* Needed for dropdown positioning */
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 8px;
            transition: background-color 0.3s;
            cursor: pointer;
        }

        nav ul li a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }

        .dropdown {
            display: none;
            /* Initially hidden */
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 5px;
            margin-top: 5px;
            right: 0;
            /* Align dropdown to the right side of the menu */
        }

        .dropdown a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }

        .dropdown a:hover {
            background-color: #f0f0f0;
        }


        .dark-mode-toggle,
        .notification-icon,
        .cart-icon {
            cursor: pointer;
            color: #fff;
            margin-left: 15px;
            font-size: 20px;
           
        }


        .cart-icon i {
            color: white;
            /* Set the cart icon color to white */
        }

        .profile-container {
            position: relative;
            margin-right: 5%;
        }

        .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid #FF7518;
        }

        /* Hamburger Menu */
        .hamburger {
            display: none;
            /* Initially hidden */
            flex-direction: column;
            cursor: pointer;

        }

        .hamburger div {
            width: 25px;
            height: 3px;
            background-color: white;
            margin: 3px 0;
            left: 0;
            transition: 0.4s;
            /* Transition for smooth effect */
        }

        /* Dark mode styles */
        body.dark-mode {
            background-color: #333;
            color: #f0f0f0;
        }

        nav.dark-mode {
            background-color: #444;
        }



        /* Medium devices (desktops, 768px and up) */
        @media (max-width: 1023px) {

            nav h1 {
                font-size: 30px;
                /* Smaller logo text */
                text-align: left;
                margin-right: auto;
            }

            nav ul {
                flex-direction: column;
                /* Stack navigation items */
                align-items: flex-start;
                /* Align to start */
                display: none;
                /* Initially hide the menu */
                position: absolute;
                background-color: #0056b3;
                width: 100%;
                left: 0;
                top: 50px;
                /* Position below the nav bar */
                z-index: 2;
            }

            nav.active ul {
                display: flex;
                /* Show menu when active */
            }

            .search-container {
                display: none;
                /* Hide the search box on small screens */
            }

            .hamburger {
                background-color: green;
                display: flex;
                /* This shows the hamburger icon */
                justify-content: flex-start;
                /* Aligns content to the left */
                margin-right: 2%;
                align-items: left;
                /* Vertically centers the content */
            }
        }

        /* Extra large devices (extra large desktops, 1200px and up) */
        @media (min-width: 1024px) and (max-width: 1300px) {
            nav h1 {
                font-size: 10px;
                /* Slightly larger logo text */
                color: black;
            }


            nav h1 {
                font-size: 28px;
                /* Larger logo text size */
            }

            nav ul {
                margin-left: 4%;
                margin-right: auto;
            }

            .search-container {
                margin-left: 3%;
            }

            .search-box {
                right: 0;
                max-width: 200px;
                /* Wider search box */
            }

        }

        .user-info {
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
            margin: 20px 0;
            border-radius: 5px;
        }

        .user-options {
            margin: 20px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .user-options ul {
            list-style-type: none;
            padding: 0;
        }

        .user-options li {
            margin: 10px 0;
        }

        .user-options a {
            text-decoration: none;
            color: #4CAF50;
            /* Change the color as needed */
        }





header {
    background-color: #ff6347;
    color: #fff;
    padding: 15px 20px;
    text-align: center;
}

main {
    padding: 20px;
}

/* Location and Filter Section Styling */
.location-filter-section {
    background-color: #fff;
    padding: 15px 20px;
    border-bottom: 1px solid #ddd;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.location-search {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.location-icon {
    font-size: 1.2em;
    color: #ff6347; /* Color similar to the location pin */
}

.location-text {
    margin-left: 8px;
    font-size: 1.1em;
    color: #333;
    flex: 1;
}

.search-icon {
    font-size: 1.2em;
    color: #333;
    cursor: pointer;
}

.filter-buttons {
    display: flex;
    justify-content: space-between;
    gap: 10px;
}

.filter-btn {
    background-color: #f4f4f4;
    /* border: 1px solid #ddd; */
    border-radius: 20px;
    padding: 8px 16px;
    font-size: 0.9em;
    color: #333;
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s;
}

.filter-btn:hover {
    background-color: #ff6347;
    color: #fff;
    border-color: #ff6347;
}

/* Menu Category Styling */
.menu-category {
    margin-bottom: 30px;
}

.menu-category h2 {
    border-bottom: 2px solid #ff6347;
    padding-bottom: 5px;
    color: #ff6347;
}

.menu-item {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    background: #fff;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.menu-item img {
    width: 100px;
    height: 80px;
    object-fit: cover;
    border-radius: 5px;
    margin-right: 15px;
}

.item-info {
    flex: 1;
}

.item-info h3 {
    margin: 0;
    color: #333;
}

.item-info p {
    font-size: 0.9em;
    margin: 5px 0;
    color: #777;
}

.price {
    font-weight: bold;
    color: #ff6347;
}

.icon {
    background-color: #e3f8e0;
    color: #388e3c;
    padding: 3px 8px;
    border-radius: 5px;
    font-size: 0.8em;
}


body {
            background-color: #2c2c2c;
            color: white;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #FF7518;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
            background-color: #333;
        }

        table, th, td {
            border: 1px solid #444;
        }

        th, td {
            padding: 12px;
            text-align: left;
            color: #fff;
        }

        th {
            background-color: #FF7518;
        }

        td {
            background-color: #444;
        }

        .update-btn {
            background-color: #FF7518;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .update-btn:hover {
            background-color: #e76300;
            transform: scale(1.1);
        }

        tr:hover {
            background-color: #555;
            transform: scale(1.02);
            transition: all 0.2s ease-in-out;
        }

        td img {
            max-width: 50px;
            max-height: 50px;
        }

         /* Modal Styling */
         .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow: auto;
            padding-top: 60px;
        }

        .modal-content {
            background-color: #2c2c2c;
            color: white;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 8px;
            text-align: center;
        }

        .close {
            color: #FF7518;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #e76300;
            text-decoration: none;
            cursor: pointer;
        }

        .back-button {
            background-color: white;
            color: #FF7518;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            position: fixed;
            bottom: 20px; /* Positioned at the bottom */
            left: 20px; /* Positioned on the left side */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            opacity: 0.95;
            z-index: 1;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease, background-color 0.2s ease;
            width: auto;
        }

        /* Hover effect for subtle animation */
        .back-button:hover {
            background-color: #e06a15;
            color: #FFFFFF;
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
            opacity: 1;
        }

        /* Optional button text fade-in animation */
        .back-button::after {
            content: 'Back to Previous';
            opacity: 1;
        }
/* Footer Styling */
footer {
    text-align: center;
    background-color: #ff6347;
    color: white;
    padding: 10px 0;
    position: relative;
    bottom: 0;
    width: 100%;
}

footer a {
    color: #fff;
    text-decoration: underline;
}


/* General Layout of Admin Content */




    </style>
</head>

<body>
    <nav>
        <div class="hamburger" id="hamburgerMenu">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <h1>Restaurant</h1>
        <div class="nav-links">
            <ul>

            <li><a href="index.php">Home</a></li> <!-- Home link added -->
                <li><a href="user.php">UserManagement</a></li> <!-- About link added -->
                <li><a href="ManageMenu.php">MenuManagement</a></li>
                <li><a href="manage-orders.php">OrderManagement</a></li>
              


                <li>
                    <a id="loginButton">Login</a>
                    <div class="dropdown" id="loginDropdown">
                        <a href="login.php">Sign In</a>
                        <a href="../Signup.php">Sign up</a>
                        <?php if (isset($_SESSION['user'])): ?>
                            <a href="../logout.php">Logout</a>
                        <?php endif; ?>
                    </div>
                </li>
               
            </ul>
        </div>

        

        <div class="profile-container">
            <img src="https://cdn.vectorstock.com/i/500p/96/75/gray-scale-male-character-profile-picture-vector-51589675.jpg" alt="Profile Picture" class="profile-picture">
            <div class="dropdown" id="profileDropdown">
                <div class="nav-links">
                    <div class="notification-icon">🔔</div> <!-- Moved inside the dropdown -->
                    <div class="dark-mode-toggle">🌙</div>
                </div> <!-- Moved inside the dropdown -->
                
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="logout.php">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

  
    
<h1>Manage Orders</h1>

<?php
// Check if the query returns any results
if ($result->num_rows > 0) {
    echo "<table>
            <thead>
                <tr>
                    <th>Cart ID</th>
                    <th>User ID</th>
                    <th>Menu Section ID</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>State</th>
                    <th>Time to Reach</th>
                    <th>Added At</th>
                    <th>Updated At</th>
                    <th>Item Name</th>
                    <th>Item Image</th>
                    <th>Address</th> <!-- New column for address -->
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>";

    // Fetch and display each row of data
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['cart_id']}</td>
                <td>{$row['user_id']}</td>
                <td>{$row['menu_section_id']}</td>
                <td>{$row['quantity']}</td>
                <td>{$row['price']}</td>
                <td>{$row['state']}</td>
                <td>{$row['time_to_reach']}</td>
                <td>{$row['added_at']}</td>
                <td>{$row['updated_at']}</td>
                <td>{$row['item_name']}</td>
                <td><img src='{$row['item_image']}' alt='Item Image'></td>
                <td>{$row['address']}</td> <!-- Display address -->
                <td>
                    <!-- Form to update state and time_to_reach -->
                    <form method='POST' action=''>
                        <input type='hidden' name='cart_id' value='{$row['cart_id']}'>
                        <input type='text' name='state' value='{$row['state']}' required>
                        <input type='text' name='time_to_reach' value='{$row['time_to_reach']}' required>
                        <button type='submit' name='update' class='update-btn'>Update</button>
                    </form>
                </td>
              </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "No records found.";
}

// Close the connection
$conn->close();
?>

<!-- Modal for success message -->
<div id="successModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Record updated successfully!</h2>
    </div>
</div>


<button class="back-button" onclick="history.back()">Back to Previous</button> 

  



    <script>
        // Dark mode toggle functionality
        const darkModeToggle = document.querySelector('.dark-mode-toggle');
        darkModeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            document.querySelector('nav').classList.toggle('dark-mode');
        });

        // Toggle dropdown for profile picture
        const profilePicture = document.querySelector('.profile-picture');
        const profileDropdown = document.querySelector('#profileDropdown');

        profilePicture.addEventListener('click', () => {
            profileDropdown.style.display = profileDropdown.style.display === 'block' ? 'none' : 'block';
        });

        // Toggle dropdown for login button
        const loginButton = document.querySelector('#loginButton');
        const loginDropdown = document.querySelector('#loginDropdown');

        loginButton.addEventListener('click', () => {
            loginDropdown.style.display = loginDropdown.style.display === 'block' ? 'none' : 'block';
        });

        // Search functionality when clicking on the search icon
        const searchButton = document.getElementById('searchButton');
        const searchInput = document.getElementById('searchInput');

        searchButton.addEventListener('click', () => {
            const query = searchInput.value;
            if (query) {
                alert('Searching for: ' + query); // Replace this with your search function
            }
        });

        // Toggle hamburger menu
        const hamburgerMenu = document.getElementById('hamburgerMenu');
        const nav = document.querySelector('nav');

        hamburgerMenu.addEventListener('click', () => {
            nav.classList.toggle('active');
        });

        // Close dropdowns when clicking outside
        window.addEventListener('click', (event) => {
            if (!profilePicture.contains(event.target) && !profileDropdown.contains(event.target)) {
                profileDropdown.style.display = 'none';
            }
            if (!loginButton.contains(event.target) && !loginDropdown.contains(event.target)) {
                loginDropdown.style.display = 'none';
            }
        });


          // Show modal if update success flag is set
    <?php if ($update_success): ?>
        var modal = document.getElementById("successModal");
        modal.style.display = "block";

        // Close the modal when the user clicks on <span> (x)
        var span = document.getElementsByClassName("close")[0];
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Automatically close the modal after 3 seconds
        setTimeout(function() {
            modal.style.display = "none";
        }, 3000);
    <?php endif; ?>
    </script>
</body>

</html>