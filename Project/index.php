<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Home Page with Slider</title>
    <link rel="stylesheet" href="style.css">
  
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: #f8f9fa;
        }

/* Enhanced Navigation Styles */
nav {
    background-color: rgba(255, 255, 255, 0.9);  /* Initially transparent */
    box-shadow: none;  /* No shadow before scrolling */
    position: sticky;
    top: 0;
    z-index: 1000;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;  /* Smooth transition */
}

.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    font-size: 1.8rem;
    font-weight: bold;
    color: #2c3e50;
    text-decoration: none;
}

.nav-links {
    display: flex;
    gap: 2.5rem;
}

.nav-links a {
    text-decoration: none;
    color: #2c3e50;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
    padding: 5px 0;
}

.nav-links a::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background-color: #3498db;
    transition: width 0.3s ease;
}

.nav-links a:hover::after {
    width: 100%;
}

/* On scroll, change the navbar appearance */
nav.scrolled {
    background-color: rgba(255, 255, 255, 0.9); /* Slight opaque white background */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Add a subtle shadow */
}

        /* Enhanced Slider Styles */
        .slider-container {
            position: relative;
            width: 100%;
            height: 600px;
            overflow: hidden;
        }

        .slider {
            display: flex;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
        }

        .slide {
            min-width: 100%;
            height: 100%;
            position: relative;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Individual slide backgrounds */
        .slide:nth-child(1) {
            background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)),
                url('background1.jpg');
        }

        .slide:nth-child(2) {
            background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)),
                url('background.jpg');
        }

        .slide:nth-child(3) {
            background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)),
                url('vaccination.jpg');
        }

        .slide-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 3rem;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
            color: white;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.5s ease;
        }

        .slide.active .slide-content {
            transform: translateY(0);
            opacity: 1;
        }

        .slide-content h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .slide-content p {
            font-size: 1.2rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            max-width: 600px;
        }

        .slider-buttons button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.2);
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.5rem;
            color: white;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        .slider-buttons button:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-50%) scale(1.1);
        }

        .prev-btn {
            left: 2rem;
        }

        .next-btn {
            right: 2rem;
        }

        .dots {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 1rem;
            z-index: 100;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            border: 2px solid white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dot.active {
            background: white;
            transform: scale(1.2);
        }

        /* Enhanced Feature Cards */
        .main-content {
            max-width: 1200px;
            margin: 5rem auto;
            padding: 0 1rem;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .feature-card h3 {
            margin-bottom: 1rem;
            color: #2c3e50;
            font-size: 1.5rem;
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }

            .nav-links.active {
                display: flex;
            }

            .mobile-menu-btn {
                display: block;
            }

            .slider-container {
                height: 400px;
            }

            .slide-content h2 {
                font-size: 1.8rem;
            }
        }

        .logo {
    font-size: 1.2rem;               /* Reduced size of the logo text */
    font-weight: bold;               /* Keep text bold */
    color: #3498db;                  /* Primary color */
    text-decoration: none;           /* Remove underline */
    text-transform: uppercase;       /* Uppercase letters */
    letter-spacing: 1px;             /* Slightly reduce spacing between letters */
    transition: color 0.3s ease;     /* Smooth color transition on hover */
    position: relative;              /* For pseudo-element styling */
}

.logo:hover {
    color: #2980b9;                  /* Darker shade on hover */
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);  /* Subtler shadow effect */
}

.logo::after {
    content: '•';                    /* Decorative dot after the logo */
    color: #e74c3c;                  /* Dot color */
    font-size: 1rem;                 /* Reduced size of the dot */
    position: absolute;              /* Position relative to the logo */
    top: 0;                          /* Adjust position */
    right: -10px;                    /* Closer to the text */
    transition: transform 0.3s ease; /* Smooth transition for animation */
}

.logo:hover::after {
    transform: scale(1.1);           /* Smaller enlargement effect on hover */
}

/* Style for the image logo */
.logo-img {
    width: 50px;                  /* Adjust width to make it smaller or bigger */
    height: 10px;                  /* Maintain the aspect ratio */
    display: block;                /* Remove bottom space/line, so it behaves like a block element */
    object-fit: contain;           /* Ensures the logo stays within the container without distortion */
    transition: transform 0.3s ease, filter 0.3s ease;  /* Smooth transition effects */
}

/* Hover effect for the logo image */
.logo-img:hover {
    transform: scale(1.1);         /* Slightly zoom in the logo */
    filter: brightness(1.2);       /* Increase brightness when hovered */
}


/* Dropdown Menu Styles */
.nav-item {
    position: relative; /* Ensures the dropdown menu positions correctly relative to the nav item */
}

.nav-link {
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
    text-decoration: none;
    padding: 10px;
    transition: background-color 0.3s ease, color 0.3s ease;
    display: block; /* Ensures proper positioning */
}

/* Hover effect for the nav link */
.nav-link:hover {
    color: #f6a613;  /* Color when hovered */
    background-color: #ecf0f1;  /* Light background color on hover */
}

/* Dropdown Container */
.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%; /* Position the dropdown directly below the nav item */
    left: 0; /* Align to the left edge of the nav item */
    background-color: white;
    border: 1px solid #ddd; /* Fixed the border */
    border-radius: 5px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: opacity 0.3s ease, visibility 0s 0.3s, transform 0.3s ease;
    z-index: 999;
    min-width: 180px; /* Ensures the dropdown menu has sufficient width */
}

/* Show Dropdown Menu when hovering over the nav item */
.nav-item:hover .dropdown-menu {
    display: block;
    opacity: 1;
    visibility: visible;
    transform: translateY(0); /* Moves the dropdown into view */
    transition: opacity 0.3s ease, visibility 0s 0s, transform 0.3s ease;
}

/* Style for the dropdown items */
.dropdown-item {
    padding: 10px 15px; /* Adjust padding for better space inside the dropdown */
    color: #2c3e50;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.3s ease, color 0.3s ease;
    display: block; /* Ensures each item is in its own block */
}

/* Hover effect for dropdown items */
.dropdown-item:hover {
    background-color: #f6a613;
    color: white;
}

/* Additional Styling for Dropdown Container */
.nav-item:hover .dropdown-menu {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); /* Add subtle shadow on hover */
}

/* Optional: Adjust for smaller screens */
@media (max-width: 768px) {
    .dropdown-menu {
        min-width: 150px; /* Adjust width for mobile */
    }
}






    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
        <a href="#" class="logo">
        <img src="logo.png" alt="YourBrand Logo" 
        style="width: 100px; height: auto; display: block; object-fit: contain; transition: transform 0.3s ease, filter 0.3s ease;">
</a>
            <button class="mobile-menu-btn">☰</button>
            <div class="nav-links">
                <a href="#">Home</a>
                <div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">About</a>
    <div class="dropdown-menu">
        <a href="Our_team.html" class="dropdown-item">Our Team</a>
        <a href="About_Us.html" class="dropdown-item" >Mission & Vision</a>
    </div>
</div>

<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Services</a>
    <div class="dropdown-menu">
        <a href="service.html" class="dropdown-item">Discover Local disease</a>
        
    </div>
</div>

                
                <a href="login.php">Login</a>
                <a href="login.php">Sign Up</a>
                <div class="nav-button">
     
            </div>
            </div>
        </div>
    </nav>

    <!-- Slider Section -->
    <div class="slider-container">
        <div class="slider">
            <div class="slide active">
                <div class="slide-content">
                    <h2>Welcome to Our Website</h2>
                    <p>Discover amazing features and services</p>
                </div>
            </div>
            <div class="slide">
                <div class="slide-content">
                    <h2>Professional Services</h2>
                    <p>Expert solutions for your needs</p>
                </div>
            </div>
            <div class="slide">
                <div class="slide-content">
                    <h2>Quality Guarantee</h2>
                    <p>We ensure the best quality for our customers</p>
                </div>
            </div>
        </div>
        <div class="slider-buttons">
            <button class="prev-btn">❮</button>
            <button class="next-btn">❯</button>
        </div>
        <div class="dots"></div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <div class="features">
            <!-- Add your feature cards here -->
        </div>
    </main>

    <script>
        // Enhanced Slider functionality
        const slider = document.querySelector('.slider');
        const slides = document.querySelectorAll('.slide');
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');
        const dotsContainer = document.querySelector('.dots');
        let currentSlide = 0;
        const slideCount = slides.length;

        // Mobile menu functionality
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const navLinks = document.querySelector('.nav-links');

        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });

        // Create dots
        slides.forEach((_, index) => {
            const dot = document.createElement('button');
            dot.classList.add('dot');
            if (index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(index));
            dotsContainer.appendChild(dot);
        });

        const dots = document.querySelectorAll('.dot');

        function updateSlides() {
            slides.forEach((slide, index) => {
                slide.classList.toggle('active', index === currentSlide);
            });
        }

        function updateDots() {
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
        }

        function goToSlide(index) {
            currentSlide = index;
            slider.style.transform = `translateX(-${currentSlide * 100}%)`;
            updateDots();
            updateSlides();
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slideCount;
            goToSlide(currentSlide);
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + slideCount) % slideCount;
            goToSlide(currentSlide);
        }

        // Event listeners
        prevBtn.addEventListener('click', prevSlide);
        nextBtn.addEventListener('click', nextSlide);

        // Pause auto-slide on hover
        slider.addEventListener('mouseenter', () => clearInterval(autoSlideInterval));
        slider.addEventListener('mouseleave', startAutoSlide);

        // Touch events for mobile
        let touchStartX = 0;
        let touchEndX = 0;

        slider.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        });

        slider.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });

        function handleSwipe() {
            const swipeThreshold = 50;
            const difference = touchStartX - touchEndX;

            if (Math.abs(difference) > swipeThreshold) {
                if (difference > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        }

        // Auto slide function
        let autoSlideInterval;

        function startAutoSlide() {
            autoSlideInterval = setInterval(nextSlide, 5000);
        }

        startAutoSlide();



        // JavaScript to toggle 'scrolled' class when the user scrolls
window.addEventListener('scroll', function () {
    const nav = document.querySelector('nav');
    if (window.scrollY > 0) {
        nav.classList.add('scrolled');  // Add 'scrolled' class when scrolling
    } else {
        nav.classList.remove('scrolled');  // Remove 'scrolled' class when at the top
    }
});


function myMenuFunction() {
            var i = document.getElementById("navMenu");
            i.classList.toggle("responsive");
        }

        function toggleForm(form) {
            var loginForm = document.getElementById("login");
            var registerForm = document.getElementById("register");
            var loginBtn = document.getElementById("loginBtn");
            var registerBtn = document.getElementById("registerBtn");

            if (form === 'login') {
                loginForm.style.left = "4px";
                registerForm.style.right = "-520px";
                loginBtn.classList.add("white-btn");
                registerBtn.classList.remove("white-btn");
                loginForm.style.opacity = 1;
                registerForm.style.opacity = 0;
            } else if (form === 'register') {
                loginForm.style
                loginForm.style.left = "-520px";
                registerForm.style.right = "4px";
                registerBtn.classList.add("white-btn");
                loginBtn.classList.remove("white-btn");
                loginForm.style.opacity = 0;
                registerForm.style.opacity = 1;
            }
        }

        function registerUser(event) {
            event.preventDefault(); // Prevent form submission

            // Get the values from the registration form
            var firstname = document.getElementById("register-firstname").value;
            var lastname = document.getElementById("register-lastname").value;
            var username = document.getElementById("register-username").value;
            var email = document.getElementById("register-email").value;
            var password = document.getElementById("register-password").value;

            // Prepare data for POST request
            var data = {
                firstname: firstname,
                lastname: lastname,
                username: username,
                email: email,
                password: password
            };

            // Send the data via AJAX
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "register.php", true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert("Registration successful! Please log in.");
                        toggleForm('login');
                    } else {
                        alert(response.message);
                    }
                }
            };
            xhr.send(JSON.stringify(data));
        }
    </script>
</body>
</html>