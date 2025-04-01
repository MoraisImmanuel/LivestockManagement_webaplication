<?php
session_start();
require '../config/db.php';

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Prepare the DELETE statement
    try {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        
        // Bind the user ID to the query
        $stmt->bind_param('i', $userId);
        
        // Execute the query
        if ($stmt->execute()) {
            // Redirect to user management page with success message
            $_SESSION['message'] = "User deleted successfully.";
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Error deleting user: " . $conn->error;
            header("Location: dashboard.php");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting user: " . $e->getMessage();
        header("Location: dashboard.php");
        exit();
    }
} else {
    // If no ID is provided, redirect to the user management page with an error
    $_SESSION['error'] = "Invalid request.";
    header("Location: user_management.php");
    exit();
}
?>
