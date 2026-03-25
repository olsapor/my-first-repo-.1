<?php
require_once 'config.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

if (!$isLoggedIn) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

$success = '';
$error = '';

// Get cart items
$conn = getDBConnection();
$ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$stmt = $conn->prepare("SELECT * FROM product WHERE id IN ($placeholders)");
$types = str_repeat('i', count($ids));
$stmt->bind_param($types, ...$ids);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
$total = 0;

while ($product = $result->fetch_assoc()) {
    $product['cart_quantity'] = $_SESSION['cart'][$product['id']];
    $product['subtotal'] = $product['price'] * $product['cart_quantity'];
    $total += $product['subtotal'];
    $cartItems[] = $product;
}

// Get user info
$stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Process checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'] ?? $user['Address'];
    $phone = $_POST['phone'] ?? $user['phone'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Create order
        $orderTotal = $total * 1.1; // Including 10% tax
        $stmt = $conn->prepare("INSERT INTO orders (UserId, TotalPrice, status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param("id", $_SESSION['user_id'], $orderTotal);
        $stmt->execute();
        $orderId = $conn->insert_id;
        
        // Add order items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($cartItems as $item) {
            $stmt->bind_param("iiid", $orderId, $item['id'], $item['cart_quantity'], $item['price']);
            $stmt->execute();
            
            // Update stock
            $newStock = $item['StockQty'] - $item['cart_quantity'];
            $updateStmt = $conn->prepare("UPDATE product SET StockQty = ? WHERE id = ?");
            $updateStmt->bind_param("ii", $newStock, $item['id']);
            $updateStmt->execute();
        }
        
        $conn->commit();
        
        // Clear cart
        $_SESSION['cart'] = [];
        
        header('Location: order_confirmation.php?order_id=' . $orderId);
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $error = 'Order failed. Please try again.';
    }
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Aurora Skin</title>
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
            max-width: 1200px;
            margin: 3rem auto;
            padding: 0 3%;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 3rem;
        }

        .section {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.95rem;
        }

        input, textarea {
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
            min-height: 100px;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--sage);
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #E0E0E0;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .item-qty {
            color: #7A7A7A;
            font-size: 0.9rem;
        }

        .item-price {
            font-weight: 600;
            color: var(--terracotta);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            padding-top: 1.5rem;
            margin-top: 1rem;
            border-top: 2px solid var(--sage);
            font-size: 1.5rem;
            font-weight: 600;
        }

        .place-order-btn {
            width: 100%;
            padding: 1.3rem;
            background: var(--sage);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 1px;
            cursor: pointer;
            margin-top: 2rem;
            transition: all 0.3s;
        }

        .place-order-btn:hover {
            background: #7A8C75;
            transform: translateY(-2px);
        }

        .error-message {
            background: #ffe6e6;
            color: #d63031;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 968px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav>
        <a href="index.php" class="logo">AURORA</a>
    </nav>

    <div class="container">
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="checkout-grid">
            <div class="section">
                <h2 class="section-title">Shipping Information</h2>
                
                <form method="POST" id="checkoutForm">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" value="<?= htmlspecialchars($user['UserName']) ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" value="<?= htmlspecialchars($user['Email']) ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Shipping Address</label>
                        <textarea id="address" name="address" required><?= htmlspecialchars($user['Address'] ?? '') ?></textarea>
                    </div>

                    <div class="section-title" style="margin-top: 3rem;">Payment Method</div>
                    
                    <div class="form-group">
                        <label>
                            <input type="radio" name="payment" value="cod" checked>
                            Cash on Delivery
                        </label>
                    </div>

                    <button type="submit" class="place-order-btn">PLACE ORDER</button>
                </form>
            </div>

            <div class="section">
                <h2 class="section-title">Order Summary</h2>
                
                <div>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="order-item">
                            <div class="item-info">
                                <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                                <div class="item-qty">Qty: <?= $item['cart_quantity'] ?></div>
                            </div>
                            <div class="item-price">$<?= number_format($item['subtotal'], 2) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>$<?= number_format($total, 2) ?></span>
                </div>

                <div class="summary-row">
                    <span>Shipping</span>
                    <span>Free</span>
                </div>

                <div class="summary-row">
                    <span>Tax (10%)</span>
                    <span>$<?= number_format($total * 0.1, 2) ?></span>
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <span>$<?= number_format($total * 1.1, 2) ?></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
