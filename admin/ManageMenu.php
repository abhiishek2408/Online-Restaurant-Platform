<?php
session_start();
include('../config/db.php');

// Initialize session storage for undo/redo
if (!isset($_SESSION['deleted_items'])) {
    $_SESSION['deleted_items'] = [];
    $_SESSION['redo_stack'] = [];
}

// Delete functionality
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $deleteQuery = "SELECT * FROM menu_sections WHERE Id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Store the item details before deletion
    if ($item = $result->fetch_assoc()) {
        $_SESSION['deleted_items'][] = $item;  // Add to deleted items stack
        $_SESSION['redo_stack'] = []; // Clear redo stack on new deletion
        $deleteQuery = "DELETE FROM menu_sections WHERE Id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $deleteId);
        $deleteStmt->execute();
        echo "<script>alert('Item deleted successfully.'); window.location.href='ManageMenu.php';</script>";
    }
}

// Undo functionality
if (isset($_GET['undo']) && !empty($_SESSION['deleted_items'])) {
    $itemToRestore = array_pop($_SESSION['deleted_items']); // Retrieve last deleted item
    $_SESSION['redo_stack'][] = $itemToRestore; // Save in redo stack for potential re-deletion

    // Restore the item in the database
    $restoreQuery = "INSERT INTO menu_sections (Id, FoodType, food_category, name, description, price, vegan, rating, time, product_image, Offers, quantity) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $restoreStmt = $conn->prepare($restoreQuery);
    $restoreStmt->bind_param(
        "issssdisbssi",
        $itemToRestore['Id'],
        $itemToRestore['FoodType'],
        $itemToRestore['food_category'],
        $itemToRestore['name'],
        $itemToRestore['description'],
        $itemToRestore['price'],
        $itemToRestore['vegan'],
        $itemToRestore['rating'],
        $itemToRestore['time'],
        $itemToRestore['product_image'],
        $itemToRestore['Offers'],
        $itemToRestore['quantity']
    );
    $restoreStmt->execute();
    echo "<script>alert('Undo successful. Item restored.'); window.location.href='ManageMenu.php';</script>";
}

// Redo functionality
if (isset($_GET['redo']) && !empty($_SESSION['redo_stack'])) {
    $itemToDeleteAgain = array_pop($_SESSION['redo_stack']); // Retrieve last restored item from redo stack
    $_SESSION['deleted_items'][] = $itemToDeleteAgain; // Save to deleted items stack for potential undo

    // Delete the item again
    $redoDeleteQuery = "DELETE FROM menu_sections WHERE Id = ?";
    $redoStmt = $conn->prepare($redoDeleteQuery);
    $redoStmt->bind_param("i", $itemToDeleteAgain['Id']);
    $redoStmt->execute();
    echo "<script>alert('Redo successful. Item deleted again.'); window.location.href='ManageMenu.php';</script>";
}


// Get selected FoodType from dropdown
$selectedFoodType = isset($_GET['FoodType']) ? $_GET['FoodType'] : '';

// Fetch all menu items (for the complete list)
$allItemsQuery = "SELECT Id, FoodType, food_category, name, description, price, vegan, rating, time, product_image, Offers, quantity FROM menu_sections";
$allItemsResult = $conn->query($allItemsQuery);

// Fetch distinct food types for the dropdown
$foodTypeQuery = "SELECT DISTINCT FoodType FROM menu_sections";
$foodTypeResult = $conn->query($foodTypeQuery);

// Fetch filtered menu items if a specific FoodType is selected
$filteredItemsQuery = "SELECT Id, FoodType, food_category, name, description, price, vegan, rating, time, product_image, Offers, quantity FROM menu_sections WHERE FoodType = ?";
$filteredStmt = $conn->prepare($filteredItemsQuery);
$filteredItems = [];
if ($selectedFoodType) {
    $filteredStmt->bind_param("s", $selectedFoodType);
    $filteredStmt->execute();
    $filteredItems = $filteredStmt->get_result();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #2C2C2C;
            color: #FFFFFF;
            margin: 0;
            padding: 0;
        }

        h2,
        h3 {
            color: #FF7518;
            text-align: center;
            margin-top: 20px;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .filter-section {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.filter-section select {
    padding: 8px 12px;
    font-size: 16px;
    border: none;
    border-radius: 12px; /* Increased radius for a more rounded look */
    background-color: #FF7518;
    color: #FFFFFF;
    transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
    outline: none;
    cursor: pointer;
}

/* Hover effect for select */
.filter-section select:hover {
    background-color: #e86e0e; /* Slightly darker color on hover */
    transform: scale(1.02); /* Small scaling effect */
    box-shadow: 0px 4px 10px rgba(255, 117, 24, 0.5); /* Subtle glow effect */
}

/* Focus effect for select */
.filter-section select:focus {
    box-shadow: 0px 0px 8px rgba(255, 117, 24, 0.7); /* Bright glow on focus */
}


        .table {
            width: 100%;
            border-collapse: collapse;
            background-color: #3D3D3D;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 40px;
            animation: containerFadeIn 0.8s ease-out forwards;
            

            /* Animates on page load */
        }

        .table thead {
            background-color: #FF7518;
          
        }

        .table th,
        .table td {
            width: 100%;
            padding: 15px;
            text-align: center;
        }

        .table th {
            color: #FFFFFF;
            font-weight: bold;
        }


        .table td {
            border-bottom: 1px solid #2C2C2C;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }


        .table td:hover {
            background-color: #3D3D3D;
            transform: scale(1.02);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }


        .table img {
            width: 50px;
            height: 50px;
            border-radius: 5px;
        }

        .action-btns button {
            padding: 8px 12px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .edit-btn {
            background-color: #FF7518;
        }

        .edit-btn:hover {
            background-color: #e86e0e;
        }

        .delete-btn {
            background-color: #f44336;
        }

        .delete-btn:hover {
            background-color: #d32f2f;
        }


        .action-buttons {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .undo-btn,
        .redo-btn {
            padding: 10px 15px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            margin-left: 10%;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .undo-btn {
            background-color: #4CAF50;
            /* Green for undo */
            color: white;
        }

        .undo-btn:hover {
            background-color: #45a049;
        }

        .redo-btn {
            background-color: #FF7518;
            /* Orange for redo */
            color: white;
            margin-left: 10%;
        }

        .redo-btn:hover {
            background-color: #e86e0e;
        }


        /* Keyframes for button hover animation */
        @keyframes buttonHoverEffect {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        /* General button style */
        .action-btns button,
        .undo-btn,
        .redo-btn {
            transition: background-color 0.3s, transform 0.3s ease-out;
        }

        /* Apply animation to all hover states */
        .action-btns button:hover,
        .undo-btn:hover,
        .redo-btn:hover {
            animation: buttonHoverEffect 0.3s ease-out;
            transform: scale(1.05);
            /* Slight scaling effect */
        }

        /* Individual button hover color adjustments */
        .edit-btn:hover {
            background-color: #e86e0e;
        }

        .delete-btn:hover {
            background-color: #d32f2f;
        }

        .undo-btn:hover {
            background-color: #45a049;
        }

        .redo-btn:hover {
            background-color: #e86e0e;
        }




        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #3D3D3D;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 400px;
            margin: auto;
        }

        .close {
            color: #FFFFFF;
            float: right;
            font-size: 24px;
            cursor: pointer;
        }

        input,
        button {
            width: 100%;
            margin-top: 10px;
            padding: 10px;
            font-size: 16px;
        }

        button {
            background-color: #FF7518;
            border: none;
            color: white;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #e86e0e;
        }
        .back-button {
            background-color: white;
            color: #FF7518;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            position: fixed;
            bottom: 20px; /* Positioned at the bottom */
            left: 20px; /* Positioned on the left side */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            opacity: 0.95;
            z-index: 1;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease, background-color 0.2s ease;
            width: auto;
        }

        /* Hover effect for subtle animation */
        .back-button:hover {
            background-color: #e06a15;
            color: #FFFFFF;
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
            opacity: 1;
        }

        /* Optional button text fade-in animation */
        .back-button::after {
            content: 'Back to Previous';
            opacity: 1;
        }

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

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-5px);
            }

            60% {
                transform: translateY(-3px);
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Menu Management</h2>

        <!-- Filter Dropdown -->
        <div class="filter-section">
            <form method="GET" action="ManageMenu.php"> <!-- Ensure action is menu.php -->
                <label for="FoodType">Filter by Food Type: </label>
                <select name="FoodType" id="FoodType" onchange="this.form.submit()">
                    <option value="">All</option>
                    <?php while ($foodType = $foodTypeResult->fetch_assoc()) : ?>
                        <option value="<?php echo $foodType['FoodType']; ?>" <?php echo $selectedFoodType == $foodType['FoodType'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($foodType['FoodType']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>
    </div>

    <div class="action-buttons">
        <?php if (!empty($_SESSION['deleted_items'])) : ?>
            <a href="ManageMenu.php?undo=true" onclick="return confirm('Undo the last delete action?');">
                <button class="undo-btn">Undo Delete</button>
            </a>
        <?php endif; ?>

        <?php if (!empty($_SESSION['redo_stack'])) : ?>
            <a href="ManageMenu.php?redo=true" onclick="return confirm('Redo the last delete action?');">
                Redo Prev Deleted item:<button class="redo-btn">Redo Delete</button>

            </a>
        <?php endif; ?>
    </div>

   <button class="back-button" onclick="history.back()">Back to Previous</button> 



    <!-- Display Filtered Items (if any) -->
    <?php if ($selectedFoodType): ?>
        <h3>Filtered Results for "<?php echo htmlspecialchars($selectedFoodType); ?>"</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Food Type</th>
                    <th>Category</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Vegan</th>
                    <th>Rating</th>
                    <th>Time</th>
                    <th>Image</th>
                    <th>Offers</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $filteredItems->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $row['Id']; ?></td>
                        <td><?php echo htmlspecialchars($row['FoodType']); ?></td>
                        <td><?php echo htmlspecialchars($row['food_category']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>$<?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo $row['vegan'] ? 'Yes' : 'No'; ?></td>
                        <td><?php echo $row['rating']; ?>/5</td>
                        <td><?php echo htmlspecialchars($row['time']); ?> mins</td>
                        <td>
                            <?php if ($row['product_image']): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($row['product_image']); ?>" alt="Product Image">
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['Offers']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td class="action-btns">
                            <a href="update_menu.php?Id=<?php echo $row['Id']; ?>">
                                <button class="edit-btn">Edit</button>
                            </a>
                            <a href="ManageMenu.php?delete=<?php echo $row['Id']; ?>" onclick="return confirm('Are you sure you want to delete this item?');">
                                <button class="delete-btn">Delete</button>
                            </a>

                        </td>
                        <td>
                            <div class="action-buttons">

                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Display All Items -->
    <h3>All Menu Items</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Food Type</th>
                <th>Category</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Vegan</th>
                <th>Rating</th>
                <th>Time</th>
                <th>Image</th>
                <th>Offers</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $allItemsResult->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row['Id']; ?></td>
                    <td><?php echo htmlspecialchars($row['FoodType']); ?></td>
                    <td><?php echo htmlspecialchars($row['food_category']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo $row['vegan'] ? 'Yes' : 'No'; ?></td>
                    <td><?php echo $row['rating']; ?>/5</td>
                    <td><?php echo htmlspecialchars($row['time']); ?> mins</td>
                    <td>
                        <?php if ($row['product_image']): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($row['product_image']); ?>" alt="Product Image">
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['Offers']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td class="action-btns">
                        <a href="update_menu.php?Id=<?php echo $row['Id']; ?>">
                            <button class="edit-btn">Edit</button>
                        </a>
                        <a href="ManageMenu.php?delete=<?php echo $row['Id']; ?>" onclick="return confirm('Are you sure you want to delete this item?');">
                            <button class="delete-btn">Delete</button>
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>







</body>

</html>
<?php
$filteredStmt->close();
$conn->close();
?>