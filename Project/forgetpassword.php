<?php
session_start();
require 'config/db.php';

// Variable to hold error messages
$error_message = '';

/**
 * Generates a random 4-digit OTP.
 *
 * @return int
 */
function generateOtp() {
    return mt_rand(1000, 9999);
}

/**
 * Sends OTP to the specified email via Formspree.
 *
 * @param string $email
 * @param int $otp
 * @return void
 */
function sendOtpViaFormspree($email, $otp) {
    $formspree_url = 'https://formspree.io/f/xbljykdn';

    // Prepare email data
    $data = [
        'email' => $email,
        'subject' => 'Your OTP for Password Reset',
        'message' => 'Your OTP for password reset is: ' . $otp
    ];

    // Set up cURL for Formspree API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $formspree_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

    // Execute and close cURL session
    curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    }
    curl_close($ch);
}

// Step 1: Handle OTP Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email exists, proceed to generate and send OTP
        $otp = generateOtp();
        $expires_at = time() + 3600;

        // Store OTP details in session
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiration'] = $expires_at;
        $_SESSION['email'] = $email;

        // Send OTP to user's email
        sendOtpViaFormspree($email, $otp);

        // Redirect user to OTP verification page
        echo '<script>
                alert("An OTP has been sent to your email. Please check your inbox.");
                window.location.href = "?step=verify";
              </script>';
    } else {
        // Email does not exist in the database, set error message
        $error_message = 'Email not found. Please enter a registered email address.';
    }
}

// Step 2: Process OTP Verification and Password Reset
if (isset($_GET['step']) && $_GET['step'] === 'verify' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_otp = $_POST['otp'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['new_password_confirm'];

    // Validate OTP and expiration
    if (isset($_SESSION['otp'], $_SESSION['otp_expiration'], $_SESSION['email']) &&
        $_SESSION['otp'] == $input_otp && time() < $_SESSION['otp_expiration']) {

        // Validate new password match
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update user's password in the database
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $_SESSION['email']);
            $stmt->execute();

            // Clear session data for OTP
            unset($_SESSION['otp'], $_SESSION['otp_expiration'], $_SESSION['email']);

            echo '<div class="message success">Your password has been reset successfully.</div>';
        } else {
            $error_message = 'New passwords do not match.';
        }
    } else {
        $error_message = 'Invalid or expired OTP.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            background-image: url('background1.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        
        h2, h3 {
            color: #333;
            text-align: center;
        }

        /* Container for forms */
        .container {
            width: 100%;
            max-width: 400px;
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Form styles */
        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin: 0.5rem 0 0.2rem;
            color: #555;
        }

        input[type="email"],
        input[type="text"],
        input[type="password"] {
            padding: 0.6rem;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 1rem;
            transition: border-color 0.3s;
        }

        input:focus {
            border-color: #007bff;
            outline: none;
        }

        /* Button Styles */
        .button-group {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        button {
            padding: 0.7rem;
            font-size: 1rem;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-button {
            background-color: #6c757d;
        }

        /* Success & Error messages */
        .message {
            text-align: center;
            margin: 1rem 0;
            padding: 0.5rem;
            font-size: 0.9rem;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Password Reset</h2>

        <!-- Display Error Message -->
        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- OTP Request Form (Step 1) -->
        <?php if (!isset($_GET['step'])): ?>
        <form method="POST" action="">
            <label for="email">Enter your email to receive OTP:</label>
            <input type="email" name="email" required>
            <div class="button-group">
                <button type="submit">Send OTP</button>
                <button type="button" class="back-button" onclick="window.location.href='login.php';">Back</button>
            </div>
        </form>
        <?php endif; ?>

        <!-- OTP Verification and Password Reset Form (Step 2) -->
        <?php if (isset($_GET['step']) && $_GET['step'] === 'verify'): ?>
        <h3>Verify OTP and Set New Password</h3>
        <form method="POST" action="">
            <label for="otp">Enter OTP sent to your email:</label>
            <input type="text" name="otp" required>

            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" required>

            <label for="new_password_confirm">Confirm New Password:</label>
            <input type="password" name="new_password_confirm" required>

            <div class="button-group">
                <button type="submit">Reset Password</button>
                <button type="button" class="back-button" onclick="window.location.href='login.php';">Back</button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
