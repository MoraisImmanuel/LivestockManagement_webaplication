<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $stored_password, $role);

    if ($stmt->fetch() && password_verify($password, $stored_password)) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $role;
        header("Location: " . ($role === 'admin' ? 'admin/dashboard.php' : 'client/home.php'));
        exit();
    } else {
        echo "Invalid credentials";
    }

    $stmt->close();
    $conn->close();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Login & Registration</title>
</head>
<style>
    
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
</style>
<body>
    <div class="wrapper">
        <nav class="nav">
            <div class="nav-logo">
                <p><a href="#" class="logo">
        <img src="logo.png" alt="YourBrand Logo" 
        style="width: 100px; height: auto; display: block; object-fit: contain; transition: transform 0.3s ease, filter 0.3s ease;">
</a></p>
            </div>
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="index.php" class="link active">Home</a></li>
                    <li><a href="Our_team.html" class="link">About Us</a></li>
                    <li><a href="service.html" class="link">Services</a></li>
                    <li><a href="profile.html" class="link">Contact</a></li>
                  
                </ul>
            </div>
            <div class="nav-button">
                <button class="btn white-btn" id="loginBtn" onclick="toggleForm('login')">Sign In</button>
                <button class="btn" id="registerBtn" onclick="toggleForm('register')">Sign Up</button>
            </div>
            <div class="nav-menu-btn">
                <i class="bx bx-menu" onclick="myMenuFunction()"></i>
            </div>
        </nav>

        <div class="form-box">
            <!-- Login form -->
            <form action="login.php" method="POST" class="login-container" id="login">
                <div class="top">
                    <span>Don't have an account? <a href="#" onclick="toggleForm('register')">Sign Up</a></span>
                    <header>Login</header>
                </div>
                <div class="input-box">
                    <input type="text" class="input-field" id="username" name="username" required placeholder="Username">
                    <i class="bx bx-user"></i>
                </div>
                <div class="input-box">
                    <input type="password" class="input-field" id="password" name="password" required placeholder="Password">
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="input-box">
                    <input type="submit" class="submit" value="Login">
                </div>
                <div class="two-col">
                    <div class="one">
                        <input type="checkbox" id="login-check">
                        <label for="login-check">Remember Me</label>
                    </div>
                    <div class="two">
                        <label><a href="forgetpassword.php">Forgot password?</a></label>
                    </div>
                </div>
            </form>

            <!-- Registration form -->
            <form onsubmit="registerUser(event)" class="register-container" id="register">
                <div class="top">
                    <span>Have an account? <a href="#" onclick="toggleForm('login')">Login</a></span>
                    <header>Sign Up</header>
                </div>
                <div class="two-forms">
                    <div class="input-box">
                        <input type="text" class="input-field" id="register-firstname" name="firstname" placeholder="Firstname" required>
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="input-box">
                        <input type="text" class="input-field" id="register-lastname" name="lastname" placeholder="Lastname" required>
                        <i class="bx bx-user"></i>
                    </div>
                </div>
                <div class="input-box">
                    <input type="text" class="input-field" id="register-username" name="username" placeholder="Username" required>
                    <i class="bx bx-user"></i>
                </div>
                <div class="input-box">
                    <input type="email" class="input-field" id="register-email" name="email" placeholder="Email" required>
                    <i class="bx bx-envelope"></i>
                </div>
                <div class="input-box">
                    <input type="password" class="input-field" id="register-password" name="password" placeholder="Password" required>
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="input-box">
                    <input type="submit" class="submit" value="Register">
                </div>
                <div class="two-col">
                    <div class="one">
                        <input type="checkbox" id="register-check">
                        <label for="register-check">Remember Me</label>
                    </div>
                    <div class="two">
                        <label><a href="Terms.html">Terms & Conditions</a></label>
                    </div>
                </div>
            </form>
        </div>
    </div> 

    <script>
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
