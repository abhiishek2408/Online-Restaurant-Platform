<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Document</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        /* Basic CSS Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #333;
            transition: background-color 0.3s, color 0.3s;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            background-color: #2C2C2C;
            padding: 10px;
            overflow: visible;
            /* Ensures dropdowns are visible */
            z-index: 10;
        }

        nav h1 {
            margin-right: 50%;
            font-size: 24px;
            color: #FF7518;
            font-family: 'Playfair Display', serif;
            font-weight: bold;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            line-height: 1.5;
            transition: color 0.3s ease;
        }

        .nav-links {
            display: flex;
            align-items: center;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
        }

        nav ul li {
            display: inline;
            margin-right: 15px;
            position: relative;
            /* Ensures dropdown is positioned relative to this */
        }

        nav ul li a {
            color: white;
            font-family: 'Roboto', sans-serif;
            text-decoration: none;
            padding: 8px;
            transition: background-color 0.3s;
            cursor: pointer;
        }

        nav ul li a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .dropdown {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            /* Ensures dropdown appears above everything */
            border-radius: 5px;
            margin-top: 5px;
            right: 0;
        }

        .dropdown a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }

        .dropdown a:hover {
            background-color: #f0f0f0;
        }

        .profile-container {
            position: relative;
            display: inline-block;
        }

        .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid #FF7518;
        }

        .show {
            display: block;
        }

        @media (max-width: 1023px) {
            nav h1 {
                font-size: 30px;
                text-align: left;
                margin-right: auto;
            }

            nav ul {
                flex-direction: column;
                align-items: flex-start;
                display: none;
                position: absolute;
                background-color: #0056b3;
                width: 100%;
                left: 0;
                top: 50px;
                z-index: 2;
            }

            nav.active ul {
                display: flex;
            }

            .hamburger {
                display: flex;
                flex-direction: column;
                cursor: pointer;
            }

            .hamburger div {
                width: 25px;
                height: 3px;
                background-color: white;
                margin: 3px 0;
                transition: 0.4s;
            }
        }


        .icon {
            background-color: #e3f8e0;
            color: #388e3c;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.8em;
        }



        .btn-cart {
            width: 20px;
            height: 20px;
            top: -15px;
            border-radius: 10px;
            border: none;
            background-color: #2c2c2c;
            /* position: relative; */
        }

        .btn-cart::after {
            content: attr(data-quantity);
            width: 13px;
            height: 13px;
            position: absolute;
            top: -7px;
            right: -8px;
            background-color: rgb(255, 153, 0);
            color: white;
            font-size: 12px;
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 1;
            visibility: visible;
            transition: .2s linear;
        }

        .icon-cart {
            width: 16.38px;
            height: 20.52px;
            transition: .2s linear;
            fill: white;
        }

        .icon-cart path {
            fill: white;
            transition: .2s linear;
        }

        .btn-cart:hover>.icon-cart {
            transform: scale(1.2);
        }

        .btn-cart:hover>.icon-cart path {
            fill: rgb(239, 107, 12);
        }

        .btn-cart:hover::after {
            visibility: visible;
            opacity: 1;
        }

        .quantity {
            display: none;
        }
    </style>
</head>

<body>
    <nav>
        <div class="hamburger" id="hamburgerMenu">
            <div></div>
            <div></div>
            <div></div>
        </div>

        <h1>Bistrofy</h1>

        <div class="nav-links">
            <ul>
                <li><a href="index.php?page=home">Home</a></li>
                <li><a href="order.php">Shop</a></li>
                <li><a href="TableBooking.php">Book</a></li>
                <li><a href="About.php">About</a></li>
                <li>
                    <a id="loginButton">Login</a>
                    <div class="dropdown" id="loginDropdown">
                        <a href="login.php">Sign In</a>
                        <a href="register.php">Register</a>
                        <?php if (isset($_SESSION['user'])): ?>
                            <a href="logout.php">Logout</a>
                        <?php endif; ?>
                    </div>
                </li>
                <li><a href="add_to_cart.php">
                <span className="cart-icon" id="cart-icon">

                    <svg
                        className="icon-cart"
                        viewBox="0 0 24.38 30.52"
                        height="20.52"
                        width="24.38"
                        xmlns="http://www.w3.org/2000/svg">
                        <title>icon-cart</title>
                        <path
                            transform="translate(-3.62 -0.85)"
                            d="M28,27.3,26.24,7.51a.75.75,0,0,0-.76-.69h-3.7a6,6,0,0,0-12,0H6.13a.76.76,0,0,0-.76.69L3.62,27.3v.07a4.29,4.29,0,0,0,4.52,4H23.48a4.29,4.29,0,0,0,4.52-4ZM15.81,2.37a4.47,4.47,0,0,1,4.46,4.45H11.35a4.47,4.47,0,0,1,4.46-4.45Zm7.67,27.48H8.13a2.79,2.79,0,0,1-3-2.45L6.83,8.34h3V11a.76.76,0,0,0,1.52,0V8.34h8.92V11a.76.76,0,0,0,1.52,0V8.34h3L26.48,27.4a2.79,2.79,0,0,1-3,2.44Zm0,0"
                            fill="white"></path>
                    </svg>
                    <span className="quantity"></span>
                </span>
                </a></li>

            </ul>
        </div>

        <div class="profile-container" id="profile-content">
            <img src="https://cdn.vectorstock.com/i/500p/96/75/gray-scale-male-character-profile-picture-vector-51589675.jpg" alt="Profile Picture" class="profile-picture">
            <div class="dropdown" id="profileDropdown">
                <a href="Profile.php" id="profile-page">View Profile</a>
                <a href="#">Order History</a>
                <a href="#">My Reservations</a>
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="logout.php">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <script>
        // Dark mode toggle functionality
        const darkModeToggle = document.querySelector('.dark-mode-toggle');
        darkModeToggle?.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            document.querySelector('nav').classList.toggle('dark-mode');
        });

        // Toggle dropdown for profile picture
        const profilePicture = document.querySelector('.profile-picture');
        const profileDropdown = document.querySelector('#profileDropdown');

        profilePicture.addEventListener('click', (event) => {
            profileDropdown.style.display = profileDropdown.style.display === 'block' ? 'none' : 'block';
            event.stopPropagation(); // Prevent click from propagating to document
        });

        // Hide dropdown when clicking outside of it
        document.addEventListener('click', (event) => {
            if (!profileDropdown.contains(event.target) && !profilePicture.contains(event.target)) {
                profileDropdown.style.display = 'none';
            }
        });

        // Toggle dropdown for login
        const loginButton = document.querySelector('#loginButton');
        const loginDropdown = document.querySelector('#loginDropdown');

        loginButton.addEventListener('click', (event) => {
            loginDropdown.style.display = loginDropdown.style.display === 'block' ? 'none' : 'block';
            event.stopPropagation();
        });

        // Hide login dropdown when clicking outside
        document.addEventListener('click', (event) => {
            if (!loginDropdown.contains(event.target) && !loginButton.contains(event.target)) {
                loginDropdown.style.display = 'none';
            }
        });
    </script>
</body>

</html>