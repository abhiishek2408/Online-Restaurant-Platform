<?php
session_start();
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_management";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

// Ensure user is logged in
if (!isset($_SESSION['user']['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

$user_id = $_SESSION['user']['user_id'];

// Get POST data
$full_name = $conn->real_escape_string($_POST['full_name']);
$email = $conn->real_escape_string($_POST['email']);
$phone = $conn->real_escape_string($_POST['phone']);
$delivery_address = $conn->real_escape_string($_POST['delivery_address']);
$special_instructions = $conn->real_escape_string($_POST['special_instructions']);

// Get cart items for the user
$sql = "SELECT * FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Cart is empty.']);
    exit;
}

// Insert order details into the database
$conn->begin_transaction();

try {
    while ($row = $result->fetch_assoc()) {
        $stmt = $conn->prepare("INSERT INTO manage_order 
            (user_id, menu_section_id, quantity, price, state, time_to_reach, added_at, updated_at, item_name, item_image, delivery_address, full_name, email, phone, special_instructions) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW(), NOW(), ?, ?, ?, ?, ?, ?, ?)");
        $state = 'pending';

        $stmt->bind_param(
            "iiidssssssss",
            $user_id,
            $row['menu_section_id'],
            $row['quantity'],
            $row['price'],
            $state,
            $row['item_name'],
            $row['item_image'],
            $delivery_address,
            $full_name,
            $email,
            $phone,
            $special_instructions
        );

        $stmt->execute();
    }

    // Clear the cart after successful checkout
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $conn->commit();
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Checkout failed: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>
