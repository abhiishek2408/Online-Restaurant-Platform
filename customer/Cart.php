<?php
// Sample Cart Items (this could be fetched from a database in a real application)
$cart_items = [
    [
        "name" => "Italy Pizza",
        "description" => "Extra cheese and topping",
        "price" => 681,
        "image" => "pizza.jpg"
    ],
    [
        "name" => "Combo Plate",
        "description" => "Extra cheese and topping",
        "price" => 681,
        "image" => "combo_plate.jpg"
    ],
    [
        "name" => "Spanish Rice",
        "description" => "Extra garlic",
        "price" => 681,
        "image" => "spanish_rice.jpg"
    ]
];

// Shipping Cost
$shipping_cost = 4;

// Calculate Subtotal
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'];
}

// Total including shipping
$total = $subtotal + $shipping_cost;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #edf1fc;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .container {
            display: flex;
            max-width: 1200px;
            margin: 50px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .cart-items {
            flex: 2;
            padding: 20px;
        }
        .cart-items h2 {
            margin-bottom: 20px;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .cart-item img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
        }
        .cart-item-details {
            flex: 2;
            margin-left: 20px;
        }
        .cart-item-details h3 {
            margin: 0;
            font-size: 18px;
        }
        .cart-item-details p {
            margin: 5px 0;
            color: gray;
        }
        .cart-item-controls {
            display: flex;
            align-items: center;
        }
        .cart-item-controls input {
            width: 40px;
            text-align: center;
        }
        .delete-item {
            color: red;
            cursor: pointer;
            margin-left: 10px;
        }
        .payment-details {
            flex: 1;
            background-color: #4b4ea3;
            padding: 20px;
            color: white;
        }
        .payment-details h3 {
            margin-bottom: 20px;
        }
        .payment-details input,
        .payment-details select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            border-radius: 5px;
        }
        .payment-details .card-types {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .payment-details .card-types img {
            width: 50px;
        }
        .checkout-button {
            background-color: #00c853;
            color: white;
            border: none;
            padding: 15px;
            font-size: 18px;
            cursor: pointer;
            width: 100%;
            border-radius: 5px;
        }
        .summary {
            margin-top: 20px;
        }
        .summary p {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .summary .total {
            font-weight: bold;
            font-size: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Shopping Cart Section -->
    <div class="cart-items">
        <h2>Shopping Cart</h2>
        <?php foreach ($cart_items as $item): ?>
            <div class="cart-item">
                <img src="<?= $item['image']; ?>" alt="<?= $item['name']; ?>">
                <div class="cart-item-details">
                    <h3><?= $item['name']; ?></h3>
                    <p><?= $item['description']; ?></p>
                </div>
                <div class="cart-item-controls">
                    <input type="number" value="1" min="1">
                    <span>$<?= $item['price']; ?></span>
                    <span class="delete-item">🗑️</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Payment Details Section -->
    <div class="payment-details">
        <h3>Card Details</h3>
        <div class="card-types">
            <img src="mastercard.png" alt="MasterCard">
            <img src="visa.png" alt="Visa">
            <img src="rupay.png" alt="RuPay">
            <button style="background: none; border: none; color: #fff; cursor: pointer;">See All</button>
        </div>
        <input type="text" placeholder="Name on card">
        <input type="text" placeholder="Card Number" maxlength="16">
        <div style="display: flex; gap: 10px;">
            <input type="text" placeholder="Expiration (MM/YY)" style="flex: 1;">
            <input type="text" placeholder="CVV" maxlength="3" style="flex: 1;">
        </div>

        <!-- Order Summary -->
        <div class="summary">
            <p>Subtotal: <span>$<?= $subtotal; ?></span></p>
            <p>Shipping: <span>$<?= $shipping_cost; ?></span></p>
            <p class="total">Total (Tax incl.): <span>$<?= $total; ?></span></p>
        </div>

        <!-- Checkout Button -->
        <button class="checkout-button">Checkout - $<?= $total; ?></button>
    </div>
</div>

</body>
</html>
