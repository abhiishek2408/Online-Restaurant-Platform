
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item_id'])) {
    $cartItemId = filter_var($_POST['delete_item_id'], FILTER_VALIDATE_INT); // Validate item ID

    if ($cartItemId) {
        // Prepare delete statement
        $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
        $userId = $_SESSION['user']['user_id'];
        $stmt->bind_param("ii", $cartItemId, $userId);

        if ($stmt->execute()) {
            echo 'Item deleted successfully.';
        } else {
            echo 'Failed to delete item.';
        }
        $stmt->close();
    } else {
        echo 'Invalid item ID.';
    }
    $conn->close();
    exit(); // Stop further processing
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    // Validate and sanitize input
    $itemImage = $_POST['modalItemImage'];
    $itemName = filter_var($_POST['modalItemName'], FILTER_SANITIZE_STRING);
    $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT); // Ensure quantity is an integer
    $price = filter_var($_POST['modalItemPrice'], FILTER_VALIDATE_FLOAT); // Ensure price is a float
    $menuSectionId = filter_var($_POST['modalItemId'], FILTER_VALIDATE_INT); // Validate menu section ID
    $userId = $_SESSION['user']['user_id']; // Assuming user ID is stored in the session
    $state = 'active'; // Assuming a default state for new items



    // Check for valid data before proceeding
    if ($quantity === false || $price === false || $menuSectionId === false) {
        // Handle invalid input error (e.g., redirect, display error message)
        die("Invalid input data.");
    }

    // Decode the base64 image string
    $itemImage = preg_replace('#^data:image/\w+;base64,#i', '', $itemImage); // Remove the prefix
    $itemImage = base64_decode($itemImage); // Decode the base64 string


    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO cart (user_id, menu_section_id, quantity, price, state, added_at, updated_at, item_name, item_image) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?, ?)");

    // Check if prepare was successful
    if ($stmt === false) {
        die("Failed to prepare statement: " . $conn->error);
    }

    // Bind parameters: "iiids" corresponds to the types of the variables
    $stmt->bind_param("iiidsss", $userId, $menuSectionId, $quantity, $price, $state, $itemName, $itemImage);

   
    if ($stmt->execute()) {
        echo "$quantity x $itemName has been added to your cart!";
    } else {
        echo "Failed! $itemName has not been added to your cart!";
    }



    $stmt->close();
    $conn->close();
    exit(); // Stop further processing (important)
}




// Fetching the cart items for the current user
$userId = $_SESSION['user']['user_id']; // Ensure you have a user ID in session
$sql = "SELECT cart_id, menu_section_id, quantity, price, added_at, state, item_name, item_image FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$menuItems = array();
// Check if the query returned results

if ($result->num_rows > 0) {
   

    echo "<style>
    .cart-container {
        display: flex;
        max-width: 1051px;
        /* Fixed maximum width */
        width: 100%;
        /* Set to 100% to fit the parent container */
        min-height: 200px;
        /* Minimum height to ensure it doesn't collapse when empty */
        background-color: white;
        /* Container background color */
        padding: 20px;
        /* Padding for the container */
        border-radius: 10px;
        /* Rounded corners for the container */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        /* Optional: add shadow for better visibility */
    }


    .cart-box {
        /* Equal flexible width */
        background-color:white;
        /* Background color */
        padding: 20px;
        /* Padding for items */
        border-radius: 10px;
        /* Rounded corners */
        color: black;
        /* Text color */
        width: 50%;
        max-height: 400px;
        /* Fixed height */
        overflow-y: auto;
        /* Scroll if content overflows */
    }


    .order-summary {
        flex: 1;
        /* Equal flexible width */
        background-color: white;
        /* Background color */
        padding: 20px;
        /* Padding for summary */
        border-radius: 10px;
        /* Rounded corners */
        color: black;
        /* Text color */

        max-height: 400px;
        /* Fixed height */
        overflow-y: auto;
        /* Scroll if content overflows */
    }



    /* Hide scrollbar for Firefox */
    .order-summary,
    .cart-box {
        scrollbar-width: none;
        /* Hide scrollbar for Firefox */
    }

    /* Hide scrollbar for Internet Explorer and Edge */
    .order-summary,
    .cart-box {
        -ms-overflow-style: none;
        /* Hide scrollbar for IE and Edge */
    }



    .cart-item {
        display: flex;
        align-items: center;
        /* background-color: #013033; */
        padding: 15px;
        color: black;
        width: 450px;
        font-family: 'Roboto', sans-serif;
        border-top: .6px solid #000; /* Change to your desired color and width */
        border-bottom: .6px solid #000; /* Change to your desired color and width */
    }

    .item-image img {
        width: 50px;
        height: 50px;
        background-color: #ccc;
        border-radius: 5px;
    }

    .item-details {
        flex: 1;
        padding: 0 15px;
    }

    .item-name {
        font-size: 16px;
        margin: 0;
        font-family: 'Poppins', sans-serif;
    }

    .item-price-single {
        font-size: 14px;
        color: black;
        margin: 0;
    }

    .item-quantity {
        display: flex;
        align-items: center;
    }
    
    .item-quantity input {
        background-color: #ff847c;
            border:none;
            width: 30px;
            height: 30.8px;
            color: white;
            text-align: center;
            font-size: 18px;
            margin: 0 -2px;
        }
    .quantity-btn {
        background-color: #ff847c;
        border: none;
        color: white;
        font-size: 18px;
        padding: 5px;
        cursor: pointer;
    }

    .quantity {
        padding: 0 10px;
        font-size: 16px;
    }

    .item-price-total {
        font-size: 16px;
        padding-left: 10px;
    }

    .item-remove {
        padding-left: 10px;
    }

    .remove-btn {
        background: none;
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
    }

    .summary-item,
    .summary-total {
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
</style> ";



    echo "<div class='cart-container'>
    
     <div class='cart-box'>";

    while ($row = $result->fetch_assoc()) {
        $cartitemId = htmlspecialchars($row["cart_id"]);
        $itemImage = base64_encode($row["item_image"]);
        $itemName = htmlspecialchars($row["item_name"]);
        $itemQuantity = htmlspecialchars($row["quantity"]);
        $itemPrice = htmlspecialchars($row["price"]);
        $itemStat = htmlspecialchars($row["state"]);
        $totalPrice = $itemQuantity * $itemPrice;



        echo "<div class='cart-item'>
        <div class='item-image'>
            <img src='data:image/jpeg;base64,$itemImage' alt='$itemName'>
        </div>
        <div class='item-details'>
            <p class='item-name'>$itemName</p>
            <p class='item-price-single'>$itemPrice</p>
        </div>
        <div class='item-quantity'>
        <button class='quantity-btn' onclick='decreaseItemQuantity(this)'>-</button>
        <input type='text' class='quantity-input' name='quantity' min='1' value='1' onchange='updateItemTotalPrice(this)'>
        <button class='quantity-btn' onclick='increaseItemQuantity(this)'>+</button>
    </div>

        <div class='item-price-total'>
            <p>$totalPrice</p>
        </div>
        <div class='item-remove'>
            <button class='remove-btn' onclick='deleteCartItem($cartitemId)'>🗑️</button>
        </div>
    </div> ";
   }
    echo "</div>";
    echo "<div class='order-summary'>
            <div class='summary-item'>
            <span>Subtotal</span>
            <span>$totalPrice</span>
        </div>
        <div class='summary-item'>
            <span>Pickup</span>
            <span>FREE</span>
        </div>
        <div class='summary-total'>
            <span>Total</span>
            <span></span>
        </div>
        <button class='checkout-btn' onclick='cartMessage(this)'>Checkout</button>
        
            </div>

    </div>";

    echo "<script>

   
    function decreaseItemQuantity(button) {
        const quantityInput = button.nextElementSibling;
        let quantity = parseInt(quantityInput.value);
        if (quantity > 1) {
            quantity--;
            quantityInput.value = quantity;
            updateItemTotalPrice(quantityInput);
        }
    }

    function increaseItemQuantity(button) {
        const quantityInput = button.previousElementSibling;
        let quantity = parseInt(quantityInput.value);
        quantity++;
        quantityInput.value = quantity;
        updateItemTotalPrice(quantityInput);
    }

    function updateItemTotalPrice(input) {
        const cartItem = input.closest('.cart-item');
        const itemPrice = parseFloat(cartItem.querySelector('.item-price-single').textContent);
        const quantity = parseInt(input.value);
        const totalPriceElement = cartItem.querySelector('.item-price-total p');

        const newTotalPrice = (itemPrice * quantity).toFixed(2);
        totalPriceElement.textContent = newTotalPrice;

        updateOrderSummary();
    }

    function updateOrderSummary() {
        let subtotal = 0;
        const totalPriceElements = document.querySelectorAll('.item-price-total p');
        
        totalPriceElements.forEach((priceElement) => {
            subtotal += parseFloat(priceElement.textContent);
        });
        
        document.querySelector('.order-summary .summary-item span:last-child').textContent = subtotal.toFixed(2);
}

function cartMessage(button) {
       alert('We can't accept online orders right now');
            }
</script>";

    
} else {
    echo "<h2>Your cart is empty.</h2>";
}






$stmt->close();
$conn->close();


?>

<script>
      function deleteCartItem(cartItemId) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "index.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = xhr.responseText; // Get the response as plain text
                alert(response); // Show the response message
                if (response.includes('deleted successfully')) {
                    location.reload(); // Reload the page to reflect the changes
                }
            }
        };
        xhr.send("delete_item_id=" + cartItemId);
    }

</script>






