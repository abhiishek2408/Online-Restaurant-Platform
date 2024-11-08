<?php
session_start();
include('../config/db.php');

// Check if an ID is provided in the URL
if (!isset($_GET['Id'])) {
    echo "No menu item selected.";
    exit;
}

$menuId = $_GET['Id'];

// Fetch the current menu item data
$query = "SELECT * FROM menu_sections WHERE Id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $menuId);
$stmt->execute();
$result = $stmt->get_result();
$menuItem = $result->fetch_assoc();

if (!$menuItem) {
    echo "Menu item not found.";
    exit;
}

// Handle form submission for updating the menu item
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $foodType = $_POST['FoodType'];
    $foodCategory = $_POST['food_category'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $vegan = isset($_POST['vegan']) ? 1 : 0;
    $rating = $_POST['rating'];
    $time = $_POST['time'];
    $offers = $_POST['offers'];
    $quantity = $_POST['quantity'];

    // Prepare the update query to update all fields except the image
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        // If a new image is uploaded, process the image and save it as binary data
        $imageTmpName = $_FILES['product_image']['tmp_name'];
        $imageData = file_get_contents($imageTmpName); // Read the image contents

        // Update the database query with image update
        $updateQuery = "UPDATE menu_sections SET FoodType = ?, food_category = ?, name = ?, description = ?, price = ?, vegan = ?, rating = ?, time = ?, Offers = ?, quantity = ?, product_image = ? WHERE Id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ssssdiiisisi", $foodType, $foodCategory, $name, $description, $price, $vegan, $rating, $time, $offers, $quantity, $imageData, $menuId);
    } else {
        // If no new image is uploaded, update the record without changing the image
        $updateQuery = "UPDATE menu_sections SET FoodType = ?, food_category = ?, name = ?, description = ?, price = ?, vegan = ?, rating = ?, time = ?, Offers = ?, quantity = ? WHERE Id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ssssdiiisii", $foodType, $foodCategory, $name, $description, $price, $vegan, $rating, $time, $offers, $quantity, $menuId);
    }

    // Execute the query
    if ($updateStmt->execute()) {
        header("Location: ManageMenu.php?message=updated");
        exit;
    } else {
        echo "Error updating menu item: " . $conn->error;
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
    <title>Edit Menu Item</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
               body {
            font-family: 'Roboto', sans-serif;
            background-color: #2C2C2C;
            color: #FFFFFF;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            margin-top: 30%;
            background-color: #3D3D3D;
            padding: 50px;
            border-radius: 12px;
            width: 100%;
            max-width: 800px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .page-title {
            font-family: 'Roboto', sans-serif;
            font-size: 28px;
            font-weight: 500;
            color: #FF7518;
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        h2 {
            color: #FF7518;
            text-align: center;
            font-weight: 500;
            font-size: 24px;
            margin-bottom: 25px;
        }
        form label {
            font-weight: 500;
            margin-bottom: 6px;
            display: block;
            color: #FFA559;
        }
        form input, form textarea, form select {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border: none;
            border-radius: 8px;
            background-color: #2C2C2C;
            color: #FFFFFF;
            font-size: 15px;
            box-sizing: border-box;
        }

        form textarea {
            width: 100%;
            height: 43px; 
            resize: none; 
            padding: 10px; 
            box-sizing: border-box; 
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .form-row .form-group {
            flex: 1;
            min-width: 45%;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .current-image {
            display: block;
            margin: 10px 0;
            max-width: 100px;
        }
        button {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            color: #FFFFFF;
            background-color: #FF7518;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #e86e0e;
        }
        .back-button {
            margin-top: 20px;
            background-color: #606060;
        }
        .back-button:hover {
            background-color: #4a4a4a;
        }
        @media screen and (max-width: 768px) {
            .form-row {
                flex-direction: column;
            }
            .form-row .form-group {
                min-width: 100%;
            }
        }

        .form-group-inline {
            display: flex;
            align-items: center;
            gap: 10px; /* Adjusts spacing between label and checkbox */
            margin-bottom: 15px; /* Adds spacing below the row */
        }

        .form-group-inline label {
            margin: 0;
            font-weight: 500;
            color: #FFA559;
        }


        /* Keyframes for container entrance animation */
@keyframes containerFadeIn {
    0% {
        opacity: 0;
        transform: scale(0.9);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

/* Keyframes for button bounce effect */
@keyframes buttonBounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-5px);
    }
    60% {
        transform: translateY(-3px);
    }
}

/* Apply animation to container */
.container {
    margin-top: 30%;
    background-color: #3D3D3D;
    padding: 50px;
    border-radius: 12px;
    width: 100%;
    max-width: 800px;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.4);
    display: flex;
    flex-direction: column;
    gap: 25px;
    opacity: 0;
    animation: containerFadeIn 0.8s ease-out forwards; /* Animates on page load */
}

/* Apply bounce effect on button hover */
button {
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    color: #FFFFFF;
    background-color: #FF7518;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #e86e0e;
    animation: buttonBounce 0.5s; /* Bounce effect on hover */
}



    </style>
</head>
<body>
<div class="container">

    <h1 class="page-title">Update Menu Item</h1>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label for="FoodType">Food Type:</label>
                <input type="text" id="FoodType" name="FoodType" value="<?php echo htmlspecialchars($menuItem['FoodType']); ?>" required>
            </div>
            <div class="form-group">
                <label for="food_category">Category:</label>
                <input type="text" id="food_category" name="food_category" value="<?php echo htmlspecialchars($menuItem['food_category']); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($menuItem['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($menuItem['price']); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($menuItem['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="rating">Rating:</label>
                <input type="number" step="0.1" id="rating" name="rating" value="<?php echo htmlspecialchars($menuItem['rating']); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="time">Time (mins):</label>
                <input type="number" id="time" name="time" value="<?php echo htmlspecialchars($menuItem['time']); ?>" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($menuItem['quantity']); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="offers">Offers:</label>
            <input type="text" id="offers" name="offers" value="<?php echo htmlspecialchars($menuItem['Offers']); ?>">
        </div>

        <div class="form-group-inline">
    <label for="vegan">Vegan:</label>
    <input type="checkbox" id="vegan" name="vegan" <?php echo $menuItem['vegan'] ? 'checked' : ''; ?>>
</div>

        <div class="form-group">
            <label for="product_image">Product Image:</label>
            <input type="file" id="product_image" name="product_image" accept="image/*">
        </div>

        <button type="submit">Update Menu Item</button>
    </form>
    <button class="back-button" onclick="history.back()">Back to Previous</button>
</div>
</body>
</html>
