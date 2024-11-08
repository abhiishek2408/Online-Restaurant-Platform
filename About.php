<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Bistrofy</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Resetting some basic styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #2C2C2C;
            color: #FFFFFF;
            line-height: 1.8;
        }

        .about-us {
            padding: 60px 20px;
            background-color: #2C2C2C;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .about-container {
            max-width: 800px;
            padding: 40px;
            background-color: #333333;
            border: 1px solid #444444;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            text-align: center;
            border-radius: 8px;
        }

        .about-container h1 {
            color: #FF7518;
            font-size: 2.8rem;
            margin-bottom: 25px;
        }
        .about-container h2 {
            color: #FF7518;
            font-size: 1.3rem;
            margin-bottom: 25px;
        }

        .about-container p {
            font-size: 1.1rem;
            margin-bottom: 20px;
            color: #DDDDDD;
            text-align: justify;
        }

        .about-intro {
            font-weight: 500;
            margin-bottom: 30px;
        }

        .about-highlight {
            color: #FF7518;
            font-weight: 700;
        }

        /* Responsive Styling */
        @media (min-width: 768px) {
            .about-container h1 {
                font-size: 3rem;
            }

            .about-container p {
                font-size: 1.2rem;
            }

            .about-container {
                padding: 60px;
            }
        }
    </style>
</head>
<body>
    <section class="about-us" id="about">
    <div class="about-container">
    <h1>Welcome to Bistrofy</h1>
    <p class="about-intro">At Bistrofy, we believe that great food is at the heart of every memorable moment. Located in the heart of the city, we serve up fresh, seasonal, and delicious dishes that bring people together.</p>
    <p>Our culinary team, inspired by global flavors and local ingredients, crafts every dish with passion and precision. Whether you're joining us for a casual meal or a special celebration, we strive to make each experience delightful and unforgettable.</p>
    <p class="about-highlight">Come experience the art of dining at Bistrofy. We can't wait to welcome you!</p>
    
    <!-- Vision Section -->
    <h2>Our Vision</h2>
    <p>At Bistrofy, our vision is to create a space where people can connect over exceptional food, fostering memories that last a lifetime. We aim to be the city's favorite gathering spot for food lovers and create an atmosphere of warmth and joy for all.</p>

    <!-- Creator's Name Section -->
    <p><strong>Created by:</strong> Abhishek Yadav</p>
</div>

    </section>
</body>
</html>
