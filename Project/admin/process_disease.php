<?php
session_start();
require_once __DIR__ . '../config/db.php'; // Adjusted path to config.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $disease_name = isset($_POST['disease_name']) ? mysqli_real_escape_string($conn, $_POST['disease_name']) : '';
    $animal_type = isset($_POST['animal_type']) ? mysqli_real_escape_string($conn, $_POST['animal_type']) : '';
    $symptoms = isset($_POST['symptoms']) ? mysqli_real_escape_string($conn, $_POST['symptoms']) : '';
    $treatment = isset($_POST['treatment']) ? mysqli_real_escape_string($conn, $_POST['treatment']) : '';

    if ($disease_name && $animal_type && $symptoms && $treatment) {
        $query = "INSERT INTO diseases (disease_name, animal_type, symptoms, treatment) 
                  VALUES ('$disease_name', '$animal_type', '$symptoms', '$treatment')";

        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Disease added successfully";
        } else {
            $_SESSION['error'] = "Error adding disease: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "All fields are required.";
    }

    // Redirect to the disease management page
    header("Location: diseases.php");
    exit();
}
?>
