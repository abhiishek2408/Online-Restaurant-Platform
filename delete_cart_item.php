<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_management";
$port = 3307;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Get the JSON data from the request
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['cart_id'])) {
    $itemId = $data['cart_id'];
    $userId = $_SESSION['user']['user_id']; // Assuming user ID is stored in the session

    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $itemId, $userId);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Item deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete item.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare delete statement.']);
    }
}

// Close the connection
$conn->close();
exit();
?>