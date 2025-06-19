<?php
require_once 'functions.php';

session_start();

$message = '';
$showVerificationForm = false;

// Handle email submission
if ($_POST && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    
    if ($email) {
        $code = generateVerificationCode();
        $_SESSION['verification_code'] = $code;
        $_SESSION['pending_email'] = $email;
        
        if (sendVerificationEmail($email, $code)) {
            $message = "Verification code sent to your email!";
            $showVerificationForm = true;
        } else {
            $message = "Failed to send verification email. Please try again.";
        }
    } else {
        $message = "Please enter a valid email address.";
    }
}

// Handle verification code submission
if ($_POST && isset($_POST['verification_code'])) {
    $inputCode = $_POST['verification_code'];
    
    if (isset($_SESSION['verification_code']) && isset($_SESSION['pending_email'])) {
        if ($inputCode === $_SESSION['verification_code']) {
            $email = $_SESSION['pending_email'];
            
            if (registerEmail($email)) {
                $message = "Email successfully registered for GitHub timeline updates!";
                unset($_SESSION['verification_code']);
                unset($_SESSION['pending_email']);
                $showVerificationForm = false;
            } else {
                $message = "Failed to register email. Please try again.";
            }
        } else {
            $message = "Invalid verification code. Please try again.";
            $showVerificationForm = true;
        }
    } else {
        $message = "Session expired. Please start over.";
        $showVerificationForm = false;
    }
}

// Check if we should show verification form
if (isset($_SESSION['verification_code']) && isset($_SESSION['pending_email'])) {
    $showVerificationForm = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GitHub Timeline Subscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="email"], input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input[type="email"]:focus, input[type="text"]:focus {
            border-color: #4CAF50;
            outline: none;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>GitHub Timeline Subscription</h1>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : (strpos($message, 'sent') !== false ? 'info' : 'error'); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Email Registration Form -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Enter your email to subscribe to GitHub timeline updates:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <button type="submit" id="submit-email">Submit</button>
        </form>

        <!-- Verification Code Form -->
        <form method="POST" action="" style="margin-top: 30px;">
            <div class="form-group">
                <label for="verification_code">Enter the 6-digit verification code sent to your email:</label>
                <input type="text" name="verification_code" id="verification_code" maxlength="6" required>
            </div>
            <button type="submit" id="submit-verification">Verify</button>
        </form>

        <div style="margin-top: 30px; text-align: center;">
            <p><a href="unsubscribe.php">Want to unsubscribe? Click here</a></p>
        </div>
    </div>
</body>
</html>