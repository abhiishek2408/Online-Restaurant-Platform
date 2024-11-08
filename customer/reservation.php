<?php
session_start();
include('../config/db.php');

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    echo "You need to log in first.";
    exit;  // Stop the execution of the script if the user is not logged in
}

// Handle reservation
if (isset($_POST['reserve'])) {
    // Check if user ID is set
    if (!isset($_SESSION['user']['id'])) {
        echo "User ID is not set in the session.";
        exit;
    }
    
    $user_id = $_SESSION['user']['id']; // This should now be set correctly
    $date = $_POST['date'];
    $time = $_POST['time'];
    $num_guests = $_POST['num_guests'];

    // Debugging output
    echo "User ID: $user_id <br>";
    echo "Date: $date <br>";
    echo "Time: $time <br>";
    echo "Number of Guests: $num_guests <br>";

    // Prepare the SQL query
    $sql = "INSERT INTO reservations (user_id, date, time, num_guests) VALUES ('$user_id', '$date', '$time', '$num_guests')";

    // Execute the query and check for errors
    if ($conn->query($sql) === TRUE) {
        echo "Reservation made successfully!";
    } else {
        echo "Error: " . $conn->error; // Outputs the error message
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Make a Reservation</title>
</head>
<body>
    <h2>Make a Reservation</h2>
    <form method="post" action="">
        Date: <input type="date" name="date" required><br>
        Time: <input type="time" name="time" required><br>
        Number of Guests: <input type="number" name="num_guests" required><br>
        <button type="submit" name="reserve">Reserve</button>
    </form>
</body>
</html>
