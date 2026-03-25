<?php
require_once 'config.php';

$success = '';
$error = '';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isLoggedIn) {
    $userId = $_SESSION['user_id'];
    $type = $_POST['type'] ?? '';
    $details = $_POST['details'] ?? '';
    
    // Generate unique complaint number
    $compNumber = 'COMP' . time() . rand(100, 999);
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO complaints (UserId, CompNumber, type, details, status) VALUES (?, ?, ?, ?, 'Open')");
    $stmt->bind_param("isss", $userId, $compNumber, $type, $details);
    
    if ($stmt->execute()) {
        $success = "Complaint submitted successfully! Your complaint number is: $compNumber";
    } else {
        $error = 'Failed to submit complaint. Please try again.';
    }
    
    $stmt->close();
    $conn->close();
}

// Get user's complaints if logged in
$userComplaints = [];
if ($isLoggedIn) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM complaints WHERE UserId = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userComplaints = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaints - Aurora Skin</title>
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
            color: var(--charcoal);
        }

        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(250, 247, 242, 0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
            padding: 1.5rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(139, 157, 131, 0.2);
        }

        .logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 300;
            letter-spacing: 3px;
            color: var(--charcoal);
            text-decoration: none;
        }

        .container {
            max-width: 900px;
            margin: 120px auto;
            padding: 0 2rem;
        }

        .page-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3.5rem;
            text-align: center;
            margin-bottom: 3rem;
            font-weight: 300;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            margin-bottom: 3rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .form-group {
            margin-bottom: 2rem;
        }

        label {
            display: block;
            margin-bottom: 0.8rem;
            color: var(--charcoal);
            font-weight: 600;
            font-size: 0.95rem;
        }

        input[type="text"],
        select,
        textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid rgba(139, 157, 131, 0.2);
            border-radius: 10px;
            font-size: 1rem;
            font-family: 'Montserrat', sans-serif;
            transition: all 0.3s;
        }

        textarea {
            resize: vertical;
            min-height: 150px;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--sage);
        }

        .submit-btn {
            width: 100%;
            padding: 1.2rem;
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
        }

        .success-message {
            background: #e6f7e6;
            color: #27ae60;
            padding: 1.2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .error-message {
            background: #ffe6e6;
            color: #d63031;
            padding: 1.2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .login-notice {
            text-align: center;
            padding: 3rem;
            color: #7A7A7A;
        }

        .login-notice a {
            color: var(--sage);
            text-decoration: none;
            font-weight: 600;
        }

        .complaints-list {
            margin-top: 4rem;
        }

        .complaints-list h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        .complaint-item {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--sage);
        }

        .complaint-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .complaint-number {
            font-weight: 600;
            color: var(--terracotta);
        }

        .complaint-status {
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-open {
            background: #fff3cd;
            color: #856404;
        }

        .status-resolved {
            background: #d4edda;
            color: #155724;
        }

        .complaint-type {
            color: var(--sage);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .complaint-details {
            color: #5A5A5A;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <nav>
        <a href="index.php" class="logo">AURORA</a>
        <div>
            <a href="index.php" style="text-decoration: none; color: var(--charcoal); margin-right: 2rem;">Home</a>
            <?php if ($isLoggedIn): ?>
                <a href="logout.php" style="text-decoration: none; color: var(--charcoal);">Logout</a>
            <?php else: ?>
                <a href="login.php" style="text-decoration: none; color: var(--charcoal);">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <h1 class="page-title">Customer Complaints</h1>

        <?php if (!$isLoggedIn): ?>
            <div class="card">
                <div class="login-notice">
                    <p>Please <a href="login.php">login</a> to submit a complaint.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2rem; margin-bottom: 2rem; font-weight: 300;">Submit a Complaint</h2>
                
                <?php if ($success): ?>
                    <div class="success-message"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="error-message"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="type">Complaint Type</label>
                        <select id="type" name="type" required>
                            <option value="">Select type...</option>
                            <option value="Product Quality">Product Quality</option>
                            <option value="Delivery">Delivery Issue</option>
                            <option value="Customer Service">Customer Service</option>
                            <option value="Billing">Billing</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="details">Details</label>
                        <textarea id="details" name="details" required placeholder="Please describe your complaint in detail..."></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">SUBMIT COMPLAINT</button>
                </form>
            </div>

            <?php if (!empty($userComplaints)): ?>
                <div class="complaints-list">
                    <h3>Your Complaints</h3>
                    <?php foreach ($userComplaints as $complaint): ?>
                        <div class="complaint-item">
                            <div class="complaint-header">
                                <span class="complaint-number"><?= htmlspecialchars($complaint['CompNumber']) ?></span>
                                <span class="complaint-status status-<?= strtolower($complaint['status']) ?>">
                                    <?= htmlspecialchars($complaint['status']) ?>
                                </span>
                            </div>
                            <div class="complaint-type"><?= htmlspecialchars($complaint['type']) ?></div>
                            <div class="complaint-details"><?= htmlspecialchars($complaint['details']) ?></div>
                            <div style="margin-top: 1rem; font-size: 0.85rem; color: #999;">
                                Submitted: <?= date('F j, Y', strtotime($complaint['created_at'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
