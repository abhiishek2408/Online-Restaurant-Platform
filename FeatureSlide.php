<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hero Section with Slider</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    /* Basic reset */
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background-color: #fff;
    }

    /* Slider container */
    .slider-container {
        position: relative;
  width: 100%;
  height: 100%;
  max-width: 1400px;
  margin: auto;
  overflow: hidden;
  margin-bottom: 15%;
    }

    /* Slider */
    .slider {
      display: flex;
      transition: transform 1s ease-in-out;
    }

    .slide {
  min-width: 100%;
  height: 600px;
  position: relative;

    }

    .slide img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      animation: zoomInOut 10s infinite; /* Animation for zoom in-out */
    }

    /* Zoom animation */
    @keyframes zoomInOut {
      0%, 100% {
        transform: scale(1); /* Normal size */
      }
      50% {
        transform: scale(1.1); /* Zoomed in */
      }
    }

    /* Text styling */
    .text {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: white;
      text-align: center;
      z-index: 2;
      /* background-color: rgba(0, 0, 0, 0.5); Semi-transparent background */
      padding: 20px;
      border-radius: 10px;
    
    }

    .text h1 {
      font-size: 3rem;
      margin-bottom: 10px;
      color: #FF7518;
    }

    .text p {
      font-size: 1.5rem;
      margin-bottom: 20px;
      color: #fff;
    }

    .btn-primary {
      padding: 10px 20px;
      background-color: #FF7518;
      color: white;
      text-decoration: none;
      border-radius: 20px;
    }

    /* Dots navigation */
    .dots-container {
      position: absolute;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 10px;
    }

    .dot {
      width: 10px;
      height: 10px;
      background-color: white;
      border-radius: 50%;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .dot.active {
      background-color: #FF7518;
    }



    /* Feature container */
.feature-container {
  display: flex;
  justify-content: space-around;
  align-items: center;
  width: 100%;
  max-width: 1200px;
  gap: 30px;
  padding: 20px;
  text-align: center;
  margin-left: 6.5%;
  position: absolute;
  top: 90%;
}

/* Feature box styles */
.feature-box {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  padding: 20px;
  width: 30%;
}

.feature-box h3 {
  font-size: 1.5rem;
  margin-top: 10px;
  color: #FF7518;
  font-weight: 600;
}

.feature-box p {
  font-size: 1rem;
  color: #333;
  margin-top: 10px;
}

/* Icon styles */
.iconsss {
  font-size: 3rem;
  color: #FF7518;
  margin-bottom: 15px;
}


/* Responsive design: Keeping all in the same row */
@media (max-width: 768px) {
  .feature-container {
      flex-wrap: nowrap;  /* This ensures the items stay in one row */
      overflow-x: auto;  /* Allows horizontal scrolling if needed on small screens */
  }

  .feature-box {
      width: 300px;  /* Fixed width to ensure they stay in row */
  }
}
  </style>
</head>
<body>
  <div class="slider-container">
    <div class="slider">
      <!-- Slide 1 
      <div class="slide">
        <img src="https://demo-themewinter.com/deliciko/deliciko-red/wp-content/uploads/sites/2/2019/06/slider_image03.jpg" alt="Slide 1">
        <div class="text">
          <h1>Welcome to Our Restaurant</h1>
          <p>Indulge in a delightful dining experience.</p>
          <a href="/order" class="btn-primary">Explore Menu</a>
        </div>
      </div>-->
      <!-- Slide 2 -->
      <div class="slide">
        <img src="https://media.istockphoto.com/id/1428409996/photo/closeup-of-female-chef-in-restaurant-decorates-the-meal.jpg?s=612x612&w=0&k=20&c=WVNYWmlDaG1fEv5Be7hpBblsbPPFcZ62NcF9XaIJR6o=" alt="Slide 2">
        <div class="text">
          <h1>Fresh Ingredients</h1>
          <p>Enjoy meals made with fresh, high-quality ingredients.</p>
          <a href="/order" class="btn-primary">Order Now</a>
        </div>
      </div>
      <!-- Slide 3 
      <div class="slide">
        <img src="https://demo-themewinter.com/deliciko/deliciko-red/wp-content/uploads/sites/2/2019/06/slider_image03.jpg" alt="Slide 3">
        <div class="text">
          <h1>Relax and Enjoy</h1>
          <p>Unwind and savor every bite.</p>
          <a href="/booktable" class="btn-primary">Reserve a Table</a>
        </div>-->
      </div>
    </div>

    <!-- Dots navigation -->
    <div class="dots-container">
      <div class="dot active"></div>
      <div class="dot"></div>
      <div class="dot"></div>
    </div>
  </div>


  
  <div class="feature-container">
      <div class="feature-box">
        <div class="iconsss">
          <i class="fas fa-utensils"></i> 
        </div>
        <h3>Magical Atmosphere</h3>
        <p>Wonderful serenity has taken possession of my entire soul, like these sweet mornings.</p>
      </div>
      <div class="feature-box">
        <div class="iconsss">
          <i class="fas fa-concierge-bell"></i> 
        </div>
        <h3>Best Food Quality</h3>
        <p>Wonderful serenity has taken possession of my entire soul, like these sweet mornings.</p>
      </div>
      <div class="feature-box">
        <div class="iconsss">
          <i class="fas fa-dollar-sign"></i> 
        </div>
        <h3>Low Costing Food</h3>
        <p>Wonderful serenity has taken possession of my entire soul, like these sweet mornings.</p>
      </div>
    </div>

  <script>
    $(document).ready(function () {
      let currentIndex = 0;
      const slides = $('.slide');
      const dots = $('.dot');
      const totalSlides = slides.length;

      // Function to update the slider position
      function updateSlider() {
        const translateX = -currentIndex * 100; // Translate percentage
        $('.slider').css('transform', `translateX(${translateX}%)`);
        dots.removeClass('active').eq(currentIndex).addClass('active');
      }

      // Auto slide
      setInterval(() => {
        currentIndex = (currentIndex + 1) % totalSlides;
        updateSlider();
      }, 5000);

      // Dot click event
      dots.on('click', function () {
        currentIndex = $(this).index();
        updateSlider();
      });
    });
  </script>
</body>
</html>
