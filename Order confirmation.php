<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$orderId = $_GET['order_id'] ?? 0;

// Get order details
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND UserId = ?");
$stmt->bind_param("ii", $orderId, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header('Location: index.php');
    exit();
}

// Get order items
$stmt = $conn->prepare("
    SELECT oi.*, p.name, p.brand 
    FROM order_items oi 
    JOIN product p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$orderItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Aurora Skin</title>
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .confirmation-container {
            max-width: 700px;
            width: 100%;
            background: white;
            border-radius: 20px;
            padding: 4rem 3rem;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: var(--sage);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 3rem;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 300;
            color: var(--charcoal);
        }

        .order-number {
            color: var(--terracotta);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 2rem;
        }

        .message {
            color: #7A7A7A;
            line-height: 1.8;
            margin-bottom: 3rem;
            font-size: 1.05rem;
        }

        .order-details {
            background: var(--cream);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .detail-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            font-weight: 300;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(139, 157, 131, 0.2);
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-name {
            font-weight: 600;
        }

        .item-qty {
            color: #7A7A7A;
            font-size: 0.9rem;
        }

        .order-total {
            display: flex;
            justify-content: space-between;
            padding-top: 1.5rem;
            margin-top: 1rem;
            border-top: 2px solid var(--sage);
            font-size: 1.3rem;
            font-weight: 600;
        }

        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn {
            padding: 1rem 2.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            letter-spacing: 1px;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-primary {
            background: var(--sage);
            color: white;
        }

        .btn-primary:hover {
            background: #7A8C75;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: transparent;
            color: var(--sage);
            border: 2px solid var(--sage);
        }

        .btn-secondary:hover {
            background: var(--sage);
            color: white;
        }

        @media (max-width: 600px) {
            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="success-icon">✓</div>
        
        <h1>Order Confirmed!</h1>
        <div class="order-number">Order #<?= $orderId ?></div>
        
        <p class="message">
            Thank you for your purchase! Your order has been successfully placed and is being processed. 
            We'll send you a confirmation email shortly with tracking details.
        </p>

        <div class="order-details">
            <div class="detail-title">Order Summary</div>
            
            <?php foreach ($orderItems as $item): ?>
                <div class="order-item">
                    <div>
                        <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                        <div class="item-qty">Quantity: <?= $item['quantity'] ?></div>
                    </div>
                    <div style="font-weight: 600;">
                        $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="order-total">
                <span>Total Paid</span>
                <span>$<?= number_format($order['TotalPrice'], 2) ?></span>
            </div>
        </div>

        <div class="actions">
            <a href="products.php" class="btn btn-primary">CONTINUE SHOPPING</a>
            <a href="my_orders.php" class="btn btn-secondary">VIEW ORDERS</a>
        </div>
    </div>
</body>
</html>
