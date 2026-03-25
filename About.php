<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Aurora Skin</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600;700&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --cream: #92d7ff;
            --sage: #025165;
            --terracotta: #0028ef;
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
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--sage);
        }

        .hero {
            margin-top: 80px;
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(139, 157, 131, 0.1) 0%, rgba(212, 165, 116, 0.1) 100%);
            text-align: center;
        }

        .hero h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 5rem;
            font-weight: 300;
            color: var(--charcoal);
        }

        .content {
            max-width: 1000px;
            margin: 6rem auto;
            padding: 0 5%;
        }

        .story {
            background: white;
            border-radius: 20px;
            padding: 4rem;
            margin-bottom: 4rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
        }

        .story h2 {
            font-family: 'khmer os siemreap', serif;
            font-size: 3rem;
            margin-bottom: 2rem;
            font-weight: 300;
            color: var(--sage);
        }

        .story p {
            font-size: 1.1rem;
            line-height: 2;
            color: #5A5A5A;
            margin-bottom: 1.5rem;
        }

        .values {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 3rem;
            margin: 6rem 0;
        }

        .value-card {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }

        .value-card:hover {
            transform: translateY(-10px);
        }

        .value-icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
        }

        .value-card h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            margin-bottom: 1rem;
            font-weight: 300;
        }

        .value-card p {
            color: #7A7A7A;
            line-height: 1.8;
        }

        .team {
            text-align: center;
            padding: 6rem 0;
        }

        .team h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3.5rem;
            margin-bottom: 1rem;
            font-weight: 300;
        }

        .team p {
            font-size: 1.1rem;
            color: #7A7A7A;
            margin-bottom: 4rem;
        }

        footer {
            background: var(--charcoal);
            color: white;
            padding: 3rem 5%;
            text-align: center;
        }

        footer a {
            color: var(--sage);
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 3rem;
            }

            .story {
                padding: 2.5rem;
            }

            .nav-links {
                display: none;
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

    <section class="hero">
        <h1>Our Story</h1>
    </section>

    <div class="content">
        <div class="story">
            <h2>The Aurora Philosophy</h2>
            <p>
               Aurora Skin កើតចេញពីជំនឿសាមញ្ញមួយ៖ ភាពស្រស់ស្អាតពិតកើតឡើងនៅពេលដែលយើងគោរពទាំងធម្មជាតិ និងវិទ្យាសាស្រ្ត។ 
                ដូចជា aurora borealis ដែលបំភ្លឺមេឃជាមួយនឹងពន្លឺធម្មជាតិ ផលិតផលរបស់យើងត្រូវបានរចនាឡើង បញ្ចេញពន្លឺនៃស្បែករបស់អ្នក។
            </p>
            <p>
                យើងធ្វើដំណើរជុំវិញពិភពលោក ដើម្បីទទួលបានសារធាតុផ្សំពីរុក្ខសាស្ត្រល្អបំផុត បន្ទាប់មកផ្សំវាជាមួយ 
                ការស្រាវជ្រាវរោគសើស្បែកទំនើប។ រូបមន្តនីមួយៗត្រូវបានរៀបចំឡើងដោយចេតនា ធាតុផ្សំនីមួយៗ 
                ជ្រើសរើសសម្រាប់ប្រសិទ្ធភាព និងភាពបរិសុទ្ធរបស់វា។
            </p>
            <p>
                
                ការប្តេជ្ញាចិត្តរបស់យើងលើសពីស្បែកដ៏ស្រស់ស្អាត។ យើងជឿជាក់លើភាពស្រស់ស្អាតប្រកបដោយនិរន្តរភាព—ផលិតផលដែលចិញ្ចឹម 
                អ្នកខណៈពេលដែលគោរពភពផែនដីរបស់យើង។ ពី​ការ​វេច​ខ្ចប់​ដោយ​មិន​ដឹង​ខ្លួន​ទៅ​ជា​ទម្រង់​គ្មាន​ភាព​ឃោរឃៅ យើង​មាន 
                ស្រមៃមើលឡើងវិញនូវអ្វីដែលការថែរក្សាស្បែកប្រណីតអាចជា។
            </p>
        </div>

        <div class="values">
            <div class="value-card">
                <div class="value-icon">🌿</div>
                <h3>Natural Purity</h3>
                <p>យើងប្រភពគ្រឿងផ្សំរុក្ខសាស្ត្រល្អបំផុតពីកសិដ្ឋានប្រកបដោយនិរន្តរភាពជុំវិញពិភពលោក ដោយធានាបាននូវភាពបរិសុទ្ធគ្រប់ដំណក់.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">🔬</div>
                <h3>Scientific Innovation</h3>
                <p>រូបមន្តរបស់យើងរួមបញ្ចូលគ្នានូវប្រាជ្ញាបុរាណជាមួយនឹងវិទ្យាសាស្ត្រសើស្បែកទំនើបដើម្បីទទួលបានលទ្ធផលដែលអាចមើលឃើញ.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">♻️</div>
                <h3>Sustainability</h3>
                <p>ពីការវេចខ្ចប់ដែលអាចកែច្នៃឡើងវិញបានរហូតដល់ការដឹកជញ្ជូនកាបូនអព្យាក្រឹត យើងប្តេជ្ញាការពារភពផែនដីដ៏ស្រស់ស្អាតរបស់យើង។.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">🐰</div>
                <h3>Cruelty-Free</h3>
                <p>ផលិតផលរបស់យើងទាំងអស់ត្រូវបានបញ្ជាក់ដោយគ្មានភាពឃោរឃៅ។ សម្រស់​មិន​គួរ​មក​ពី​មិត្ត​សត្វ​យើង​ឡើយ។.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">✨</div>
                <h3>Luxury Experience</h3>
                <p>Eផលិតផលខ្លាំងណាស់ត្រូវបានរចនាឡើងដើម្បីបំប្លែងទម្លាប់ថែរក្សាស្បែករបស់អ្នកទៅជាពេលមួយនៃការមើលថែខ្លួនឯងដោយព្រងើយកន្តើយ.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">💚</div>
                <h3>Transparency</h3>
                <p>យើងជឿជាក់លើតម្លាភាពនៃធាតុផ្សំពេញលេញ។ អ្នកសមនឹងដឹងយ៉ាងច្បាស់នូវអ្វីដែលអ្នកកំពុងលាបលើស្បែករបស់អ្នក។.</p>
            </div>
        </div>

        <div class="team">
            <h2>Join Our Journey</h2>
            <p>ស្វែងយល់ពីសិល្បៈនៃស្បែកភ្លឺថ្លា និងមានសុខភាពល្អជាមួយ Aurora ។ ការផ្លាស់ប្តូររបស់អ្នកចាប់ផ្តើមនៅទីនេះ.</p>
            <a href="products.php" style="display: inline-block; padding: 1.2rem 3rem; background: var(--sage); color: white; text-decoration: none; border-radius: 50px; font-weight: 600; letter-spacing: 1px; margin-top: 2rem;">EXPLORE PRODUCTS</a>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Aurora Skin ។ រក្សាសិទ្ធិគ្រប់យ៉ាង. | <a href="index.php">Home</a> | <a href="products.php">Shop</a> | <a href="contact.php">Contact</a></p>
    </footer>
</body>
</html>