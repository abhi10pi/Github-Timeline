<?php
require_once 'functions.php';

session_start();

$message = '';
$showVerificationForm = false;

// Handle unsubscribe email submission
if ($_POST && isset($_POST['unsubscribe_email'])) {
    $email = filter_var($_POST['unsubscribe_email'], FILTER_VALIDATE_EMAIL);
    
    if ($email) {
        // Check if email exists in registered emails
        $file = __DIR__ . '/registered_emails.txt';
        if (file_exists($file)) {
            $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (in_array($email, $emails)) {
                $code = generateVerificationCode();
                $_SESSION['unsubscribe_code'] = $code;
                $_SESSION['unsubscribe_email'] = $email;
                
                if (sendUnsubscribeEmail($email, $code)) {
                    $message = "Unsubscribe confirmation code sent to your email!";
                    $showVerificationForm = true;
                } else {
                    $message = "Failed to send confirmation email. Please try again.";
                }
            } else {
                $message = "Email not found in our subscription list.";
            }
        } else {
            $message = "No subscribers found.";
        }
    } else {
        $message = "Please enter a valid email address.";
    }
}

// Handle unsubscribe verification code submission
if ($_POST && isset($_POST['unsubscribe_verification_code'])) {
    $inputCode = $_POST['unsubscribe_verification_code'];
    
    if (isset($_SESSION['unsubscribe_code']) && isset($_SESSION['unsubscribe_email'])) {
        if ($inputCode === $_SESSION['unsubscribe_code']) {
            $email = $_SESSION['unsubscribe_email'];
            
            if (unsubscribeEmail($email)) {
                $message = "Successfully unsubscribed from GitHub timeline updates!";
                unset($_SESSION['unsubscribe_code']);
                unset($_SESSION['unsubscribe_email']);
                $showVerificationForm = false;
            } else {
                $message = "Failed to unsubscribe. Please try again.";
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
if (isset($_SESSION['unsubscribe_code']) && isset($_SESSION['unsubscribe_email'])) {
    $showVerificationForm = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribe from GitHub Timeline</title>
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
            border-color: #f44336;
            outline: none;
        }
        button {
            background-color: #f44336;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background-color: #da190b;
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
        <h1>Unsubscribe from GitHub Timeline</h1>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'Successfully') !== false ? 'success' : (strpos($message, 'sent') !== false ? 'info' : 'error'); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Unsubscribe Email Form -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="unsubscribe_email">Enter your email to unsubscribe:</label>
                <input type="email" name="unsubscribe_email" id="unsubscribe_email" required>
            </div>
            <button type="submit" id="submit-unsubscribe">Unsubscribe</button>
        </form>

        <!-- Unsubscribe Verification Code Form -->
        <form method="POST" action="" style="margin-top: 30px;">
            <div class="form-group">
                <label for="unsubscribe_verification_code">Enter the verification code sent to your email:</label>
                <input type="text" name="unsubscribe_verification_code" id="unsubscribe_verification_code" maxlength="6" required>
            </div>
            <button type="submit" id="verify-unsubscribe">Verify</button>
        </form>

        <div style="margin-top: 30px; text-align: center;">
            <p><a href="index.php">Back to subscription page</a></p>
        </div>
    </div>
</body>
</html>