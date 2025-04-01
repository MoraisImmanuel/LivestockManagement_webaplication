<?php
session_start();
require_once 'config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $firstname = trim($data['firstname']);
    $lastname = trim($data['lastname']);
    $username = trim($data['username']);
    $email = trim($data['email']);
    $password = $data['password'];
    $role = 'client';

    // Validate fields
    if (empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit();
    }

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Username or email is already taken."]);
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->close();

    // Hash password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, username, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firstname, $lastname, $username, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registration successful!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>



