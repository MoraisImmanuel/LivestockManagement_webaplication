<?php
session_start();
require '../config/db.php';

// Initialize errors array
$errors = [];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and validate inputs
    $user_id = $_POST['user_id'];
    $username = trim($_POST['username']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    // Validate required fields
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($firstname)) {
        $errors[] = "First name is required.";
    }
    if (empty($lastname)) {
        $errors[] = "Last name is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($role)) {
        $errors[] = "Role is required.";
    }

    // If there are no errors, proceed with the update
    if (empty($errors)) {
        // Prepare SQL statement
        $sql = "UPDATE users SET username = ?, firstname = ?, lastname = ?, email = ?, role = ? WHERE id = ?";

        // Prepare and bind parameters
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssi", $username, $firstname, $lastname, $email, $role, $user_id);

            // Execute the query
            if ($stmt->execute()) {
                // Redirect to the users page with a success message
                $_SESSION['message'] = "User updated successfully!";
                header("Location: dashboard.php"); // Redirect to the user management page
                exit;
            } else {
                $errors[] = "Error updating user: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $errors[] = "Error preparing the statement: " . $conn->error;
        }
    }
} else {
    // Fetch the user data based on the provided ID
    $user_id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute() && $result = $stmt->get_result()) {
            $user = $result->fetch_assoc();
        } else {
            $errors[] = "Error fetching user data: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errors[] = "Error preparing the statement: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen p-6 lg:p-8">
        <div class="max-w-6xl mx-auto">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Edit User</h1>
                <p class="text-gray-600 mt-2">Update user information</p>
            </header>

            <!-- Edit User Form -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Update User Details</h2>
                
                <form action="edit_user.php" method="POST" class="space-y-4">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" name="username" id="username" required 
                                value="<?php echo htmlspecialchars($user['username']); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="firstname" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" name="firstname" id="firstname" required 
                                value="<?php echo htmlspecialchars($user['firstname']); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="lastname" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" name="lastname" id="lastname" required 
                                value="<?php echo htmlspecialchars($user['lastname']); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="email" required 
                                value="<?php echo htmlspecialchars($user['email']); ?>"
                                class="w-full px-3 py-2 border border-gray -300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select name="role" id="role" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Role</option>
                                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if (isset($errors) && !empty($errors)): ?>
    <div id="error-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-medium text-red-600 mb-4">Error</h3>
            <?php foreach ($errors as $error): ?>
                <p class="text-gray-700 mb-2"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
            <button onclick="document.getElementById('error-modal').style.display='none'" 
                class="mt-4 px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                Close
            </button>
        </div>
    </div>
    <?php endif; ?>
</body>
</html>