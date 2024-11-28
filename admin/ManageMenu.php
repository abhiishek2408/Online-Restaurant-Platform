<?php
session_start();
include('../config/db.php');

// Filter logic for AJAX (Only process filter request when the 'FoodType' is set)
if (isset($_GET['FoodType'])) {
    $selectedFoodType = $_GET['FoodType'];
    $filteredItems = [];

    if ($selectedFoodType) {
        $filteredItemsQuery = "SELECT Id, FoodType, food_category, name, description, price, vegan, rating, time, product_image, Offers, quantity FROM menu_sections WHERE FoodType = ?";
        $filteredStmt = $conn->prepare($filteredItemsQuery);
        $filteredStmt->bind_param("s", $selectedFoodType);
        $filteredStmt->execute();
        $filteredItems = $filteredStmt->get_result();
    }

    // Output filtered results as table rows
    if ($filteredItems->num_rows > 0) {
        while ($row = $filteredItems->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['Id']}</td>
                    <td>" . htmlspecialchars($row['FoodType']) . "</td>
                    <td>" . htmlspecialchars($row['food_category']) . "</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . htmlspecialchars($row['description']) . "</td>
                    <td>$" . number_format($row['price'], 2) . "</td>
                    <td>" . ($row['vegan'] ? 'Yes' : 'No') . "</td>
                    <td>{$row['rating']}/5</td>
                    <td>" . htmlspecialchars($row['time']) . " mins</td>
                    <td>" . ($row['product_image'] ? '<img src="data:image/jpeg;base64,' . base64_encode($row['product_image']) . '" alt="Product Image" class="product-img">' : 'N/A') . "</td>
                    <td>" . htmlspecialchars($row['Offers']) . "</td>
                    <td>" . htmlspecialchars($row['quantity']) . "</td>
                    <td class='action-btns'>
                        <a href='update_menu.php?Id={$row['Id']}'>
                            <button class='edit-btn'>Edit</button>
                        </a>
                        <a href='ManageMenu.php?delete={$row['Id']}' onclick='return confirm(\"Are you sure you want to delete this item?\");'>
                            <button class='delete-btn'>Delete</button>
                        </a>
                    </td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='13'>No items found for this filter.</td></tr>";
    }
    exit;
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
        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        /* Image Styling */
        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }

        /* Modal Styles */
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
            font-size: 24px;
            cursor: pointer;
        }

        /* Loading Spinner */
        .loader {
            border: 4px solid #f3f3f3; /* Light grey */
            border-top: 4px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 2s linear infinite;
            margin: 10px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Styles for buttons */
        .filter-btn {
            background-color: #FF7518;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }

        .filter-btn:hover {
            background-color: #e86e0e;
        }

        .action-btns button {
            padding: 5px 10px;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-btn {
            background-color: #4CAF50;
            color: white;
        }

        .delete-btn {
            background-color: #f44336;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Menu Management</h2>

        <!-- Filter Button to open Modal -->
        <button class="filter-btn" id="openFilterModal">Filter Menu</button>

        <!-- Modal for Filter -->
        <div class="modal" id="filterModal">
            <div class="modal-content">
                <span class="close" id="closeModal">&times;</span>
                <h3>Select Filter</h3>
                <form id="filterForm">
                    <label for="FoodType">Select Food Type:</label>
                    <select name="FoodType" id="FoodType">
                        <option value="">All</option>
                        <!-- Food type options will be loaded dynamically here -->
                    </select>
                    <button type="button" id="applyFilter" style="margin-top: 10px; background-color: #FF7518; padding: 8px 16px; color: white; border: none;">Apply Filter</button>
                </form>
            </div>
        </div>

        <!-- Table for Filtered Results -->
        <table id="filteredResults">
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
                <!-- Filtered menu items will be inserted here dynamically -->
            </tbody>
        </table>

        <!-- Loading Spinner -->
        <div id="loader" class="loader" style="display: none;"></div>

    </div>

    <script>
        // Open Modal
        document.getElementById('openFilterModal').addEventListener('click', function () {
            document.getElementById('filterModal').style.display = 'flex';
            
            // Fetch food types dynamically when the filter modal opens
            fetch('getFoodTypes.php') // This script should return the distinct food types
                .then(response => response.json())
                .then(data => {
                    const foodTypeSelect = document.getElementById('FoodType');
                    data.forEach(foodType => {
                        const option = document.createElement('option');
                        option.value = foodType;
                        option.textContent = foodType;
                        foodTypeSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching food types:', error));
        });

        // Close Modal
        document.getElementById('closeModal').addEventListener('click', function () {
            document.getElementById('filterModal').style.display = 'none';
        });

        // Handle Apply Filter Button Click
        document.getElementById('applyFilter').addEventListener('click', function () {
            const foodType = document.getElementById('FoodType').value;

            // Show loader spinner while fetching data
            document.getElementById('loader').style.display = 'block';

            // Send AJAX request to get filtered results
            fetch('ManageMenu.php?FoodType=' + foodType)
                .then(response => response.text())
                .then(data => {
                    // Insert filtered data into the table
                    document.getElementById('filteredResults').getElementsByTagName('tbody')[0].innerHTML = data;

                    // Hide loader spinner after the data is loaded
                    document.getElementById('loader').style.display = 'none';

                    // Close the modal
                    document.getElementById('filterModal').style.display = 'none';
                })
                .catch(error => {
                    console.error('Error fetching filtered data:', error);

                    // Hide loader spinner in case of error
                    document.getElementById('loader').style.display = 'none';
                });
        });
    </script>
</body>

</html>
