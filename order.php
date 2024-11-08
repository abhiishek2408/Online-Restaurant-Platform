<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        /* Location and Filter Section Styling */
        .location-filter-section {
            background-color: #fff;
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            flex-direction: column;
            gap: 10px;
            
        }

        .location-search {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .location-icon {
            font-size: 1.2em;
            color: #ff6347;
            /* Color similar to the location pin */
        }

        .location-text {
            margin-left: 8px;
            font-size: .7em;
            color: #333;
            flex: 1;

            /* Ensure the text does not wrap */
            white-space: nowrap;
            /* Prevents text from wrapping */
            overflow: hidden;
            /* Hides overflow text */
            text-overflow: ellipsis;
            /* Optional: Adds ellipsis (...) for overflowed text */
            cursor: pointer;
            /* Change cursor to pointer on hover */
            position: relative;
            /* Position for absolute dropdown */
        }




       

        .search-container {
            display: flex;
            align-items: center;
            position: relative;
            margin-left: 40%;

        }


        .search-box {
            width: 100%;
            max-width: 900px;
            border: 1px solid #ddd;
            border-radius: 25px;
            padding: 10px 20px;
            outline: none;
            transition: border-color 0.3s;
            font-size: 16px;
        }

        .search-box:focus {
            border-color: #0056b3;
        }

        .search-icon {
            margin-left: -30px;
            cursor: pointer;
            color: #0056b3;
            font-size: 20px;
            pointer-events: auto;
        }


        

        .filter-buttons {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            display: flex;
            /* Use flexbox for layout */
            overflow-x: auto;
            /* Enable horizontal scrolling */
            overflow-y: hidden;
            /* Prevent vertical overflow */
            white-space: nowrap;
            /* Prevent line breaks */
            padding: 10px;
            /* Optional padding */
            border: 1px solid #ccc;
            /* Optional border for visualization */
            scrollbar-width: none;
            /* For Firefox */
        }

        .filter-buttons::-webkit-scrollbar {
            display: none;
            /* For Chrome, Safari, and Edge */
        }



        .filter-btn {
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 0.9em;
            color: #333;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        .filter-btn:hover {
            background-color: #ff6347;
            color: #fff;
            border-color: #ff6347;
        }

        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            z-index: 1;
            /* Sits on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            /* Hide overflow to keep modal fixed size */
            background-color: rgba(0, 0, 0, 0.5);
            /* Black with transparency */
        }

        .modal-content {
            background-color: #fff;
            margin: auto;
            /* Center the modal */
            position: relative;
            padding: 20px;
            border: 1px solid #888;
            width: 800px;
            /* Fixed width */
            height: 600px;
            /* Fixed height */
            max-width: 800px;
            /* Maximum width */
            max-height: 600px;
            /* Maximum height */
            border-radius: 8px;
            display: flex;
            /* Make it a flex container */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Modal close button */
        .close {
            position: absolute;
            top: 10px;
            right: 20px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        /* Sidebar styles */
        .sidebar {
            width: 200px;
            border-right: 1px solid #ddd;
            padding: 10px;
            overflow-y: auto;
            /* Scroll if content is too large */
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 10px 5px;
            cursor: pointer;
            color: #333;
        }

        .sidebar ul li.active {
            color: #ff6b6b;
            font-weight: bold;
        }

        .sidebar ul li:hover {
            background-color: #f0f0f0;
        }

        /* Content area styles */
        .filter-options-container {
            flex: 1;
            /* Take up the remaining space */
            padding: 10px;
            overflow-y: auto;
            /* Scroll if content is too large */
        }

        .filter-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .filter-options input {
            margin-right: 10px;
        }

        .apply-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            position: absolute;
            bottom: 20px;
            /* Position buttons at the bottom of the modal */
            right: 25%;

            gap: 10px;
        }


        .clear-button {

            background-color: #f1f1f1;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .apply-button {
            background-color: #ff6b6b;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            
        }
    </style>
</head>

<body>


    <main style="background-color: #ccc;">

        <section class="location-filter-section">
            <h2>Explore Our Menu</h2>
            <div class="location-search">
                <span class="location-icon">📍</span> 
                <span class="location-text" id="locationText">
                    <span class="dropdown"></span> 
                </span>



                <!-- <div class="search-container">
                    <input type="text" class="search-box" placeholder="Search..." id="searchInput">
                    <span class="search-icon" id="searchButton">🔍</span>
                    <div class="message-box" id="messageBox"></div>
                </div> -->

            </div>

            <div class="filter-buttons">
                

                <button class="filter-btn" id="filter-menu-btn" onclick="openModal()">Filters</button>
                <button class="filter-btn" id="Seasonal-Menu-btn">Seasonal Menu</button>
                <button class="filter-btn" id="Special-Offers-Menu-btn">Offers</button>
                <button class="filter-btn" id="alaCarte-Menu-btn">A La Carte Menu</button>
                <button class="filter-btn" id="Prix-Fixe-Menu-btn">Prix Fixe Menu</button>
                <button class="filter-btn" id="Tasting-Menu-btn">Tasting Menu</button>
                <button class="filter-btn" id="Buffet-Menu-btn">Buffet Menu</button>
                <button class="filter-btn" id="Specialty-Menu-btn">Specialty Menu</button>
                <button class="filter-btn" id="Brunch-or-Happy-Hour-Menu-btn">Brunch</button>
                <button class="filter-btn" id="EventCatering-Menu-btn">Chef's Specials</button>
                <button class="filter-btn" id="Dietary-Specific-Menu-btn">Dietary-Specific Menu</button>
                <button class="filter-btn" id="Event-or-Catering-Menu-btn">Event or Catering Menu</button>
            </div>

        </section>

        <section>
            <div id="contentnew-container"></div>
        </section>

        <div id="filterModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <div class="sidebar">
                    <ul>
                        <li onclick="showFilterOptions('cuisines')">Cuisines</li>
                    </ul>
                </div>

        <div class="filter-options-container">
            <div id="cuisines" class="filter-options" style="display: none;">
                <h3>Cuisines</h3>
            <form id="pageForm" method="post">
                    <label><input type="checkbox" value="customer/Appetizers.php" name="page">Appetizers</label></br>
                    <label><input type="checkbox" value="customer/DrinksMenu.php">Drinks Menu</label></br>
                    <label><input type="checkbox" value="customer/Pasta.php">Pasta</label></br>
                    <label><input type="checkbox" value="customer/RiceGrainBasedDishes.php">Rice and Grain-Based Dishes</label></br>
                    <label><input type="checkbox" value="customer/MeatDishes.php">Meat Dishes</label></br>
                    <label><input type="checkbox" value="customer/SeafoodDishes.php">Seafood Dishes</label></br>
                    <label><input type="checkbox" value="customer/CurryDishes.php">Curry Dishes</label></br>
                    <label><input type="checkbox" value="customer/PizzaFlatbreads.php">Pizza and Flatbreads</label></br>
                    <label><input type="checkbox" value="customer/BurgersSandwiches.php">Burgers and Sandwiches</label></br>
                    <label><input type="checkbox" value="customer/GrilledBarbecueDishes.php">Grilled and Barbecue Dishes</label></br>
                    <label><input type="checkbox" value="customer/AsianDishes.php">Asian Dishes</label></br>
                    <label><input type="checkbox" value="customer/VegetarianVeganDishes.php">Vegetarian and Vegan Dishes</label></br>
                    <label><input type="checkbox" value="customer/MexicanDishes.php">Mexican Dishes</label></br>

                <div class="apply-buttons">
                    <button type="button" class="clear-button" onclick="clearFilters()">Clear all</button>
                    <button type="submit" class="apply-button" id="submitBtn">Apply</button>
                </div>
            </form>
            </div>
            </div>

</main>

</body>
<script>
    //fetch location
    function fetchLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    // Use a reverse geocoding service to get the location name
                    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`)
                        .then(response => response.json())
                        .then(data => {
                            const locationName = data.display_name;
                            document.getElementById('locationText').textContent = locationName; // Update location text
                        })
                        .catch(error => {
                            console.error("Error fetching location:", error);
                        });
                },
                function() {
                    alert("Unable to retrieve your location.");
                }
            );
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }
    window.onload = fetchLocation;


    $(document).ready(function() {
        $("#contentnew-container").load("customer/seasonalmenu.php");

        $('#Seasonal-Menu-btn').click(function() {
            $("#contentnew-container").load("customer/seasonalmenu.php");
        });

        $('#Special-Offers-Menu-btn').click(function() {
            $("#contentnew-container").load("customer/Offers.php");
        });
        $('#alaCarte-Menu-btn').click(function() {
            $("#contentnew-container").load("customer/alacarte.php");
        });

        $("#Prix-Fixe-Menu-btn").click(function() {
            $("#contentnew-container").load("customer/PrixFixeMenu.php");
        });

        $("#Tasting-Menu-btn").click(function() {
            $("#contentnew-container").load("customer/TastingMenu.php");
        });

        $("#Buffet-Menu-btn").click(function() {
            $("#contentnew-container").load("customer/BuffetMenu.php");
        });

        $("#Specialty-Menu-btn").click(function() {
            $("#contentnew-container").load("customer/SpecialtyMenu.php");
        });

        $("#Brunch-or-Happy-Hour-Menu-btn").click(function() {
            $("#contentnew-container").load("customer/BrunchMenu.php");
        });

        $("#Chef-Specials-or-Daily-Specials-Menu-btn").click(function() {
            $("#contentnew-container").load("ChefSpecialMenu.php");
        });

        $("#Dietary-Specific-Menu-btn").click(function() {
            $("#contentnew-container").load("customer/DietarySpecificMenu.php");
        });
        $("#EventCatering-Menu-btn").click(function() {
            $("#contentnew-container").load("customer/EventCateringMenu.php");
        });
        $("#main-course-food").load("menuall4.php");
    });




    function openModal() {
        document.getElementById('filterModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('filterModal').style.display = 'none';
    }


    function showFilterOptions(filterType) {
        $('.filter-options').hide();
        $('#' + filterType).show();
        $('.sidebar ul li').removeClass('active');
        $('.sidebar ul li[onclick="showFilterOptions(\'' + filterType + '\')"]').addClass('active');
    }


    
    function clearFilters() {
        const checkboxes = document.querySelectorAll('.filter-options input[type="checkbox"]');
        checkboxes.forEach(checkbox => checkbox.checked = false);
    }

   
    // function applyFilters() {
    //     $(document).ready(function() {

    //         $('#Seasonal-Menu-btn').click(function() {
    //             $("#contentnew-container").load("customer/seasonalmenu.php");
    //         });

    //         $('#Special-Offers-Menu-btn').click(function() {
    //             $("#contentnew-container").load("customer/Offers.php");
    //         });
    //         $('#alaCarte-Menu-btn').click(function() {
    //             $("#contentnew-container").load("customer/alacarte.php");
    //         });

    //         $("#Prix-Fixe-Menu-btn").click(function() {
    //             $("#contentnew-container").load("customer/PrixFixeMenu.php");
    //         });

    //         $("#Tasting-Menu-btn").click(function() {
    //             $("#contentnew-container").load("customer/TastingMenu.php");
    //         });

    //         $("#Buffet-Menu-btn").click(function() {
    //             $("#contentnew-container").load("customer/BuffetMenu.php");
    //         });

    //         $("#Specialty-Menu-btn").click(function() {
    //             $("#contentnew-container").load("customer/SpecialtyMenu.php");
    //         });

    //         $("#Brunch-or-Happy-Hour-Menu-btn").click(function() {
    //             $("#contentnew-container").load("customer/BrunchMenu.php");
    //         });

    //         $("#Chef-Specials-or-Daily-Specials-Menu-btn").click(function() {
    //             $("#contentnew-container").load("ChefSpecialMenu.php");
    //         });

    //         $("#Dietary-Specific-Menu-btn").click(function() {
    //             $("#contentnew-container").load("customer/DietarySpecificMenu.php");
    //         });
    //         $("#EventCatering-Menu-btn").click(function() {
    //             $("#contentnew-container").load("customer/EventCateringMenu.php");
    //         });

    //         $("#main-course-food").load("menuall4.php");
    //     });
    //     closeModal(); 
    // }


    window.onclick = function(event) {
        const modal = document.getElementById('filterModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }


    $(document).ready(function() {
        $('input[type="checkbox"]').on('change', function() {
            $('input[type="checkbox"]').not(this).prop('checked', false); // Uncheck other checkboxes
        });

        $('#pageForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission
            $("#filterModal").hide();
            var selectedPage = $('input[type="checkbox"]:checked').val(); // Get selected checkbox value (URL)

            if (!selectedPage) {
                alert("Please select a menu category to load.");
            } else {
                // Perform AJAX request to load the selected menu content
                $.ajax({
                    url: selectedPage, // URL is the value of the checked checkbox
                    type: 'POST',
                    success: function(response) {
                        // Load the content from the PHP page into the container
                        $('#contentnew-container').html(response);
                    },
                    error: function() {
                        alert('Error loading the page.');
                    }
                });
            }
        });

        window.clearFilters = function() {
            $('input[type="checkbox"]').prop('checked', false); // Uncheck all checkboxes
            $('#contentnew-container').html('<h2>Selected menu content will appear here</h2>'); // Reset the container content
        };
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</html>