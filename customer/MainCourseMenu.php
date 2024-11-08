<?php
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

// SQL query to fetch only items from the Seasonal Menu
$sql = "SELECT * FROM menu_sections WHERE food_category = 'Main Course' ORDER BY name"; // Replace 'menu_sections' with your table name
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Start output
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
                width: calc(25% - 20px); /* 4 items in a row */
                box-sizing: border-box; /* Include padding and border in width */
                border-radius: 10px; /* Rounded corners */
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
                background-color: #fff; /* White background for cards */
                transition: transform 0.2s; /* Animation for hover effect */
                position: relative; /* For absolute positioning of button */
                cursor: pointer; /* Make the card clickable */
            }
            .menu-item-card:hover {
                transform: scale(1.02); /* Slightly enlarge on hover */
            }
            .image-container {
                position: relative; /* For positioning the button inside */
                text-align: center; /* Center the button */
            }
            img {
                width: 100%; /* Responsive image */
                height: auto; /* Maintain aspect ratio */
                border-radius: 10px; /* Round image corners */
            }
            
            .name-rating {
                display: flex; /* Use flexbox for horizontal alignment */
                justify-content: space-between; /* Space between name and rating */
                align-items: center; /* Center items vertically */
                margin: 10px 0; /* Margin for spacing */
            }
            .price-time {
                display: flex; /* Use flexbox to align price and time */
                justify-content: space-between; /* Space between price and time */
                margin: 5px 0; /* Margin for spacing */
                color: grey; /* Grey color for price and time */
            }
            /* Change color of all paragraphs to grey */
            .menu-item-card p {
                color: grey; /* Grey color for all paragraphs */
                margin: 5px 0; /* Margin for spacing */
            }
            .food-type-heading {
                width: 100%; /* Make the heading span the entire row */
                font-size: 1.5em; /* Increase font size */
                color: #333; /* Dark color for the heading */
                margin: 20px 0 10px 0; /* Spacing above and below the heading */
                font-weight: bold; /* Bold text for the heading */
            }
            .vegan-label, .non-vegan-label {
                text-align: center; /* Center the text */
                margin: 5px 0; /* Margin for spacing */
                font-weight: bold; /* Bold text */
            }
            .vegan-label {
                color: green; /* Green for vegan */
            }
            .non-vegan-label {
                color: red; /* Red for non-vegan */
            }



       
        </style>";
    
    echo "<div class='menu-container'>"; // Flex container
    
    // Display the heading for Seasonal Menu
    // echo "<div class='food-type-heading'>Seasonal Menu</div>";

    // Start displaying each row in a separate container
    while ($row = $result->fetch_assoc()) {
        // Escape data to prevent issues with special characters
        $itemId = urlencode($row["Id"]);
        $itemName = urlencode($row["name"]);
        $itemDescription = urlencode($row["description"]);
        $itemPrice = urlencode($row["price"]);
        $itemRating = urlencode($row["rating"]);
        $itemTime = urlencode($row["time"]);
        $itemVegan = urlencode($row["vegan"]);
        $itemImage = base64_encode($row["product_image"]);

        echo "<div class='menu-item-card' onclick=\"window.location='customer/detail.php?Id={$itemId}&name={$itemName}&description={$itemDescription}&price={$itemPrice}&rating={$itemRating}&time={$itemTime}&vegan={$itemVegan}'\">
                <div class='image-container'>";
        
        // Check if the product_image column contains data and display the image
        if ($row["product_image"]) {
            echo "<img src='data:image/jpeg;base64,$itemImage' alt='Product Image'/>";
        } else {
            echo "<p>No image available</p>";
        }
        
    
        // Display name and rating in the same row
        echo "<div class='name-rating'>
                <h3 style='margin: 0;'>" . htmlspecialchars($row["name"]) . "</h3>
                <p style='margin: 0;'>" . htmlspecialchars($row["rating"]) . " ⭐</p>
              </div>";
        
        // Display Price and Prep Time in the same row
        echo "<div class='price-time'>
                <p style='margin: 0;'>$" . htmlspecialchars($row["price"]) . "</p>
                <p style='margin: 0;'>" . htmlspecialchars($row["time"]) . " mins</p>
              </div>";
        
        // Vegan/Non-Vegan Indicator
        $veganStatus = $row["vegan"] ? "🌱 Vegan" : "🍗 Non-Vegan";
        $veganClass = $row["vegan"] ? "vegan-label" : "non-vegan-label";
        echo "<p class='$veganClass'>$veganStatus</p>"; // Center the indicator
        
        echo "</div>"; // Close card div
    }

    echo "</div>"; // Close flex container
} else {
    echo "0 results";
}

// Close connection
$conn->close();
?>
