<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $disease_name = $_POST['disease_name'];
    $location = $_POST['location'];
    $animal_type = $_POST['animal_type'];
    $affected_count = $_POST['affected_count'];
    $reported_date = date('Y-m-d');
    $status = $_POST['status'];
    $symptoms = $_POST['symptoms'];
    $treatment = "unknown"; // Always set treatment to "unknown"

    $stmt = $conn->prepare("INSERT INTO disease_cases (disease_name, location, animal_type, affected_count, reported_date, status, symptoms, treatment) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssissss", $disease_name, $location, $animal_type, $affected_count, $reported_date, $status, $symptoms, $treatment);
    
    if ($stmt->execute()) {
        $success_message = "Case reported successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livestock Disease Reporting System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --success-color: #16a34a;
            --danger-color: #dc2626;
            --background-color: #f1f5f9;
            --card-color: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background-color);
            color: var(--text-primary);
            line-height: 1.5;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .card {
            background: var(--card-color);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            padding: 2rem;
        }

        .header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .header h1 {
            color: var(--text-primary);
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        input, select, textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.875rem;
            color: var(--text-primary);
            background-color: var(--card-color);
            transition: border-color 0.15s ease-in-out;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-active { background-color: #fee2e2; color: #dc2626; }
        .status-controlled { background-color: #fef3c7; color: #d97706; }
        .status-resolved { background-color: #dcfce7; color: #16a34a; }

        button {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.15s ease-in-out;
        }

        button:hover {
            background-color: var(--secondary-color);
        }

        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .message.success {
            background-color: #dcfce7;
            color: var(--success-color);
        }

        .message.error {
            background-color: #fee2e2;
            color: var(--danger-color);
        }

        @media (max-width: 640px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full-width {
                grid-column: span 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>Livestock Disease Reporting System</h1>
                <p>Report new disease cases and track outbreak status</p>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="disease_name">Disease Name*</label>
                        <input type="text" name="disease_name" id="disease_name" required>
                    </div>

                    <div class="form-group">
                        <label for="location">Location*</label>
                        <input type="text" name="location" id="location" required>
                    </div>

                    <div class="form-group">
                        <label for="animal_type">Animal Type*</label>
                        <select name="animal_type" id="animal_type" required>
                            <option value="">Select animal type</option>
                            <option value="Cattle">Cattle</option>
                            <option value="Poultry">Poultry</option>
                            <option value="Goat">Goat</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="affected_count">Number of Affected Animals*</label>
                        <input type="number" name="affected_count" id="affected_count" required min="1">
                    </div>

                    <div class="form-group">
                        <label for="status">Status*</label>
                        <select name="status" id="status" required>
                            <option value="">Select status</option>
                            <option value="Active">Active</option>
                            <option value="Controlled">Controlled</option>
                            <option value="Resolved">Resolved</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="symptoms">Symptoms*</label>
                        <textarea name="symptoms" id="symptoms" placeholder="Describe the symptoms in detail" required></textarea>
                    </div>
                </div>

                <button type="submit">Submit Report</button>
            </form>
        </div>
    </div>
</body>
</html>