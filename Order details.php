<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$orderId = $_GET['id'] ?? 0;

// Get order details
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND UserId = ?");
$stmt->bind_param("ii", $orderId, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header('Location: my_orders.php');
    exit();
}

// Get order items with product details
$stmt = $conn->prepare("
    SELECT oi.*, p.name, p.brand, p.Description 
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
    <title>Order Details - Aurora Skin</title>
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
            padding: 1.5rem 5%;
            background: white;
            border-bottom: 1px solid rgba(139, 157, 131, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 300;
            letter-spacing: 3px;
            color: var(--charcoal);
            text-decoration: none;
        }

        .back-btn {
            padding: 0.8rem 1.5rem;
            background: var(--sage);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
        }

        .container {
            max-width: 1000px;
            margin: 3rem auto;
            padding: 0 3%;
        }

        .order-header {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .page-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 300;
        }

        .order-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .meta-item {
            padding: 1rem;
            background: var(--cream);
            border-radius: 10px;
        }

        .meta-label {
            font-size: 0.85rem;
            color: #7A7A7A;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .meta-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--charcoal);
        }

        .status-badge {
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
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

        .order-items {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        .item {
            display: grid;
            grid-template-columns: 80px 1fr auto;
            gap: 2rem;
            padding: 2rem 0;
            border-bottom: 1px solid #E0E0E0;
        }

        .item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 80px;
            height: 80px;
            background: var(--cream);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            color: var(--sage);
        }

        .item-details h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.4rem;
            margin-bottom: 0.3rem;
        }

        .item-brand {
            color: var(--sage);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .item-desc {
            color: #7A7A7A;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .item-pricing {
            text-align: right;
        }

        .item-price {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--charcoal);
            margin-bottom: 0.5rem;
        }

        .item-qty {
            color: #7A7A7A;
            font-size: 0.9rem;
        }

        .item-subtotal {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--terracotta);
            margin-top: 0.5rem;
        }

        .order-summary {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            font-size: 1.1rem;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            padding-top: 1.5rem;
            margin-top: 1rem;
            border-top: 2px solid var(--sage);
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--terracotta);
        }

        @media (max-width: 768px) {
            .item {
                grid-template-columns: 60px 1fr;
            }

            .item-pricing {
                grid-column: 1 / -1;
                text-align: left;
                margin-top: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav>
        <a href="index.php" class="logo">AURORA</a>
        <a href="my_orders.php" class="back-btn">← Back to Orders</a>
    </nav>

    <div class="container">
        <div class="order-header">
            <h1 class="page-title">Order #<?= $orderId ?></h1>
            
            <div class="order-meta">
                <div class="meta-item">
                    <div class="meta-label">Order Date</div>
                    <div class="meta-value">
                        <?= date('M j, Y', strtotime($order['OrderDate'])) ?>
                    </div>
                </div>
                
                <div class="meta-item">
                    <div class="meta-label">Status</div>
                    <div class="meta-value">
                        <span class="status-badge status-<?= strtolower($order['status']) ?>">
                            <?= htmlspecialchars($order['status']) ?>
                        </span>
                    </div>
                </div>
                
                <div class="meta-item">
                    <div class="meta-label">Total Amount</div>
                    <div class="meta-value" style="color: var(--terracotta);">
                        $<?= number_format($order['TotalPrice'], 2) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="order-items">
            <h2 class="section-title">Order Items</h2>
            
            <?php foreach ($orderItems as $item): ?>
                <div class="item">
                    <div class="item-image">
                        <?= htmlspecialchars(substr($item['name'], 0, 1)) ?>
                    </div>
                    
                    <div class="item-details">
                        <div class="item-brand"><?= htmlspecialchars($item['brand']) ?></div>
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <p class="item-desc"><?= htmlspecialchars($item['Description']) ?></p>
                    </div>
                    
                    <div class="item-pricing">
                        <div class="item-price">$<?= number_format($item['price'], 2) ?></div>
                        <div class="item-qty">Quantity: <?= $item['quantity'] ?></div>
                        <div class="item-subtotal">
                            $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="order-summary">
            <h2 class="section-title">Order Summary</h2>
            
            <?php
            $subtotal = 0;
            foreach ($orderItems as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $tax = $subtotal * 0.1;
            ?>
            
            <div class="summary-row">
                <span>Subtotal</span>
                <span>$<?= number_format($subtotal, 2) ?></span>
            </div>
            
            <div class="summary-row">
                <span>Shipping</span>
                <span>Free</span>
            </div>
            
            <div class="summary-row">
                <span>Tax (10%)</span>
                <span>$<?= number_format($tax, 2) ?></span>
            </div>
            
            <div class="summary-total">
                <span>Total</span>
                <span>$<?= number_format($order['TotalPrice'], 2) ?></span>
            </div>
        </div>
    </div>
</body>
</html>
