<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

if (isset($_POST['item_id'])) {
    $user_id = $_SESSION['user']['user_id'];
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    // Get item price
    $sql = "SELECT price FROM menu_items WHERE item_id='$item_id'";
    $result = $conn->query($sql);
    $item = $result->fetch_assoc();
    $total_amount = $item['price'] * $quantity;

    // Insert order
    $sql = "INSERT INTO orders (user_id, total_amount) VALUES ('$user_id', '$total_amount')";
    if ($conn->query($sql) === TRUE) {
        $order_id = $conn->insert_id;
        // Insert order details
        $sql = "INSERT INTO order_details (order_id, item_id, quantity, price) VALUES ('$order_id', '$item_id', '$quantity', '{$item['price']}')";
        $conn->query($sql);
        echo "Order placed successfully!";
    } else {
        echo "Error placing order: " . $conn->error;
    }
}
?>
