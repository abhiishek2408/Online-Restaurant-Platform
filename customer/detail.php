<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

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



if (isset($_GET['id'])) {
    $itemId = intval($_GET['id']);}


$stmt = $conn->prepare("SELECT product_image, Name, Description, FoodType, Price, Rating, Time, Vegan FROM menu_sections WHERE Id = ?");
$stmt->bind_param("i", $itemId);
$stmt->execute();
$result = $stmt->get_result();

// Check if any rows were returned for the main item
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $itemImage = base64_encode($row["product_image"]);
    $itemName = $row['Name'];
    $itemDescription = $row['Description'];
    $itemFoodType = $row['FoodType'];
    $itemPrice = $row['Price'];
    $itemRating = $row['Rating'];
    $itemTime = $row['Time'];
    $itemVegan = $row['Vegan'];
} else {
    die("No product found.");
}

// Fetch recommended products with FoodType 'Seasonal Menu' excluding the current item
// $stmt = $conn->prepare("SELECT Id, Name, Price, product_image FROM menu_sections WHERE FoodType = itemFoodType AND Id != ?");
// $stmt->bind_param("i", $itemId);
// $stmt->execute();
// $recommendedResult = $stmt->get_result();
$stmt = $conn->prepare("SELECT Id, Name, Price, product_image FROM menu_sections WHERE FoodType = ? AND Id != ?");
$stmt->bind_param("si", $itemFoodType, $itemId);  // Bind both FoodType and Id
$stmt->execute();
$recommendedResult = $stmt->get_result();

// Close the first statement
$stmt->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($itemName); ?> - Product Details</title>
    <style>
        /* Your existing CSS styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            background: #fff;
          
            border-radius: 8px;
        }
        header {
            text-align: center;
            margin-bottom: 20px;
        }
        .product-detail {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }
        .product-image {
            flex: 1;
            min-width: 300px;
            text-align: center;
        }
        .product-image img {
            max-width: 70%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .product-info {
            flex: 1;
            min-width: 300px;
            padding: 20px;
        }
        .product-info h2 {
            color: #e91e63;
            margin: 0 0 10px;
        }
        .rating {
            font-size: 1.2em;
            color: #ff9800;
            margin: 10px 0;
        }
        .product-info p {
            line-height: 1.6;
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
            margin-top: 20px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 4px;
            list-style: none;
        }
        
        .recommended-products {
            margin-top: 40px;
        }
        .product-suggestions {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .product-card {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
            background: #fff;
            flex: 1 1 calc(33.333% - 20px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-card img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
        footer {
            text-align: center;
            margin-top: 40px;
            padding: 10px 0;
            background-color: #f9f9f9;
            border-top: 1px solid #ccc;
        }



    </style>
</head>
<body>

    <div class="container">
        <header>
            <!-- <h1><?php echo htmlspecialchars($itemId); ?></h1> -->
            <h1><?php echo htmlspecialchars($itemName); ?></h1>
        </header>

        <div class="product-detail">
        

            <div class="product-image">
                <img src="data:image/jpeg;base64,<?php echo $itemImage; ?>" loading='lazy' alt="<?php echo htmlspecialchars($itemName); ?>">
            </div>

            <div class="product-info">
                <h2>Price: $<?php echo htmlspecialchars($itemPrice); ?></h2>
                <div class="rating">
                    <span>Rating: <?php echo htmlspecialchars($itemRating); ?> </span>
                </div>
                <p><?php echo nl2br(htmlspecialchars($itemDescription)); ?></p>

                <button class="add-to-cart">Add to Cart</button> 
            </div>
        </div>

        <div class="additional-info">
            <h3>Additional Information</h3>
            <ul>
                <li>Preparation Time: <?php echo htmlspecialchars($itemTime); ?> mins</li>
                <li>Vegan: <?php echo $itemVegan ? 'Yes' : 'No'; ?></li>
            </ul>
        </div>
    </div>
</body>


</html>


