<?php
session_start();
require 'config/db.php';

// Check if the user is logged in by verifying if user ID is set in the session
if (!isset($_SESSION['user_id'])) {
    die("You are not logged in. Please log in to view your profile.");
}

// Fetch user data from the database based on the logged-in user's ID
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, firstname, lastname FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <!--<link rel="stylesheet" href="styles1.css">-->
    <style>

        /* General reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body styles */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f7f6;
    color: #333;
    line-height: 1.6;
}

/* Profile container */
.profile-container {
    display: flex;
    justify-content: center;
    align-items: flex-start; /* Align items to the top of the screen */
    height: 100vh;
    padding-top: 30px; /* Moves the profile up slightly */
}

/* Profile card */
.profile-card {
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 30px;
    width: 100%;
    max-width: 600px;
    text-align: center;
    margin-top: 20px; /* Optional: Adds some spacing from the top */
}

/* Heading */
.profile-card h2 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 24px;
}

/* Profile details list */
.profile-details {
    list-style: none;
    margin: 0;
    padding: 0;
    text-align: left;
}

/* List items */
.profile-details li {
    margin-bottom: 12px;
    font-size: 18px;
}

.profile-details li strong {
    color: #2c3e50;
}

/* Optional: Add a hover effect for the list items */
.profile-details li:hover {
    background-color: #ecf0f1;
    border-radius: 5px;
    padding: 5px;
}

/* Add some spacing below */
.profile-card ul {
    padding-left: 20px;
}

    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-card">
            <h2>Profile Details</h2>
            <ul class="profile-details">
                <li><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></li>
                <li><strong>First Name:</strong> <?php echo htmlspecialchars($user['firstname']); ?></li>
                <li><strong>Last Name:</strong> <?php echo htmlspecialchars($user['lastname']); ?></li>
                <li><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></li>
            </ul>
        </div>
    </div>
</body>
</html>
