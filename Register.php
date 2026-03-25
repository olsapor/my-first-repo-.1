<?php
require_once 'config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        $conn = getDBConnection();
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT user_id FROM user WHERE email = ?"); 
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Email already registered';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
          $stmt = $conn->prepare("INSERT INTO user (user_name, email, password, address, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $email, $hashed_password, $address, $phone);
            
            if ($stmt->execute()) {
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Aurora Skin</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --cream: #FAF7F2;
            --sage: #8B9D83;
            --terracotta: #D4A574;
            --charcoal: #2C2C2C;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--cream);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .register-container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 0.5rem;
            color: var(--charcoal);
            letter-spacing: 3px;
        }

        .subtitle {
            text-align: center;
            color: #7A7A7A;
            margin-bottom: 3rem;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--charcoal);
            font-size: 0.9rem;
            font-weight: 500;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid rgba(139, 157, 131, 0.2);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
            font-family: 'Montserrat', sans-serif;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: var(--sage);
        }

        .success-message {
            background: #e6f7e6;
            color: #27ae60;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .error-message {
            background: #ffe6e6;
            color: #d63031;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: var(--sage);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .submit-btn:hover {
            background: #7A8C75;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(139, 157, 131, 0.3);
        }

        .links {
            margin-top: 2rem;
            text-align: center;
        }

        .links a {
            color: var(--sage);
            text-decoration: none;
            font-size: 0.95rem;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .divider {
            margin: 1rem 0;
            color: #CCCCCC;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1 class="logo">AURORA</h1>
        <p class="subtitle">Create your account</p>
        
        <?php if ($success): ?>
            <div class="success-message"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone">
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address"></textarea>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="submit-btn">CREATE ACCOUNT</button>
        </form>
        
        <div class="links">
            <a href="login.php">Already have an account? Login</a>
            <div class="divider">·</div>
            <a href="index.php">Back to Home</a>
        </div>
    </div>
</body>
</html>
