<?php
require_once 'config.php';

// Initialize cart in session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $productId = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity'] ?? 1);
        
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
    } elseif ($action === 'update') {
        $productId = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        
        if ($quantity > 0) {
            $_SESSION['cart'][$productId] = $quantity;
        } else {
            unset($_SESSION['cart'][$productId]);
        }
    } elseif ($action === 'remove') {
        $productId = intval($_POST['product_id']);
        unset($_SESSION['cart'][$productId]);
    } elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
    }
}

// Get cart items
$cartItems = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $conn = getDBConnection();
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $stmt = $conn->prepare("SELECT * FROM product WHERE id IN ($placeholders)");
    $types = str_repeat('i', count($ids));
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($product = $result->fetch_assoc()) {
        $product['cart_quantity'] = $_SESSION['cart'][$product['id']];
        $product['subtotal'] = $product['price'] * $product['cart_quantity'];
        $total += $product['subtotal'];
        $cartItems[] = $product;
    }
    
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Aurora Skin</title>
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
            text-align: center;
            margin-bottom: 3rem;
            font-weight: 300;
        }

        .cart-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3rem;
        }

        .cart-items {
            background: white;
            border-radius: 15px;
            padding: 2rem;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 100px 1fr auto;
            gap: 2rem;
            padding: 2rem 0;
            border-bottom: 1px solid #E0E0E0;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 100px;
            height: 100px;
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
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .item-brand {
            color: var(--sage);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .item-price {
            color: var(--terracotta);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .qty-btn {
            width: 30px;
            height: 30px;
            border: 2px solid var(--sage);
            background: white;
            color: var(--sage);
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .qty-btn:hover {
            background: var(--sage);
            color: white;
        }

        .qty-input {
            width: 60px;
            text-align: center;
            border: 2px solid #E0E0E0;
            border-radius: 5px;
            padding: 0.5rem;
            font-size: 1rem;
        }

        .item-actions {
            text-align: right;
        }

        .subtotal {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--charcoal);
            margin-bottom: 1rem;
        }

        .remove-btn {
            background: none;
            border: none;
            color: #d63031;
            cursor: pointer;
            text-decoration: underline;
            font-size: 0.9rem;
        }

        .cart-summary {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            height: fit-content;
            position: sticky;
            top: 120px;
        }

        .summary-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            padding-top: 1.5rem;
            margin-top: 1.5rem;
            border-top: 2px solid var(--sage);
            font-size: 1.5rem;
            font-weight: 600;
        }

        .checkout-btn {
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
            margin-top: 2rem;
            transition: all 0.3s;
        }

        .checkout-btn:hover {
            background: #7A8C75;
            transform: translateY(-2px);
        }

        .continue-shopping {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: var(--sage);
            text-decoration: none;
        }

        .empty-cart {
            text-align: center;
            padding: 6rem 2rem;
            background: white;
            border-radius: 15px;
        }

        .empty-cart h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 300;
        }

        .empty-cart p {
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
            .cart-grid {
                grid-template-columns: 1fr;
            }

            .cart-item {
                grid-template-columns: 80px 1fr;
            }

            .item-actions {
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
        <div class="nav-links">
            <a href="products.php">Continue Shopping</a>
            <a href="index.php">Home</a>
        </div>
    </nav>

    <div class="container">
        <h1 class="page-title">Shopping Cart</h1>

        <?php if (empty($cartItems)): ?>
            <div class="empty-cart">
                <h2>Your cart is empty</h2>
                <p>Discover our collection of premium skincare products</p>
                <a href="products.php" class="shop-btn">START SHOPPING</a>
            </div>
        <?php else: ?>
            <div class="cart-grid">
                <div class="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <div class="item-image">
                                <?= htmlspecialchars(substr($item['name'], 0, 1)) ?>
                            </div>
                            
                            <div class="item-details">
                                <div class="item-brand"><?= htmlspecialchars($item['brand']) ?></div>
                                <h3><?= htmlspecialchars($item['name']) ?></h3>
                                <div class="item-price">$<?= number_format($item['price'], 2) ?></div>
                                
                                <form method="POST" class="quantity-control">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <button type="button" class="qty-btn" onclick="updateQty(<?= $item['id'] ?>, -1)">-</button>
                                    <input type="number" name="quantity" class="qty-input" 
                                           value="<?= $item['cart_quantity'] ?>" 
                                           min="1" max="<?= $item['StockQty'] ?>"
                                           id="qty-<?= $item['id'] ?>">
                                    <button type="button" class="qty-btn" onclick="updateQty(<?= $item['id'] ?>, 1)">+</button>
                                </form>
                            </div>
                            
                            <div class="item-actions">
                                <div class="subtotal">$<?= number_format($item['subtotal'], 2) ?></div>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="remove-btn">Remove</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <h2 class="summary-title">Order Summary</h2>
                    
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>$<?= number_format($total, 2) ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Tax</span>
                        <span>$<?= number_format($total * 0.1, 2) ?></span>
                    </div>
                    
                    <div class="summary-total">
                        <span>Total</span>
                        <span>$<?= number_format($total * 1.1, 2) ?></span>
                    </div>
                    
                    <a href="checkout.php" class="checkout-btn">PROCEED TO CHECKOUT</a>
                    <a href="products.php" class="continue-shopping">Continue Shopping</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function updateQty(productId, change) {
            const input = document.getElementById('qty-' + productId);
            let newQty = parseInt(input.value) + change;
            const max = parseInt(input.max);
            
            if (newQty < 1) newQty = 1;
            if (newQty > max) newQty = max;
            
            input.value = newQty;
            
            // Submit the form
            const form = input.closest('form');
            const formData = new FormData();
            formData.append('action', 'update');
            formData.append('product_id', productId);
            formData.append('quantity', newQty);
            
            fetch('cart.php', {
                method: 'POST',
                body: formData
            }).then(() => {
                location.reload();
            });
        }
    </script>
</body>
</html>