<?php
// $password_input = 'hello123'; 
// $stored_hashed_password = '$2b$12$mv6Cr2jIRTAuOm1Fk2VdD.uOP7NgcUMG5z4Iutbob1FSC3hGI5L5C'; // The hashed password from the database

// if (password_verify($password_input, $stored_hashed_password)) {
//     echo "Password is valid!";
// } else {
//     echo "Invalid password!";
// }


include('config/db.php'); // Include database connection settings

// Variables (ensure these are set correctly)
$userId = 1;
$reservationDate = '2024-11-01';
$slots = '17:00 - 19:00';

// Prepare the SQL statement
$sql = "SELECT * FROM reservations WHERE user_id = ? AND reservation_date = ? AND slots = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

// Bind parameters
$stmt->bind_param("iss", $userId, $reservationDate, $slots);

// Execute the statement
if (!$stmt->execute()) {
    die("Error executing statement: " . $stmt->error);
}

// Get the result
$result = $stmt->get_result();

// Fetch results
if ($result->num_rows > 0) {
   
        // Process each reservation row (e.g., display it or store it in an array)
        print_r($result->num_rows);
       echo "<br> $reservationDate  $slots";

} else {
    echo "No reservations found for the specified criteria.";
}

// Close the statement
$stmt->close();



?> 
