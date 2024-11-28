<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root"; // Change if you have a different MySQL username
$password = ""; // Change if you have a different MySQL password
$dbname = "restaurant_management"; // Your database name
$port = 3307; // Your database port, change if needed

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Handle cart item deletion
// Handle cart item deletion using GET request
// Handle cart item deletion using GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_item_id'])) {
    $cartItemId = filter_var($_GET['delete_item_id'], FILTER_VALIDATE_INT); // Validate item ID

    if ($cartItemId) {
        // Prepare delete statement
        $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
        $userId = $_SESSION['user']['user_id'];

        // Check if the user is logged in and the user_id is set
        if (!isset($userId)) {
            echo 'User not logged in.';
            exit(); // Stop processing if user is not logged in
        }

        $stmt->bind_param("ii", $cartItemId, $userId);

        if ($stmt->execute()) {
            echo 'Item deleted successfully.';
        } else {
            echo 'Failed to delete item: ' . $stmt->error; // Debugging output
        }
        $stmt->close();
    } else {
        echo 'Invalid item ID.';
    }
    $conn->close();
    exit(); // Stop further processing
}



// Handle adding items to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $itemImage = $_POST['modalItemImage'];
    $itemName = filter_var($_POST['modalItemName'], FILTER_SANITIZE_STRING);
    $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
    $price = filter_var($_POST['modalItemPrice'], FILTER_VALIDATE_FLOAT);
    $menuSectionId = filter_var($_POST['modalItemId'], FILTER_VALIDATE_INT);
    $userId = $_SESSION['user']['user_id'];
    $state = 'active';

    if ($quantity === false || $price === false || $menuSectionId === false) {
        die("Invalid input data.");
    }

    $itemImage = preg_replace('#^data:image/\w+;base64,#i', '', $itemImage);
    $itemImage = base64_decode($itemImage);

    $stmt = $conn->prepare("INSERT INTO cart (user_id, menu_section_id, quantity, price, state, added_at, updated_at, item_name, item_image) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?, ?)");

    if ($stmt === false) {
        die("Failed to prepare statement: " . $conn->error);
    }

    $stmt->bind_param("iiidsss", $userId, $menuSectionId, $quantity, $price, $state, $itemName, $itemImage);

    if ($stmt->execute()) {
        echo "$quantity x $itemName has been added to your cart!";
    } else {
        echo "Failed! $itemName has not been added to your cart!";
    }

    $stmt->close();
    $conn->close();
    exit();
}

// Handle updating the quantity of an item in the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id']) && isset($_POST['quantity'])) {
    $cartItemId = filter_var($_POST['cart_id'], FILTER_VALIDATE_INT);
    $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
    $userId = $_SESSION['user']['user_id'];

    if ($cartItemId && $quantity !== false) {
        // Prepare update statement
        $stmt = $conn->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE cart_id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $cartItemId, $userId);

        if ($stmt->execute()) {
            echo 'Quantity updated successfully.';
        } else {
            echo 'Failed to update quantity.';
        }
        $stmt->close();
    } else {
        echo 'Invalid cart ID or quantity.';
    }
    $conn->close();
    exit();
}

// Fetching the cart items for the current user
$userId = $_SESSION['user']['user_id'];
$sql = "SELECT cart_id, menu_section_id, quantity, price, added_at, state, item_name, item_image FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$menuItems = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $menuItems[] = $row;
    }
}
$stmt->close();
$conn->close();
?>












<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <title>My Cart</title>
    <style>
/* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Page Loading Animation */
body {
    
    height: 100vh;
    background-color: #fff;
    animation: fadeIn 0.5s ease-out forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Main container for the cart page */
.cart-container {
    display: flex;
    justify-content: space-between;
    width: 90%;
    max-width: 1200px;
    margin: 30px auto;
    padding: 20px;
    gap: 60px;
    margin-top: 10px;
}

/* Cart header */
.cart-header {
    font-size: 2.5rem;
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

/* Cart item styling */
.cart-items {
   
    align-items: center;
    justify-content: space-between;
    padding: 15px;
    margin-top: 30px;
    width: 50%;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
}

.cart-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px;
    margin-bottom: 15px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
}

.cart-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

.cart-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 5px;
}

.item-details {
    flex-grow: 1;
    margin-left: 20px;
}

.item-name {
    font-size: 1.2rem;
    font-weight: bold;
    color: #333;
}

.item-price {
    color: #555;
    font-size: 1rem;
}

.quantity-controls {
    display: flex;
    align-items: center;
}

.quantity-btn {
    background-color: #f1f1f1;
    border: 1px solid #ccc;
    padding: 2px 3px;
    font-size: 1.2rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.quantity-btn:hover {
    background-color: #ddd;
}

.quantity-input {
    width: 40px;
    text-align: center;
    font-size: 1.2rem;
    border: 1px solid #ccc;
    margin: 0 10px;
}

.remove-btn {
    
    color: white;
    border: none;
   background: none;
    cursor: pointer;
    font-size: 1rem;
   
}


/* Order summary */
.order-summary {
    flex: 1;
    background-color: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    width: 300px;
    margin-top: 30px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #ddd;
}

.summary-row:last-child {
    border-bottom: none;
}

.summary-row span {
    font-size: 1rem;
    font-weight: bold;
}

#subtotal {
    color: #333;
}

#grand-total {
    color: #2ecc71;
    font-size: 1.2rem;
}

.checkout-btn {
    background-color: #ff7518;
    color: white;
    border: none;
    padding: 10px 20px;
    width: 100%;
    font-size: 1.2rem;
    cursor: pointer;
    border-radius: 5px;
    margin-top: 20px;
    transition: background-color 0.3s ease;
}

.checkout-btn:hover {
    background-color: #ff7517;
}

/* Checkbox for selecting items */
.cart-item-checkbox {
    display: none;
}

/* Style changes when an item is selected */
.cart-item.selected {
    border: 2px solid #3498db;
    background-color: #f0f8ff;
}

/* Style changes on hover */
.cart-item:hover {
    cursor: pointer;
}

/* Responsive Design */
@media (max-width: 768px) {
    .cart-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .item-details {
        margin-left: 0;
        margin-top: 10px;
    }

    .quantity-controls {
        margin-top: 10px;
    }

    .remove-btn {
        margin-top: 10px;
    }

    .order-summary {
        margin-top: 30px; /* Adjusted to match .cart-container */
    }

    .checkout-btn {
        font-size: 1.1rem;
    }
}





#checkoutModal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1000;
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s ease-in-out;
    }

    /* Checkout Modal Styling */
    .checkout-modal-container {
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        width: 90%;
        height: 100%;
        max-width: 500px;
        position: relative;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        animation: slideUp 0.5s ease-out;
    }

    h2 {
        text-align: center;
        font-size: 2rem;
        margin-bottom: 20px;
        color: #333;
    }

    /* Form Styling */
    .checkout-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    label {
        font-size: 1.1rem;
        color: #333;
        margin-bottom: 5px;
    }

    input, textarea {
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 1rem;
        outline: none;
        transition: border-color 0.3s ease;
    }

    input:focus, textarea:focus {
        border-color: #007BFF;
    }

    textarea {
        resize: vertical;
        min-height: 80px;
    }

    /* Buttons Styling */
    .modal-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .submit-btn, .cancel-btn {
        padding: 6px 15px;
        border-radius: 6px;
        font-size: 1rem;
        margin-top: -7%;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .submit-btn {
        background-color: #28a745;
        color: #fff;
    }

    .submit-btn:hover {
        background-color: #218838;
    }

    .cancel-btn {
        background-color: #dc3545;
        color: #fff;
    }

    .cancel-btn:hover {
        background-color: #c82333;
    }

    /* Modal Close Button */
    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 1.5rem;
        color: #333;
        cursor: pointer;
        background: none;
        border: none;
    }

    /* Animation for Modal */
    @keyframes fadeIn {
        0% {
            opacity: 0;
        }
        100% {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        0% {
            transform: translateY(50px);
        }
        100% {
            transform: translateY(0);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .checkout-modal-container {
            width: 90%;
            padding: 20px;
        }

        h2 {
            font-size: 1.8rem;
        }
    }
    </style>


   
</head>
<body>
   
<?php include('Navbar.php'); ?>

<div class="cart-container">
        <div class="cart-items">
            <?php if (!empty($menuItems)) : ?>
                <?php foreach ($menuItems as $item) : ?>
                    <div class="cart-item" data-cart-id="<?= $item['cart_id'] ?>">
                        <input type="checkbox" class="cart-item-checkbox" id="select-item-<?= $item['cart_id'] ?>" onchange="updateSummary()">
                        <div class="item-image">
                            <img src="data:image/jpeg;base64,<?= base64_encode($item['item_image']) ?>" alt="<?= htmlspecialchars($item['item_name']) ?>">
                        </div>
                        <div class="item-details">
                            <p class="item-name"><?= htmlspecialchars($item['item_name']) ?></p>
                            <p class="item-price">Price: $<?= number_format($item['price'], 2) ?></p>
                        </div>
                        <div class="quantity-controls">
                            <button class="quantity-btn" onclick="updateQuantity(<?= $item['cart_id'] ?>, -1)">-</button>
                            <input type="text" class="quantity-input" id="quantity-<?= $item['cart_id'] ?>" value="<?= htmlspecialchars($item['quantity']) ?>" readonly>
                            <button class="quantity-btn" onclick="updateQuantity(<?= $item['cart_id'] ?>, 1)">+</button>
                        </div>
                        <div class="total-price">
                            $<span id="total-<?= $item['cart_id'] ?>"><?= number_format($item['quantity'] * $item['price'], 2) ?></span>
                        </div>
                        <button class="remove-btn" onclick="deleteCartItem(<?= $item['cart_id'] ?>)">🗑️
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>

        <div class="order-summary">
            <div class="summary-row">
                <span>Subtotal</span>
                <span>$<span id="subtotal">0.00</span></span>
            </div>
            <div class="summary-row">
                <span>Pickup</span>
                <span>FREE</span>
            </div>
            <div class="summary-row" style="font-weight: bold; font-size: 1.2rem;">
                <span>Total</span>
                <span>$<span id="grand-total">0.00</span></span>
            </div>
            <button class="checkout-btn">Checkout</button>
        </div>
    </div>


    <!-- Checkout Modal -->
    <div id="checkoutModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1000; justify-content:center; align-items:center;">
    <div class="checkout-modal-container">
        <h2>Checkout</h2>
        <form id="checkoutForm" class="checkout-form">
            <div class="form-group">
                <label for="fullName">Full Name:</label>
                <input type="text" id="fullName" name="full_name" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" required>
            </div>

            <div class="form-group">
                <label for="deliveryAddress">Delivery Address:</label>
                <textarea id="deliveryAddress" name="delivery_address" required></textarea>
            </div>

            <div class="form-group">
                <label for="specialInstructions">Special Instructions:</label>
                <textarea id="specialInstructions" name="special_instructions"></textarea>
            </div>

            <div class="modal-buttons">
                <button type="button" onclick="submitCheckout()" class="submit-btn">Submit</button>
                <button type="button" onclick="closeModal()" class="cancel-btn">Cancel</button>
            </div>
        </form>
    </div>
</div>




    <?php include('Footer.php'); ?>




    <script>


// Delete cart item using fetch API
// Delete cart item using fetch API
function deleteCartItem(cartItemId) {
    if (!confirm('Are you sure you want to remove this item?')) return;

    const url = `add_to_cart.php?delete_item_id=${cartItemId}`;

    // Log the URL to see if it's correct
    console.log(`Sending DELETE request to: ${url}`);

    // Use fetch to send the GET request
    fetch(url, {
        method: 'GET', // GET request method
    })
    .then(response => response.text())  // Assuming the PHP will return text
    .then(data => {
        console.log(data); // Log the server's response
        // Check the response to determine success
        if (data.includes('Item deleted successfully.')) {
            alert('Item removed from cart!');
            location.reload(); // Reload the page to update the cart
        } else {
            alert('Failed to remove the item: ' + data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('There was an error removing the item.');
    });
}




        function updateQuantity(cartItemId, change) {
            const quantityInput = document.getElementById(`quantity-${cartItemId}`);
            const currentQuantity = parseInt(quantityInput.value);
            const newQuantity = currentQuantity + change;

            if (newQuantity < 1) return; // Prevent negative or zero quantity

            quantityInput.value = newQuantity;

            const itemPriceElement = document.querySelector(`#total-${cartItemId}`).closest('.cart-item').querySelector('.item-price');
            if (itemPriceElement) {
                const itemPrice = parseFloat(itemPriceElement.textContent.split('$')[1]);
                const newTotal = (newQuantity * itemPrice).toFixed(2);
                document.getElementById(`total-${cartItemId}`).textContent = newTotal;
            } else {
                console.error("Item price element not found!");
            }

            updateSummary(); // Update the cart summary (subtotal, grand total)

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send(`cart_id=${cartItemId}&quantity=${newQuantity}`);
        }

        // Function to update cart summary (subtotal and grand total)
        function updateSummary() {
            let subtotal = 0;
            const cartItems = document.querySelectorAll('.cart-item');

            cartItems.forEach(item => {
                const isSelected = item.querySelector('.cart-item-checkbox').checked;
                if (isSelected) {
                    const itemPrice = parseFloat(item.querySelector('.item-price').textContent.split('$')[1]);
                    const quantity = parseInt(item.querySelector('.quantity-input').value);
                    subtotal += itemPrice * quantity;
                }
            });

            const grandTotal = subtotal; // In this case, there's no extra charge for pickup or taxes

            // Update the subtotal and grand total in the summary
            document.getElementById('subtotal').textContent = subtotal.toFixed(2);
            document.getElementById('grand-total').textContent = grandTotal.toFixed(2);
        }

        // Toggle selection on clicking the cart item container
        document.querySelectorAll('.cart-item').forEach(item => {
            item.addEventListener('click', function() {
                const checkbox = this.querySelector('.cart-item-checkbox');
                checkbox.checked = !checkbox.checked; // Toggle the checkbox state
                this.classList.toggle('selected', checkbox.checked); // Toggle the visual selected state
                updateSummary(); // Update the summary after selection change
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            updateSummary();  // Ensure summary is updated when the page loads
        });




        // Show the checkout modal
function openCheckoutModal() {
    document.getElementById('checkoutModal').style.display = "block";
}

// Close the checkout modal
function closeModal() {
    document.getElementById('checkoutModal').style.display = "none";
}

// Event listener for the checkout button
document.querySelector('.checkout-btn').addEventListener('click', function() {
    openCheckoutModal();
});




function submitCheckout() {
        // This is where you can add your form submission logic
        alert("Checkout submitted!");
        closeModal(); // Close modal after submission
    }

    function closeModal() {
        document.getElementById('checkoutModal').style.display = 'none';
    }

    // Function to show the modal
    function showModal() {
        document.getElementById('checkoutModal').style.display = 'flex';
    }


// Handle form submission to process the checkout
document.getElementById('checkoutForm').addEventListener('submit', function(event) {
    event.preventDefault();  // Prevent the form from reloading the page

    // Collect form data
    const name = document.getElementById('name').value;
    const address = document.getElementById('address').value;
    const payment = document.getElementById('payment').value;

    // Validate form data
    if (!name || !address || !payment) {
        alert("Please fill all fields.");
        return;
    }

    // Prepare order data
    const orderData = {
        user_id: <?php echo $_SESSION['user']['user_id']; ?>,
        name: name,
        address: address,
        payment: payment,
        items: getSelectedItems() // Assume this is a function to collect items in the cart
    };

    // Send order data to the server via AJAX
    fetch('process_checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Order placed successfully!');
            closeModal(); // Close the modal
            window.location.reload(); // Reload page or redirect as needed
        } else {
            alert('Order failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error processing order:', error);
        alert('There was an error processing your order.');
    });
});


// Function to collect selected items from the cart
function getSelectedItems() {
    const selectedItems = [];
    const cartItems = document.querySelectorAll('.cart-item-checkbox:checked');

    cartItems.forEach(item => {
        const cartId = item.closest('.cart-item').dataset.cartId;
        const quantity = item.closest('.cart-item').querySelector('.quantity-input').value;
        const price = item.closest('.cart-item').querySelector('.item-price').textContent.split('$')[1];

        selectedItems.push({
            cart_id: cartId,
            quantity: parseInt(quantity),
            price: parseFloat(price)
        });
    });

    return selectedItems;
}

    </script>
</body>
</html>
