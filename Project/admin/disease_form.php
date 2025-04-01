<?php
// Include the database connection file
include('../config/db.php');

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $disease_name = isset($_POST['disease_name']) ? mysqli_real_escape_string($conn, $_POST['disease_name']) : '';
    $animal_type = isset($_POST['animal_type']) ? mysqli_real_escape_string($conn, $_POST['animal_type']) : '';
    $location = isset($_POST['location']) ? mysqli_real_escape_string($conn, $_POST['location']) : '';
    $affected_count = isset($_POST['affected_count']) ? (int)$_POST['affected_count'] : '';
    $reported_date = isset($_POST['reported_date']) ? mysqli_real_escape_string($conn, $_POST['reported_date']) : '';
    $status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : '';
    $symptoms = isset($_POST['symptoms']) ? mysqli_real_escape_string($conn, $_POST['symptoms']) : '';
    $treatment = isset($_POST['treatment']) ? mysqli_real_escape_string($conn, $_POST['treatment']) : '';

    // Validate required fields
    if (!empty($disease_name) && !empty($animal_type) && !empty($location) && !empty($affected_count) && !empty($reported_date) && !empty($status) && !empty($symptoms) && !empty($treatment)) {
        // SQL Insert query
        $query = "INSERT INTO disease_cases (disease_name, location, animal_type, affected_count, reported_date, status, symptoms, treatment) 
                  VALUES ('$disease_name', '$location', '$animal_type', $affected_count, '$reported_date', '$status', '$symptoms', '$treatment')";

        // Execute the query
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Disease case added successfully!'); window.location.href='disease_form.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('Please fill in all the required fields.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disease Case Form</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg mt-8">
        <h2 class="text-2xl font-semibold mb-6">Add New Disease Case</h2>

        <!-- Form to collect data -->
        <form action="disease_form.php" method="POST" class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Disease Name -->
                <div>
                    <label class="block text-gray-700 mb-2">Disease Name</label>
                    <input type="text" name="disease_name" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                
                <!-- Animal Type -->
                <div>
                    <label class="block text-gray-700 mb-2">Animal Type</label>
                    <select name="animal_type" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="">Select Animal Type</option>
                        <option value="cattle">Cattle</option>
                        <option value="sheep">Sheep</option>
                        <option value="goat">Goat</option>
                        <option value="pig">Pig</option>
                        <option value="poultry">Poultry</option>
                    </select>
                </div>
                
                <!-- Location -->
                <div>
                    <label class="block text-gray-700 mb-2">Location</label>
                    <input type="text" name="location" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                
                <!-- Affected Count -->
                <div>
                    <label class="block text-gray-700 mb-2">Affected Count</label>
                    <input type="number" name="affected_count" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                
                <!-- Reported Date -->
                <div>
                    <label class="block text-gray-700 mb-2">Reported Date</label>
                    <input type="date" name="reported_date" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                
                <!-- Status -->
                <div>
                    <label class="block text-gray-700 mb-2">Status</label>
                    <select name="status" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="Active">Active</option>
                        <option value="Controlled">Controlled</option>
                        <option value="Resolved">Resolved</option>
                    </select>
                </div>
                
                <!-- Symptoms -->
                <div>
                    <label class="block text-gray-700 mb-2">Symptoms</label>
                    <textarea name="symptoms" required class="w-full px-4 py-2 border rounded-lg" rows="4"></textarea>
                </div>
                
                <!-- Treatment -->
                <div>
                    <label class="block text-gray-700 mb-2">Treatment</label>
                    <textarea name="treatment" required class="w-full px-4 py-2 border rounded-lg" rows="4"></textarea>
                </div>
            </div>
            
            <button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Add Disease</button>
        </form>
    </div>

</body>
</html>
