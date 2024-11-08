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
    </style>
</head>
<body>

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
