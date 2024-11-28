<?php
include('../config/db.php');

// Fetch distinct FoodTypes from the menu_sections table
$foodTypeQuery = "SELECT DISTINCT FoodType FROM menu_sections";
$foodTypeResult = $conn->query($foodTypeQuery);

$foodTypes = [];
while ($row = $foodTypeResult->fetch_assoc()) {
    $foodTypes[] = $row['FoodType'];
}

// Return the food types as a JSON response
echo json_encode($foodTypes);
?>
