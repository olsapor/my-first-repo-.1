<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user's orders
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM orders WHERE UserId = ? ORDER BY OrderDate DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Aurora Skin</title>
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

        .nav-links a {
            text-decoration: none;
            color: var(--charcoal);
            margin-left: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 120px auto 3rem;
            padding: 0 3%;
        }

        .page-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3.5rem;
            margin-bottom: 3rem;
            font-weight: 300;
        }

        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .order-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--sage);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #E0E0E0;
        }

        .order-id {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--charcoal);
        }

        .order-date {
            color: #7A7A7A;
            font-size: 0.95rem;
        }

        .order-status {
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-processing {
            background: #d1ecf1;
            color: #0c5460;
        }

        .order-total {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--terracotta);
            margin-bottom: 1rem;
        }

        .view-details-btn {
            padding: 0.8rem 2rem;
            background: var(--sage);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s;
        }

        .view-details-btn:hover {
            background: #7A8C75;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 6rem 2rem;
            background: white;
            border-radius: 15px;
        }

        .empty-state h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 300;
        }

        .empty-state p {
            color: #7A7A7A;
            margin-bottom: 2rem;
        }

        .shop-btn {
            display: inline-block;
            padding: 1rem 3rem;
            background: var(--sage);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav>
        <a href="index.php" class="logo">AURORA</a>
        <div class="nav-links">
            <a href="products.php">Shop</a>
            <a href="index.php">Home</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h1 class="page-title">My Orders</h1>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <h2>No orders yet</h2>
                <p>Start shopping to see your orders here</p>
                <a href="products.php" class="shop-btn">START SHOPPING</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <div class="order-id">Order #<?= $order['id'] ?></div>
                                <div class="order-date">
                                    <?= date('F j, Y g:i A', strtotime($order['OrderDate'])) ?>
                                </div>
                            </div>
                            <span class="order-status status-<?= strtolower($order['status']) ?>">
                                <?= htmlspecialchars($order['status']) ?>
                            </span>
                        </div>
                        
                        <div class="order-total">
                            Total: $<?= number_format($order['TotalPrice'], 2) ?>
                        </div>
                        
                        <a href="order_details.php?id=<?= $order['id'] ?>" class="view-details-btn">
                            View Details
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
