<?php
require_once 'config.php';

// Simple admin authentication (in production, use proper authentication)
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

if (isset($_POST['admin_login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Simple check (replace with database check in production)
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin'] = true;
        header('Location: admin.php');
        exit();
    }
}

if (isset($_GET['logout'])) {
    unset($_SESSION['admin']);
    header('Location: admin.php');
    exit();
}

if (!$isAdmin) {
    // Show login form
    ?>
    <!DOCTYPE html>
    <html lang="km">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - Aurora Skin</title>
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'Montserrat', sans-serif;
                background: #FAF7F2;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .login-box {
                background: white;
                padding: 3rem;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.1);
                width: 400px;
            }
            h2 {
                font-family: 'Cormorant Garamond', serif;
                font-size: 2.5rem;
                margin-bottom: 2rem;
                text-align: center;
            }
            input {
                width: 100%;
                padding: 1rem;
                margin-bottom: 1rem;
                border: 2px solid #E0E0E0;
                border-radius: 8px;
                font-size: 1rem;
            }
            button {
                width: 100%;
                padding: 1rem;
                background: #8B9D83;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h2>Admin Login</h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="admin_login">Login</button>
            </form>
            <p style="text-align: center; margin-top: 1rem; color: #999;">Default: admin / admin123</p>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Get statistics
$conn = getDBConnection();

$totalProducts = $conn->query("SELECT COUNT(*) as count FROM product")->fetch_assoc()['count'];
$totalOrders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM user")->fetch_assoc()['count'];
$openComplaints = $conn->query("SELECT COUNT(*) as count FROM complaints WHERE status = 'Open'")->fetch_assoc()['count'];

// Get recent data
$recentOrders = $conn->query("SELECT o.*, u.UserName FROM orders o LEFT JOIN user u ON o.UserId = u.id ORDER BY o.OrderDate DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
$recentComplaints = $conn->query("SELECT c.*, u.UserName FROM complaints c LEFT JOIN user u ON c.UserId = u.id ORDER BY c.created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Aurora Skin</title>
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

        .header {
            background: white;
            padding: 1.5rem 3%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            letter-spacing: 2px;
        }

        .logout-btn {
            padding: 0.8rem 2rem;
            background: var(--sage);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
        }

        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 3%;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .stat-number {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3rem;
            color: var(--sage);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #7A7A7A;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: var(--cream);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--charcoal);
            border-bottom: 2px solid var(--sage);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #E0E0E0;
        }

        tr:hover {
            background: #F9F9F9;
        }

        .badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .badge-completed {
            background: #d4edda;
            color: #155724;
        }

        .badge-open {
            background: #ffe6e6;
            color: #d63031;
        }

        .badge-resolved {
            background: #d4edda;
            color: #155724;
        }

        .quick-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-btn {
            padding: 1rem 2rem;
            background: var(--sage);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            display: inline-block;
        }

        .action-btn:hover {
            background: #7A8C75;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">AURORA ADMIN</div>
        <a href="admin.php?logout=1" class="logout-btn">Logout</a>
    </div>

    <div class="container">
        <div class="quick-actions">
            <a href="admin_products.php" class="action-btn">Manage Products</a>
            <a href="admin_orders.php" class="action-btn">View All Orders</a>
            <a href="admin_users.php" class="action-btn">Manage Users</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $totalProducts ?></div>
                <div class="stat-label">Total Products</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $totalOrders ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $totalUsers ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $openComplaints ?></div>
                <div class="stat-label">Open Complaints</div>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Recent Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['UserName']) ?></td>
                            <td>$<?= number_format($order['TotalPrice'], 2) ?></td>
                            <td>
                                <span class="badge badge-<?= strtolower($order['status']) ?>">
                                    <?= htmlspecialchars($order['status']) ?>
                                </span>
                            </td>
                            <td><?= date('M j, Y', strtotime($order['OrderDate'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2 class="section-title">Recent Complaints</h2>
            <table>
                <thead>
                    <tr>
                        <th>Complaint #</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentComplaints as $complaint): ?>
                        <tr>
                            <td><?= htmlspecialchars($complaint['CompNumber']) ?></td>
                            <td><?= htmlspecialchars($complaint['UserName']) ?></td>
                            <td><?= htmlspecialchars($complaint['type']) ?></td>
                            <td>
                                <span class="badge badge-<?= strtolower($complaint['status']) ?>">
                                    <?= htmlspecialchars($complaint['status']) ?>
                                </span>
                            </td>
                            <td><?= date('M j, Y', strtotime($complaint['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
