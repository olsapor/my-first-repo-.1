<?php
require_once 'config.php';

$conn = getDBConnection();
$category = $_GET['category'] ?? '';

if ($category) {
    $stmt = $conn->prepare("SELECT * FROM product WHERE category = ? ORDER BY product_id DESC");
    $stmt->bind_param("s", $category);
} else {
    $stmt = $conn->prepare("SELECT * FROM product ORDER BY product_id DESC");
}

$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Aurora Skin</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600;700&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
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
            --white: #FFFFFF;
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

        .nav-links {
            display: flex;
            gap: 3rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--charcoal);
            font-weight: 400;
            font-size: 0.95rem;
            letter-spacing: 1px;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--sage);
        }

        .nav-actions {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-actions a {
            text-decoration: none;
            color: var(--charcoal);
            font-size: 0.95rem;
        }

        .page-header {
            margin-top: 100px;
            padding: 4rem 5%;
            text-align: center;
            background: white;
        }

        .page-header h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 4rem;
            font-weight: 300;
            margin-bottom: 1rem;
        }

        .page-header p {
            color: #7A7A7A;
            font-size: 1.1rem;
        }

        .filter-section {
            padding: 2rem 5%;
            background: white;
            border-bottom: 1px solid rgba(139, 157, 131, 0.2);
        }

        .filters {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.8rem 2rem;
            border: 2px solid var(--sage);
            background: transparent;
            color: var(--sage);
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--sage);
            color: white;
        }

        .products-container {
            padding: 4rem 5%;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 3rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.4s;
            cursor: pointer;
            border: 1px solid rgba(139, 157, 131, 0.1);
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            width: 100%;
            height: 350px;
            background: linear-gradient(135deg, var(--cream) 0%, #E8E3DA 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .product-placeholder {
            font-family: 'Cormorant Garamond', serif;
            font-size: 4rem;
            color: var(--sage);
            opacity: 0.3;
        }

        .product-info {
            padding: 1.8rem;
        }

        .product-brand {
            font-size: 0.8rem;
            color: var(--sage);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .product-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            margin-bottom: 0.8rem;
            color: var(--charcoal);
        }

        .product-desc {
            font-size: 0.9rem;
            color: #7A7A7A;
            line-height: 1.6;
            margin-bottom: 1.2rem;
            height: 60px;
            overflow: hidden;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid rgba(139, 157, 131, 0.1);
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--terracotta);
        }

        .stock-info {
            font-size: 0.85rem;
            color: var(--sage);
        }

        .add-to-cart {
            width: 100%;
            margin-top: 1rem;
            padding: 0.8rem;
            background: var(--sage);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .add-to-cart:hover {
            background: #7A8C75;
        }

        .empty-state {
            text-align: center;
            padding: 6rem 2rem;
            color: #7A7A7A;
        }

        .empty-state h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }

            .page-header h1 {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <nav>
        <a href="index.php" class="logo">AURORA</a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
        <div class="nav-actions">
            <a href="login.php">Login</a>
            <a href="cart.php">Cart</a>
        </div>
    </nav>

    <div class="page-header">
        <h1>Our Collection</h1>
        <p>Discover products crafted for your unique beauty journey</p>
    </div>

    <div class="filter-section">
        <div class="filters">
            <a href="products.php" class="filter-btn <?= !$category ? 'active' : '' ?>">All Products</a>
            <a href="products.php?category=Serums" class="filter-btn <?= $category === 'Serums' ? 'active' : '' ?>">Serums</a>
            <a href="products.php?category=Moisturizers" class="filter-btn <?= $category === 'Moisturizers' ? 'active' : '' ?>">Moisturizers</a>
            <a href="products.php?category=Cleansers" class="filter-btn <?= $category === 'Cleansers' ? 'active' : '' ?>">Cleansers</a>
            <a href="products.php?category=Masks" class="filter-btn <?= $category === 'Masks' ? 'active' : '' ?>">Masks</a>
            <a href="products.php?category=Sunscreen" class="filter-btn <?= $category === 'Sunscreen' ? 'active' : '' ?>">Sunscreen</a>
        </div>
    </div>

    <div class="products-container">
        <?php if (empty($products)): ?>
            <div class="empty-state">
                <h2>No products found</h2>
                <p>Check back soon for new arrivals</p>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
    <?php if (!empty($product['image_url'])): ?>
        <img src="image/products/<?php echo $product['image_url']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
    <?php else: ?>
        <div class="product-placeholder"><?php echo htmlspecialchars(substr($product['name'], 0, 1)); ?></div>
    <?php endif; ?>
</div>
                        <div class="product-info">
                            <div class="product-brand"><?= htmlspecialchars($product['brand']) ?></div>
                            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="product-desc"><?= htmlspecialchars($product['description']) ?></p>
                            <div class="product-footer">
                                <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                                <div class="stock-info">In Stock: <?= $product['stock_qty'] ?></div>
                            </div>
                            <button class="add-to-cart" onclick="addToCart(<?= $product['product_id'] ?>)">Add to Cart</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function addToCart(productId) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);
            
            fetch('api/add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    // Redirect to cart page or update UI
                    if (confirm('Product added! Go to cart?')) {
                        window.location.href = 'cart.php';
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to add product to cart');
            });
        }
    </script>
</body>
</html>
