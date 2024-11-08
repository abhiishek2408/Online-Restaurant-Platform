<?php
session_start(); // Start the session

// Initialize count in session if it doesn't exist
if (!isset($_SESSION['count'])) {
    $_SESSION['count'] = 1; // Initialize count
}

// Check if increment, decrement, or reset button was clicked
if (isset($_POST['increment'])) {
    $_SESSION['count']++; // Increment count
} elseif (isset($_POST['decrement'])) {
    if ($_SESSION['count'] > 1) { // Prevent count from going below 1
        $_SESSION['count']--; // Decrement count
    }
} elseif (isset($_POST['reset'])) {
    $_SESSION['count'] = 1; // Reset count to 1
}

// Store the current count in a variable
$current_count = $_SESSION['count'];

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
    die("Connection failed: " . $conn->connect_error);
}

// SQL Query to fetch vegetarian and vegan dishes
$sql = "SELECT * FROM menu_sections WHERE FoodType = 'Grilled and Barbecue Dishes' ORDER BY name";
$result = $conn->query($sql);

// Check for SQL query error
if (!$result) {
    die("Error in SQL query: " . $conn->error);
}

// Initialize an array to store the menu items
$menuItems = array();

echo "<style>
        .menu-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .menu-item-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px;
            width: calc(25% - 20px); 
            box-sizing: border-box;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            transition: transform 0.2s;
            position: relative;
            cursor: pointer;
        }
        .menu-item-card:hover {
            transform: scale(1.02);
        }
        .image-container {
            position: relative; 
            text-align: center;
        }
        img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .name-rating {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 10px 0;
        }
        .price-time {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            color: grey;
        }
        .menu-item-card p {
            color: grey;
            margin: 5px 0;
        }
        .food-type-heading {
            width: 100%;
            font-size: 1.5em;
            color: #333;
            margin: 20px 0 10px 0;
            font-weight: bold;
        }
        .vegan-label, .non-vegan-label {
            text-align: center;
            margin: 5px 0;
            font-weight: bold;
        }
        .vegan-label {
            color: green;
        }
        .non-vegan-label {
            color: red;
        }
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover, .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .loader {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            position: absolute;
            top: 50%;
            left: 50%;
            margin: -30px 0 0 -30px;
            z-index: 9999;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 2px;
            background: #fff;
            border-radius: 8px;
            font-family: 'Lato', sans-serif;
        }

        .itemname {
            font-size: 1.3em;
            color: #000;
            margin-left: 50%;
            textalign:left;
             font-family: 'Roboto', sans-serif; /* Simple and clean for item names */
        }

        .product-detail {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 40px;
            font-family: 'Lato', sans-serif;
        }
        .product-content {
    flex: 1;
    min-width: 300px;
    text-align: left;
    
    position: relative;
    top: -22px; /* Adjust value as needed */
}

        .product-content .product-image {
            
            min-width: 300px;
            text-align: center;
        }
        .product-content .product-image img {
            max-width: 70%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .product-info {
            flex: 1;
            min-width: 300px;
            padding: 20px;
            font-family: 'Lato', sans-serif;
        }
        .product-info h2 {
            color: #e91e63;
            margin: 0 0 10px;
            font-family: 'Lato', sans-serif;
        }

        textarea {
    width: 200px; /* Set the desired width */
    height: 50px; /* Set the desired height */
    resize: none; /* Prevents users from resizing the textarea */
}
        .rating {
            font-size: 1.2em;
            color: #ff9800;
            margin: 10px 0;
            font-family: 'Lato', sans-serif;
        }
        .add-to-cart {
            padding: 12px 20px;
            background: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 4px;
            transition: background 0.3s;
        }
        .add-to-cart:hover {
            background: #45a049;
        }
        .additional-info {
            margin-top:20px;
            margin-left:50px;
            font-family: 'Lato', sans-serif;
            padding: 0;
            border-radius: 4px;
             list-style: none;
        }
        
        .additional-info ul li {
            list-style: none;
            font-family: 'Lato', sans-serif;
        }

        .quantity-control {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .quantity-control button {
            padding: 5px 15px;
            font-size: 18px;
        }

        .container label {
            font-size: 14px;
            color: #9ab;
            display: block;
            margin-bottom: 5px;
        }

        .special-request input, .quantity-selector {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: none;
            font-size: 16px;
            
            color: white;
        }

        .quantity-selector {
            display: flex;
            justify-content: left;
            align-items: left;
        }

        .quantity-selector button {
            background-color:black;
            border: none;
            color: white;
            font-size: 18px;
            width: 40px;
            height: 40px;
            cursor: pointer;
            margin: 0 -2px;
            
        }

        .quantity-selector input {
        background-color:black;
            border:none;
            width: 40px;
            height: 40px;
            color: white;
            text-align: center;
            font-size: 18px;
            margin: 0 -2px;
        }
    </style>";

echo "<div class='menu-container'>";
echo "<div class='food-type-heading'>Grilled and Barbecue Dishes</div>";

// Loop through the result set and fetch menu items
while ($row = $result->fetch_assoc()) {
    $itemId = urlencode($row["Id"]);
    $itemName = htmlspecialchars($row["name"]);
    $itemPrice = htmlspecialchars($row["price"]);
    $itemRating = htmlspecialchars($row["rating"]);
    $itemTime = htmlspecialchars($row["time"]);
    $itemQuantity = htmlspecialchars($row["quantity"]); // Ensure this column exists
    $itemDescription = htmlspecialchars($row["description"]); // Ensure this column exists
    $itemVegan = $row["vegan"] ? "🌱 Vegan" : "🍗 Non-Vegan";
    $veganClass = $row["vegan"] ? "vegan-label" : "non-vegan-label";
    $itemImage = base64_encode($row["product_image"]);

    echo "<div class='menu-item-card' onclick='loadDetail(\"$itemId\", \"$itemName\", \"$itemImage\", $itemPrice, \"$itemRating\", \"$itemQuantity\", \"$itemDescription\", \"$itemVegan\", \"$itemTime\")'>
            <div class='image-container'>
                <img src='data:image/jpeg;base64,$itemImage' alt='$itemName'>
            </div>
            <div class='name-rating'>
                <h3>$itemName</h3>
                <div class='rating'>$itemRating ⭐</div>
            </div>
            <div class='price-time'>
                <p>Price: $$itemPrice</p>
                <p>Time: $itemTime min</p>
            </div>
            <p class='$veganClass'>$itemVegan</p>
          </div>";
}

echo "</div>";

// Modal HTML
echo "<div id='detailsModal' class='modal'>
        <div class='modal-content'>
            <span class='close'>&times;</span>
        <div class='container'>
        
        <div class='itemname'>
        <h2 id='modalItemId' style='display:none;'></h2>
        <h2 id='modalItemName'></h2>
</div>
        
        <div class='product-detail'>
        
        
        <div class='product-content'>
        <div class='product-image'>
        <img id='modalItemImage' src='' alt=''>
        </div>

         <div class='additional-info'>
                    <h3 style='font-family: 'Lato', sans-serif;'>Additional Information</h3>
                     <ul>
                        <li>Preparation Time: <span id='modeltime'>mins</span></li>
                        <li>Vegan: <span id='modelveganonvegan'></span></li>
                     </ul>
         </div>
        </div>
        
        <div class='product-info'>
                    <h2>Price: $<span id='modalItemPrice'></span></h2>

                    <div class='rating'>
                        <p>Rating: <span id='modalItemRating'></span>⭐</p>
                    </div>

                    <p id='modalItemDescription'></p>
    <label for='special-request'>Special Request</label>
    <textarea type='text' id='special-request' placeholder='We’ll do our best to accommodate any requests when possible.'>
    </textarea>

                    <label for='quantity'>Quantity</label>
    <div class='quantity-selector'>
        <button type='button' onclick='decreaseQuantity()'>-</button>
        <input type='text' id='quantity' name='quantity' min='1' value='1' onchange='updateTotalPrice()'>
        <button type='button' onclick='increaseQuantity()'>+</button>
    </div>
                    <button class='add-to-cart' name='add_to_cart' onclick='addToCart()'>Add to Cart | $<span id='totalPrice'></span></button>
                    <p id='cart-added'></p>
                </div>
                
            </div> 

               
               
    
    

        </div>
        </div>
    </div>";


// Handle Add to Cart logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $itemName = $_POST['modalItemName'];
    $quantity = $_POST['quantity']; // Capture quantity from POST
    $price = $_POST['modalItemPrice'];

    // Calculate total price for the quantity
    $totalPrice = $price * $quantity;

    // Example: Store the item in session cart
    $_SESSION['cart'][] = [
        'name' => $itemName,
        'quantity' => $quantity,
        'price' => $price,
        'total' => $totalPrice
    ];

    echo "<script>alert('$quantity x $itemName has been added to your cart for $$totalPrice!');</script>";
}


$conn->close();
?>

<script>
    // Load modal details
    function loadDetail(id,name, image, price, rating, quantity, description, veganonvegan, time) {
        document.getElementById('modalItemId').innerText = id;
        document.getElementById('modalItemName').innerText = name;
        document.getElementById('modalItemImage').src = 'data:image/jpeg;base64,' + image;
        document.getElementById('modalItemPrice').innerText = price;
        document.getElementById('modalItemRating').innerText = rating;
        document.getElementById('modalItemDescription').innerText = description;
        document.getElementById('totalPrice').innerText = price; // Set initial total price
        document.getElementById('quantity').value = 1; // Reset quantity to 1
        document.getElementById('modelveganonvegan').innerText = veganonvegan; // Reset quantity to 1
        document.getElementById('modeltime').innerText = time; // Reset quantity to 1
        document.getElementById('detailsModal').style.display = 'block'; // Show modal
    }

    // Function to increase quantity
    function increaseQuantity() {
        const quantityInput = document.getElementById("quantity");
        const currentQuantity = parseInt(quantityInput.value);
        const maxQuantity = parseInt(quantityInput.max) || 10; // Set default max quantity if not defined

        if (currentQuantity < maxQuantity) {
            quantityInput.value = currentQuantity + 1;
            updateTotalPrice();
        }
    }

    // Function to decrease quantity
    function decreaseQuantity() {
        const quantityInput = document.getElementById("quantity");
        const currentQuantity = parseInt(quantityInput.value);

        if (currentQuantity > 1) {
            quantityInput.value = currentQuantity - 1;
            updateTotalPrice();
        }
    }
    

    // Function to update total price
    function updateTotalPrice() {
        const unitPrice = parseFloat(document.getElementById('modalItemPrice').innerText);
        const quantity = parseInt(document.getElementById('quantity').value);
        document.getElementById('totalPrice').innerText = (unitPrice * quantity).toFixed(2);
    }

   

    function addToCart() {
    const itemImage = document.getElementById('modalItemImage').src;
    const itemName = document.getElementById('modalItemName').innerText;
    const itemPrice = parseFloat(document.getElementById('modalItemPrice').innerText);
    const quantity = parseInt(document.getElementById('quantity').value);
    const menuSectionId = document.getElementById('modalItemId').innerText; // Use modalItemId for the section ID

    const form = new FormData();
    form.append('modalItemImage', itemImage);
    form.append('modalItemName', itemName);
    form.append('modalItemPrice', itemPrice);
    form.append('quantity', quantity);
    form.append('modalItemId', menuSectionId); // Send the menu section ID
    form.append('add_to_cart', 'true');

    fetch('add_to_cart.php', {
        method: 'POST',
        body: form,
        credentials: 'same-origin', // Ensure session handling
    })
    .then(response => response.text()) // Read response as plain text
    .then(data => {
        alert(data); // Show the plain text response from the server
        document.getElementById('detailsModal').style.display = 'none';
    })
    .catch(error => console.error('Error:', error));
}






    // Close modal
    document.querySelector('.close').onclick = function() {
        document.getElementById('detailsModal').style.display = 'none';
    }

    // Close modal on outside click
    window.onclick = function(event) {
        const modal = document.getElementById('detailsModal');
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }
</script>