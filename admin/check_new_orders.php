<?php
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
            echo "Record updated successfully.";
        } else {
            echo "Error updating record: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

// Query to select data from the 'manage_order' table
$query = "SELECT cart_id, user_id, menu_section_id, quantity, price, state, time_to_reach, added_at, updated_at, item_name, item_image FROM manage_order";

// Execute the query
$result = $conn->query($query);

// Fetch the latest order (used for notifications)
$notification_query = "SELECT cart_id, item_name FROM manage_order ORDER BY added_at DESC LIMIT 1";
$notification_result = $conn->query($notification_query);
$new_order = $notification_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
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

        /* Notification dropdown style */
        .notification-dropdown {
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: #FF7518;
            color: white;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }

        .notification-dropdown:hover {
            background-color: #e76300;
        }

        .notification-content {
            display: none;
            position: absolute;
            top: 30px;
            right: 0;
            background-color: #333;
            padding: 10px;
            width: 200px;
            border-radius: 5px;
        }

        .notification-dropdown:hover .notification-content {
            display: block;
        }

        .notification-item {
            margin: 5px 0;
            padding: 5px;
            background-color: #444;
            border-radius: 3px;
        }

    </style>
</head>
<body>

<h1>Manage Orders</h1>

<!-- Notification Dropdown -->
<div class="notification-dropdown">
    New Order Notification
    <div class="notification-content">
        <?php if ($new_order): ?>
            <div class="notification-item">New Order: <?php echo htmlspecialchars($new_order['item_name']); ?></div>
        <?php else: ?>
            <div class="notification-item">No new orders</div>
        <?php endif; ?>
    </div>
</div>

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

<script>
    // Function to reload the page every 30 seconds to update the data
    setInterval(function() {
        location.reload();
    }, 30000); // 30000ms = 30 seconds
</script>

</body>
</html>
