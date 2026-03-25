<?php
require_once 'config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // In production, send email here
    $success = 'សូមអរគុណសម្រាប់ការទាក់ទងមកយើងខ្ញុំ! យើងនឹងត្រលប់មកអ្នកវិញក្នុងពេលឆាប់ៗនេះ.';
}
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Aurora Skin</title>
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
        }

        .container {
            max-width: 1200px;
            margin: 120px auto 3rem;
            padding: 0 5%;
        }

        .page-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 4rem;
            text-align: center;
            margin-bottom: 1rem;
            font-weight: 300;
        }

        .subtitle {
            text-align: center;
            color: #7A7A7A;
            font-size: 1.1rem;
            margin-bottom: 4rem;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
        }

        .contact-info {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
        }

        .info-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        .info-item {
            margin-bottom: 2rem;
        }

        .info-label {
            font-weight: 600;
            color: var(--sage);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-value {
            font-size: 1.1rem;
            color: var(--charcoal);
        }

        .contact-form {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
        }

        .form-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
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

        input, textarea, select {
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
            min-height: 150px;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: var(--sage);
        }

        .submit-btn {
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
            transition: all 0.3s;
        }

        .submit-btn:hover {
            background: #7A8C75;
            transform: translateY(-2px);
        }

        .success-message {
            background: #e6f7e6;
            color: #27ae60;
            padding: 1.2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .map {
            margin-top: 4rem;
            background: white;
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 968px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }

            .nav-links {
                display: none;
            }

            .page-title {
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
    </nav>

    <div class="container">
        <h1 class="page-title">Get In Touch</h1>
        <p class="subtitle">យើងចង់ឮពីអ្នក។ ផ្ញើសារមកយើង ហើយយើងនឹងឆ្លើយតបឱ្យបានឆាប់តាមដែលអាចធ្វើទៅបាន។.</p>

        <div class="contact-grid">
            <div class="contact-info">
                <h2 class="info-title">Contact Information</h2>
                
                <div class="info-item">
                    <div class="info-label">Address</div>
                    <div class="info-value">
                        Street 123, Phnom Penh<br>
                        Kingdom of Cambodia
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">
                        hello@auroraskin.com<br>
                        support@auroraskin.com
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Phone</div>
                    <div class="info-value">
                        +855 12 345 678<br>
                        +855 98 765 432
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Business Hours</div>
                    <div class="info-value">
                        ថ្ងៃចន្ទ - Friday: 9:00 AM - 6:00 PM<br>
                        ថ្ងៃសៅរ៍: 10:00 AM - 4:00 PM<br>
                        ថ្ងៃអាទិត្យ: Closed
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Follow Us</div>
                    <div class="info-value">
                        Facebook | Instagram | 
                    </div>
                </div>
            </div>

            <div class="contact-form">
                <h2 class="form-title">Send Message</h2>
                
                <?php if ($success): ?>
                    <div class="success-message"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <select id="subject" name="subject" required>
                            <option value="">Select a subject...</option>
                            <option value="Product Inquiry">Product Inquiry</option>
                            <option value="Order Support">Order Support</option>
                            <option value="Partnership">Partnership Opportunity</option>
                            <option value="Feedback">Feedback</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" required placeholder="Tell us how we can help you..."></textarea>
                    </div>

                    <button type="submit" class="submit-btn">SEND MESSAGE</button>
                </form>
            </div>
        </div>

        <div class="map">
            <h2 class="info-title">Visit Our Store</h2>
            <p style="color: #7A7A7A; margin-bottom: 2rem;">
                សូមអញ្ជើញមកហាងយើងខ្ញុំនៅចំកណ្តាលទីក្រុងភ្នំពេញ។ 
                អ្នកប្រឹក្សាសម្រស់របស់យើងនៅទីនេះដើម្បីជួយអ្នកស្វែងរកផលិតផលដែលល្អឥតខ្ចោះសម្រាប់ស្បែករបស់អ្នក។.
            </p>
            <div style="background: var(--cream); padding: 4rem; border-radius: 15px;">
                <p style="font-size: 1.2rem; color: var(--sage);">📍 ការរួមបញ្ចូលផែនទីអាចប្រើបាន</p>
            </div>
        </div>
    </div>
</body>
</html>