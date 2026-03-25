<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $conn = getDBConnection();
    
    // ស្វែងរកអ្នកប្រើប្រាស់តាមរយៈ Email
    $stmt = $conn->prepare("SELECT user_id, user_name, password FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // ផ្ទៀងផ្ទាត់លេខកូដសម្ងាត់ (Password)
        if (password_verify($password, $user['password'])) {
            // បង្កើត Session បើលេខកូដត្រូវ
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            
            header("Location: index.php"); // ចូលទៅកាន់ទំព័រដើមវិញ
            exit();
        } else {
            $error = 'លេខកូដសម្ងាត់មិនត្រឹមត្រូវទេ!';
        }
    } else {
        $error = 'រកមិនឃើញអ៊ីមែលនេះនៅក្នុងប្រព័ន្ធឡើយ!';
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <title>Login - Aurora Skin</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <div class="login-container">
        <h1>LOGIN</h1>
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">LOGIN</button>
        </form>
        <p>មិនទាន់មានគណនីមែនទេ? <a href="register.php">ចុះឈ្មោះនៅទីនេះ</a></p>
    </div>
</body>
</html>