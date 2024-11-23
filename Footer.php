<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>

        
.footer {
    background-color: #2C2C2C; 
    color: #ffffff;
    font-family: 'Roboto', sans-serif;
    padding: 2rem;
    text-align: center;
   
   
}

footer { box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1); }

.footer-content {
    display: flex;
    justify-content: space-between;
    gap: 2rem;
    flex-wrap: wrap;
    max-width: 1200px;
    margin: auto;
    padding-bottom: 1rem;
    border-bottom: 1px solid #444444;
}

.footer-section {
    flex: 1;
    min-width: 200px;
    margin-bottom: 1rem;
}

.footer-section h3 {
    font-size: 1.2rem;
    color: #FF7518; 
    margin-bottom: 0.5rem;
}

.footer-section p,
.footer-section ul,
.footer-section a {
    font-size: 0.9rem;
    color: #ffffff;
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section ul li {
    margin: 0.3rem 0;
}

.footer-section a {
    color: #ffffff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-section a:hover {
    color: #FF7518; 
}


.social-icons {
    display: flex;
    justify-content: center;
    gap: 1rem;
}

.icon {
    font-size: 1.5rem;
    color: #FF7518; 
    transition: transform 0.3s ease;
}

.icon:hover {
    transform: scale(1.1); 
    color: #ffffff; 
}


.footer-bottom {
    padding-top: 1rem;
    font-size: 0.8rem;
    color: #888888;
}

.footer-bottom p {
    margin: 0;
}

@media (max-width: 768px) {
    .footer-content {
        flex-direction: column;
        align-items: center;
    }

    .footer-section {
        text-align: center;
    }

    .social-icons {
        justify-content: center;
    }
}

    </style>
</head>
<body>
<footer class="footer">
    <div class="footer-content">
  
        <div class="footer-section contact-info">
            <h3>Contact Us</h3>
            <p>123 Kashi Vishwanath Road, Varanasi, Uttar Pradesh, 221001</p>
            <p>Phone: +91 98765 43210</p>
            <p>Email: info@yourbistrofy.com</p>
        </div>

      
        <div class="footer-section quick-links">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#menu">Menu</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </div>

        
        <div class="footer-section social-media">
            <h3>Follow Us</h3>
            <div class="social-icons">
                <!-- Replace with actual icons or use Font Awesome if desired -->
                <a href="#" class="icon">📘</a>
                <a href="#" class="icon">📷</a>
                <a href="#" class="icon">🐦</a>
                <a href="#" class="icon">📍</a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p style="font-size: 1rem;">&copy; 2024 Bistrofy. All rights reserved.</p>
    </div>
</footer>

</body>
</html>