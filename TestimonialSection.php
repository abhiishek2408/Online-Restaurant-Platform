<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
         body {
                font-family: 'Roboto', sans-serif;
                margin: 0;
                padding: 0;
                background-color: #fff; 
                color: #000; 
                transition: background-color 0.3s, color 0.3s;
            }

        .testimonial-section {
            width: 100%;
            max-width: 1400px;
            padding: 2rem;
            color: #000;
            background-color: #fff; 
            margin: auto;
            font-family: 'Roboto', sans-serif;
            text-align: center;
        }

        .testimonial-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #444444;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }

        .testimonial-header h2 {
            font-size: 2rem;
            font-weight: bold;
            color: #FF7518;
        }

        .icons {
            display: flex;
            gap: 1rem;
        }

        .icon {
            font-size: 1.5rem;
            color: #FF7518; 
        }

        .testimonials {
            display: flex;
            justify-content: center; /* Adjust alignment */
            gap: 1rem;
            flex-wrap: nowrap; /* Force all testimonials in a single row */
            color: #000;
            overflow-x: auto; /* Enable scrolling if content exceeds container width */
        }

        .testimonial {
            background-color: #fff;
            padding: 1.5rem;
            width: 20%;
            color: #000;
            border: 1px solid #444444;
            border-radius: 8px;
            opacity: 0; 
            transform: translateY(30px);
            animation: fadeInUp 1s ease forwards;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .testimonial:hover {
            box-shadow: 0 4px 20px rgba(255, 117, 24, 0.3); 
            transform: translateY(-10px); 
        }

        .testimonial p {
            margin-bottom: 0.5rem;
            color: #000;
        }

        .testimonial .author {
            font-weight: bold;
            color: #FF7518; 
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <section class="testimonial-section" id="testimonial">
        <div class="testimonial-header">
            <h2>What Our Diners Say About Us</h2>
            <div class="icons">
                <span class="icon">🏠</span>
                <span class="icon">🐟</span>
                <span class="icon">🐚</span>
            </div>
        </div>
        <div class="testimonials">
            <div class="testimonial">
                <p>"The atmosphere is perfect, and every dish is a masterpiece! I've never had such a memorable dining experience—highly recommend it!"</p>
                <p class="author">Aditi S.</p>
            </div>
            <div class="testimonial">
                <p>"From the warm staff to the outstanding flavors, this place is a hidden gem. I look forward to each visit!"</p>
                <p class="author">Vishesh Y.</p>
            </div>
            <div class="testimonial">
                <p>"Exceptional food and service! Every meal feels special, with attention to detail that makes this restaurant my top choice."</p>
                <p class="author">Kavya N.</p>
            </div>
        </div>
    </section>
</body>
</html>
