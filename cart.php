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

// Check if it's a delete request
$data = json_decode(file_get_contents("php://input"), true);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['action']) && $data['action'] === 'delete') {
    $itemId = $data['cart_id'];
    $userId = $_SESSION['user']['user_id']; // Assuming user ID is stored in the session

    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
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
    $conn->close();
    exit(); // Stop further processing to handle the delete request only
}


$stmt->close();
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: white;
    color: #ffffff;
    display: flex;
    justify-content: center;
    padding: 20px;
    
}

.cart-container {
    display: flex;
    max-width: 1200px;
    width: 100%;
    gap: 20px;
    background-color: yellow;
}


.cart-items {
    background-color: yellow;
    padding: 20px;
    border-radius: 10px;
    width: 50%;
}


 .order-summary {
    background-color: pink;
    padding: 20px;
    width: 50%;
    border-radius: 10px;
}

.cart-items {
    flex: 3;
}

.cart-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #2a6779;
    padding: 15px 0;
}

.item-image img {
    width: 50px;
    height: 50px;
    border-radius: 5px;
}

.item-details {
    flex: 1;
    margin-left: 20px;
}

.item-quantity {
    display: flex;
    align-items: center;
}

.quantity-btn {
    background-color: #ff847c;
    color: #fff;
    border: none;
    padding: 5px;
    width: 30px;
    text-align: center;
    cursor: pointer;
    border-radius: 3px;
    margin: 0 5px;
}

.item-total, .item-remove {
    text-align: center;
}

.remove-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
    color: #ff847c;
}

.order-summary {
    flex: 1;
    color: #ffffff;
}

.summary-item, .summary-total {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.checkout-btn {
    width: 100%;
    background-color: #ff847c;
    color: #ffffff;
    border: none;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

@keyframes jump {
    0% { transform: translateY(0); }
    50% { transform: translateY(-5px); } /* Move up */
    100% { transform: translateY(0); } /* Move back to original position */
}

.jump-animation {
    animation: jump 0.3s ease-in-out;
}

    </style>
</head>
<body>
<div class="cart-container">

    <div class="cart-items">
        <h2>My cart</h2>
        <?php foreach ($cartItems as $item): ?>
            <div class="cart-item">
                <div class="item-image">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                </div>
                <div class="item-details">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                    <p>$<?php echo number_format($item['price'], 2); ?></p>
                </div>
                <div class="item-quantity">
                    <button class="quantity-btn">-</button>
                    <span><?php echo htmlspecialchars($item['quantity']); ?></span>
                    <button class="quantity-btn">+</button>
                </div>
                <div class="item-total">
                    <p>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                </div>
                <div class="item-remove">
                    <button class="remove-btn" onclick="deleteCartItem(<?php echo $item['cart_id']; ?>)">delete</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    

    <div class="order-summary">
        <h2>Order summary</h2>
        <div class="summary-item">
            <span>Subtotal</span>
            <span>$<?php echo number_format($subtotal, 2); ?></span>
        </div>
        <div class="summary-item">
            <span>Pickup</span>
            <span>FREE</span>
        </div>
        <div class="summary-total">
            <span>Total</span>
            <span>$<?php echo number_format($subtotal, 2); ?></span>
        </div>
        <button class="checkout-btn">Checkout</button>
    </div>
</div>

<script>
    function deleteCartItem(itemId) {
        const button = event.target; // Get the clicked button

        // Add the jump animation class to the button
        button.classList.add('jump-animation');

        // Remove the animation class after the animation duration (300ms in this case)
        setTimeout(() => button.classList.remove('jump-animation'), 300);

        // Confirm deletion
        if (confirm("Are you sure you want to delete this item from your cart?")) {
            fetch('', { // Empty URL to send request to the same file
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', cart_id: itemId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert("Item deleted successfully!");
                    location.reload(); // Refresh the page to reflect changes
                } else {
                    alert("Failed to delete item. Please try again.");
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
</script>

</body>
</html>
