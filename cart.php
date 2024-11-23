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

// Fetch cart items for the logged-in user
$userId = $_SESSION['user']['user_id']; // Assuming user ID is stored in the session
$sql = "SELECT * FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$cartItems = $result->fetch_all(MYSQLI_ASSOC);

// Calculate subtotal
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <style>
        /* General reset and font */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            color: #333;
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        /* Main container styling */
        .cart-container {
            display: flex;
            gap: 20px;
            max-width: 1200px;
            width: 100%;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
        }

        /* Cart items section styling */
        .cart-items {
            flex: 3;
            padding: 20px;
            border-radius: 8px;
            background-color: #fff;
        }

        .cart-items h2 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .item-image img {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
        }

        .item-details {
            flex: 1;
            margin-left: 20px;
        }

        .item-details h3 {
            font-size: 1.1em;
            margin-bottom: 5px;
            color: #333;
        }

        .item-details p {
            color: #777;
            font-size: 0.9em;
        }

        .item-quantity {
            display: flex;
            align-items: center;
        }

        .quantity-btn {
            background-color: #4b6a39;
            color: #fff;
            border: none;
            padding: 5px;
            width: 30px;
            text-align: center;
            cursor: pointer;
            border-radius: 3px;
            margin: 0 5px;
            font-weight: bold;
        }

        .item-total, .item-remove {
            text-align: center;
            color: #333;
        }

        .remove-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #e74c3c;
        }

        /* Order summary styling */
        .order-summary {
            flex: 1;
            padding: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
            color: #333;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .order-summary h2 {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: #333;
        }

        .summary-item, .summary-total {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: #333;
        }

        .summary-total {
            font-size: 1.2em;
            font-weight: bold;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }

        .checkout-btn {
            width: 100%;
            background-color: #4b6a39;
            color: #ffffff;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            font-weight: bold;
            margin-top: 20px;
        }

        /* Animation for delete button */
        @keyframes jump {
            0% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0); }
        }

        .jump-animation {
            animation: jump 0.3s ease-in-out;
        }
    </style>
</head>
<body>
<div class="cart-container">


    <div class="cart-items">
        <h2>My Cart</h2>
        <?php foreach ($cartItems as $item): ?>
            <div class="cart-item">
                <div class="item-image">
                    <img src="<?php echo htmlspecialchars($item['item_image']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                </div>
                <div class="item-details">
                    <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                    <p>$<?php echo number_format($item['price'], 2); ?></p>
                    <p><?php echo htmlspecialchars($item['quantity']); ?></p>
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
    

    <!-- Order Summary Section -->
    <div class="order-summary">
        <h2>Order Summary</h2>
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
        const button = event.target;
        button.classList.add('jump-animation');
        setTimeout(() => button.classList.remove('jump-animation'), 300);

        if (confirm("Are you sure you want to delete this item from your cart?")) {
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', cart_id: itemId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert("Item deleted successfully!");
                    location.reload();
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
