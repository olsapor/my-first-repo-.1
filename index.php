<?php session_start(); ?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurora Skincare Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600;700&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --cream: #f4f4ff;
            --sage: #c3c3ff;
            --terracotta: #a269ff;
            --charcoal: #4a4a4a;
            --white: #FFFFFF;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--cream);
            color: var(--charcoal);
            overflow-x: hidden;
        }
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.2rem 8%;
            background: var(--white);
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        .logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 2px;
            color: var(--terracotta);
        }
        .nav-links { display: flex; list-style: none; gap: 2.5rem; }
        .nav-links a { text-decoration: none; color: var(--charcoal); font-weight: 500; transition: 0.3s; }
        .nav-links a:hover { color: var(--terracotta); }
        .nav-actions { display: flex; align-items: center; gap: 1.5rem; }
        .cart-icon {
            text-decoration: none;
            color: var(--charcoal);
            font-size: 1.2rem;
            position: relative;
            display: flex;
            align-items: center;
        }
        .cart-count {
            background: #ff4757;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            position: absolute;
            top: -10px;
            right: -12px;
            font-weight: bold;
        }
        .user-welcome { font-weight: 600; color: var(--terracotta); }
        .auth-link {
            text-decoration: none;
            color: var(--charcoal);
            padding: 8px 16px;
            border-radius: 5px;
            transition: 0.3s;
        }
        .btn-logout { color: #ff4757; font-weight: 600; text-decoration: none; }
        .hero {
            height: 90vh;
            display: flex;
            align-items: center;
            padding: 0 8%;
            margin-top: 60px;
            background: linear-gradient(rgba(255,255,255,0.3), rgba(255,255,255,0.3)), url('https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&w=1600&q=80');
            background-size: cover;
            background-position: center;
        }
        .hero h1 { font-family: 'Cormorant Garamond', serif; font-size: 4.5rem; color: var(--terracotta); }
        .cta-button {
            display: inline-block;
            padding: 1.2rem 2.5rem;
            background: var(--terracotta);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(162, 105, 255, 0.3);
            transition: 0.3s;
        }
        .products-section { padding: 5rem 8%; background: white; }
        .section-header h2 { font-family: 'Cormorant Garamond', serif; font-size: 2.5rem; text-align: center; margin-bottom: 3rem; }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2.5rem; }
        .product-card { background: white; border-radius: 15px; padding: 1.5rem; box-shadow: 0 5px 20px rgba(0,0,0,0.05); transition: 0.3s; }
        .product-card:hover { transform: translateY(-5px); }
        .btn-buy { width: 100%; padding: 0.8rem; background: var(--terracotta); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; margin-top: 15px; }
    </style>
</head>
<body>

    <nav>
        <div class="logo">AURORA</div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
        <div class="nav-actions">
            <a href="cart.php" class="cart-icon">
                🛒 <span class="cart-count">0</span>
            </a>
            <?php if(isset($_SESSION['user_name'])): ?>
                <span class="user-welcome">សួស្តី, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <a href="logout.php" class="btn-logout">Logout</a>
            <?php else: ?>
                <a href="login.php" class="auth-link">Login</a>
                <a href="register.php" class="auth-link" style="background: var(--sage); color: white;">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1>In joy your day</h1>
            <p>ស្វែងយល់ពីសិល្បៈនៃសម្រស់ធម្មជាតិជាមួយនឹងការថែទាំស្បែកលំដាប់ថ្នាក់របស់យើង។</p><br>
            <a href="products.php" class="cta-button">EXPLORE COLLECTION</a>
        </div>
    </section>

    <section class="products-section">
        <div class="section-header"><h2>ផលិតផលពិសេសសម្រាប់អ្នក</h2></div>
        <div class="products-grid" id="productsContainer"></div>
    </section>

    <script>
        // ១. ទាញយកទិន្នន័យផលិតផលមកបង្ហាញ
        async function loadProducts() {
            try {
                const response = await fetch('get_products.php?limit=6');
                const products = await response.json();
                const container = document.getElementById('productsContainer');
                
                container.innerHTML = products.map(product => `
                    <div class="product-card">
                        <div style="background:#fcfaff; height:220px; display:flex; align-items:center; justify-content:center; border-radius:10px;">
                            <span style="font-size:4rem; opacity:0.2;">✨</span>
                        </div>
                        <div style="margin-top:15px;">
                            <small style="color:var(--terracotta); font-weight:600;">${product.brand}</small>
                            <h3 style="margin:5px 0;">${product.name}</h3>
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <span style="font-size:1.2rem; font-weight:700;">$${parseFloat(product.price).toFixed(2)}</span>
                                <span style="font-size:0.8rem; color:gray;">Stock: ${product.stock}</span>
                            </div>
                            <button class="btn-buy" onclick="addToCart(${product.product_id || product.id})">Add to Cart</button>
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error loading products:', error);
            }
        }

        // ២. អាប់ដេតលេខកន្ត្រកទំនិញ
        function updateCartCount() {
            fetch('get_cart_count.php')
                .then(response => response.json())
                .then(data => {
                    const cartCount = document.querySelector('.cart-count');
                    if(cartCount) cartCount.textContent = data.count || 0;
                })
                .catch(err => console.error('Error updating cart count:', err));
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadProducts();
            updateCartCount();
        });
    </script>
    <script src="java.js?v=1"></script>
</body>
</html>