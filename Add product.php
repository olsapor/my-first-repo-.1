<?php
// Database connection
$host = 'localhost';
$dbname = 'skincare_shop_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $brand = trim($_POST['brand']);
    $price = floatval($_POST['price']);
    $stock_qty = intval($_POST['stock_qty']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    
    // Validation
    if (empty($name) || empty($brand) || empty($category)) {
        $message = 'Please fill in all required fields!';
        $messageType = 'error';
    } elseif ($price <= 0) {
        $message = 'Price must be greater than 0!';
        $messageType = 'error';
    } elseif ($stock_qty < 0) {
        $message = 'Stock quantity cannot be negative!';
        $messageType = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO product (name, brand, price, stock_qty, description, category) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $brand, $price, $stock_qty, $description, $category]);
            
            $message = 'Product added successfully!';
            $messageType = 'success';
            
            // Clear form
            $_POST = array();
        } catch(PDOException $e) {
            $message = 'Error adding product: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Get all products for display
try {
    $stmt = $pdo->query("SELECT * FROM product ORDER BY product_id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $products = array();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Aurora Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        label .required {
            color: #e74c3c;
        }
        
        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            width: 100%;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .products-table th,
        .products-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .products-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .products-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .category-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .category-Cleansers, .category-Cleansers2 {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        
        .category-Serums {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }
        
        .category-Masks {
            background-color: #fff3e0;
            color: #f57c00;
        }
        
        .category-Moisturizers {
            background-color: #e8f5e9;
            color: #388e3c;
        }
        
        .category-Sunscreen {
            background-color: #fff9c4;
            color: #f57f17;
        }
        
        .back-link {
            display: inline-block;
            color: white;
            text-decoration: none;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            transition: background 0.3s;
        }
        
        .back-link:hover {
            background: rgba(255,255,255,0.3);
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .products-table {
                font-size: 14px;
            }
            
            .products-table th,
            .products-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">← Back to Website</a>
        
        <div class="header">
            <h1>🌸 Aurora Admin Panel</h1>
            <p>Add New Product</p>
        </div>
        
        <div class="card">
            <h2 style="margin-bottom: 20px; color: #667eea;">Product Information</h2>
            
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label>Product Name <span class="required">*</span></label>
                        <input type="text" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Brand <span class="required">*</span></label>
                        <input type="text" name="brand" required value="<?php echo isset($_POST['brand']) ? htmlspecialchars($_POST['brand']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Price ($) <span class="required">*</span></label>
                        <input type="number" name="price" step="0.01" min="0" required value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Stock Quantity <span class="required">*</span></label>
                        <input type="number" name="stock_qty" min="0" required value="<?php echo isset($_POST['stock_qty']) ? htmlspecialchars($_POST['stock_qty']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Category <span class="required">*</span></label>
                    <select name="category" required>
                        <option value="">Select a category</option>
                        <option value="Cleansers" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Cleansers') ? 'selected' : ''; ?>>Cleansers</option>
                        <option value="Serums" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Serums') ? 'selected' : ''; ?>>Serums</option>
                        <option value="Moisturizers" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Moisturizers') ? 'selected' : ''; ?>>Moisturizers</option>
                        <option value="Masks" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Masks') ? 'selected' : ''; ?>>Masks</option>
                        <option value="Sunscreen" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Sunscreen') ? 'selected' : ''; ?>>Sunscreen</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Enter product description..."><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                
                <button type="submit" class="btn">Add Product</button>
            </form>
        </div>
        
        <div class="card">
            <h2 style="margin-bottom: 20px; color: #667eea;">Current Products (<?php echo count($products); ?>)</h2>
            
            <?php if (empty($products)): ?>
                <p style="text-align: center; color: #999; padding: 20px;">No products found.</p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Category</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['brand']); ?></td>
                                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($product['stock_qty']); ?></td>
                                    <td>
                                        <span class="category-badge category-<?php echo htmlspecialchars($product['category']); ?>">
                                            <?php echo htmlspecialchars($product['category']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . (strlen($product['description']) > 50 ? '...' : ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
