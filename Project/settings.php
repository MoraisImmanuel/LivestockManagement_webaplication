<?php
session_start();
require_once 'config/db.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['user_id'])) {
    die("You are not logged in. Please log in to view your settings.");
}

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");

if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

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
    <title>Account Settings | Livestock Monitoring System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --danger-color: #dc2626;
            --success-color: #16a34a;
            --background-light: #f8fafc;
            --background-dark: #1e293b;
            --text-light: #64748b;
            --text-dark: #1e293b;
            --border-color: #e2e8f0;
            --card-background: #ffffff;
            --input-background: #f8fafc;
            --disabled-background: #f1f5f9;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background-light);
            color: var(--text-dark);
            line-height: 1.5;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .settings-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .settings-header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .settings-header p {
            color: var(--text-light);
        }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .settings-card {
            background: var(--card-background);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
        }

        .card-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .card-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: var(--input-background);
            color: var(--text-dark);
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-input:disabled {
            background-color: var(--disabled-background);
            cursor: not-allowed;
            opacity: 0.7;
        }

        .readonly-field {
            background-color: var(--disabled-background);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            color: var(--text-light);
            font-size: 0.875rem;
        }

        .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: var(--input-background);
            color: var(--text-dark);
            font-size: 0.875rem;
            cursor: pointer;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            width: 100%;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        /* Dark mode styles */
        body.dark-mode {
            background-color: var(--background-dark);
            color: white;
        }

        body.dark-mode .settings-card {
            background-color: #2d3748;
        }

        body.dark-mode .form-input,
        body.dark-mode .form-select,
        body.dark-mode .readonly-field {
            background-color: #374151;
            border-color: #4b5563;
            color: white;
        }

        body.dark-mode .form-input:disabled,
        body.dark-mode .readonly-field {
            background-color: #1f2937;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .settings-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="settings-header">
            <h1>Account Settings</h1>
            <p>Manage your account preferences and settings</p>
        </div>

        <div class="settings-grid">
            <div class="settings-card">
                <div class="card-header">
                    <h2>Account Information</h2>
                </div>
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="readonly-field"><?php echo htmlspecialchars($user['username']); ?></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="readonly-field"><?php echo htmlspecialchars($user['email']); ?></div>
                </div>
            </div>

            <div class="settings-card">
                <div class="card-header">
                    <h2>Security Settings</h2>
                </div>
                <form method="POST" action="update_password.php" id="passwordForm">
                    <div class="form-group">
                        <label class="form-label" for="current_password">Current Password</label>
                        <input type="password" 
                               class="form-input" 
                               id="current_password" 
                               name="current_password"
                               required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="new_password">New Password</label>
                        <input type="password" 
                               class="form-input" 
                               id="new_password" 
                               name="new_password"
                               required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Confirm New Password</label>
                        <input type="password" 
                               class="form-input" 
                               id="confirm_password" 
                               name="confirm_password"
                               required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        Update Password
                    </button>
                </form>
            </div>

           
        </div>
    </div>

    <script>
        // Theme toggle functionality
        
        // Load saved theme preference
      

        // Password validation
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New passwords do not match!');
            }
        });
    </script>
</body>
</html>