<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HireHub - ابحث عن وظيفتك المثالية</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Navigation */
        .home-nav {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1.2rem 0;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 1.8rem;
            font-weight: 800;
            color: #667eea;
        }

        .logo i {
            font-size: 2rem;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-link {
            color: #333;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
            font-size: 1.1rem;
        }

        .nav-link:hover {
            color: #667eea;
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-outline {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-outline:hover {
            background: #667eea;
            color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        /* Hero Section */
        .hero {
            padding: 150px 2rem 100px;
            text-align: center;
            color: white;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.8s ease;
        }

        .hero p {
            font-size: 1.4rem;
            margin-bottom: 3rem;
            opacity: 0.95;
            animation: fadeInUp 0.8s ease 0.2s backwards;
        }

        .hero-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            animation: fadeInUp 0.8s ease 0.4s backwards;
        }

        .hero .btn {
            padding: 1.2rem 3rem;
            font-size: 1.2rem;
        }

        .btn-white {
            background: white;
            color: #667eea;
        }

        .btn-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
        }

        /* Search Box */
        .search-box {
            max-width: 700px;
            margin: 4rem auto 0;
            background: white;
            padding: 1rem;
            border-radius: 50px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            display: flex;
            gap: 1rem;
            animation: fadeInUp 0.8s ease 0.6s backwards;
        }

        .search-box input {
            flex: 1;
            border: none;
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
            font-family: 'Cairo', sans-serif;
            outline: none;
            border-radius: 50px;
        }

        .search-box button {
            padding: 1rem 2.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .search-box button:hover {
            transform: scale(1.05);
        }

        /* Features Section */
        .features {
            padding: 100px 2rem;
            background: white;
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 3rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
            margin-top: 4rem;
        }

        .feature-card {
            text-align: center;
            padding: 2.5rem;
            border-radius: 20px;
            background: #f8f9fa;
            transition: all 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: #666;
            line-height: 1.8;
            font-size: 1.1rem;
        }

        /* Stats Section */
        .stats {
            padding: 80px 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stats-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
        }

        .stat-card {
            text-align: center;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        /* How It Works */
        .how-it-works {
            padding: 100px 2rem;
            background: white;
        }

        .steps-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-top: 4rem;
        }

        .step-card {
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 1.8rem;
            font-weight: 800;
        }

        .step-card h3 {
            font-size: 1.4rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .step-card p {
            color: #666;
            line-height: 1.8;
        }

        /* CTA Section */
        .cta {
            padding: 100px 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            text-align: center;
            color: white;
        }

        .cta h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .cta p {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            opacity: 0.95;
        }

        /* Footer */
        .footer {
            background: #1a1a2e;
            color: white;
            padding: 3rem 2rem 1.5rem;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
        }

        .footer-section p,
        .footer-section a {
            color: rgba(255, 255, 255, 0.7);
            line-height: 2;
            text-decoration: none;
            display: block;
        }

        .footer-section a:hover {
            color: white;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background: #667eea;
            transform: translateY(-3px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .search-box {
                flex-direction: column;
                border-radius: 20px;
            }

            .search-box input,
            .search-box button {
                width: 100%;
            }

            .section-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="home-nav">
        <div class="nav-container">
            <div class="logo">
                <i class="fas fa-briefcase"></i>
                <span>HireHub</span>
            </div>
            <div class="nav-links">
                <a href="#features" class="nav-link">المميزات</a>
                <a href="#how-it-works" class="nav-link">كيف يعمل</a>
                <a href="#about" class="nav-link">من نحن</a>
            </div>
            <div class="nav-buttons">
                <a href="index-complete.php?page=login" class="btn btn-outline">تسجيل الدخول</a>
                <a href="index-complete.php?page=register" class="btn btn-primary">إنشاء حساب</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>ابحث عن وظيفتك المثالية</h1>
            <p>منصة توظيف متكاملة تربط بين الباحثين عن عمل وأصحاب العمل</p>
            <div class="hero-buttons">
                <a href="index-complete.php?page=jobs" class="btn btn-white">
                    <i class="fas fa-search"></i> تصفح الوظائف
                </a>
                <a href="index-complete.php?page=register" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> ابدأ الآن
                </a>
            </div>
        </div>

        <!-- Search Box -->
        <div class="search-box">
            <input type="text" placeholder="ابحث عن وظيفة، شركة، أو مكان..." id="homeSearch">
            <button onclick="searchJobs()">
                <i class="fas fa-search"></i> بحث
            </button>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number">500+</div>
                <div class="stat-label">وظيفة متاحة</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">1000+</div>
                <div class="stat-label">باحث عن عمل</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">200+</div>
                <div class="stat-label">شركة موثوقة</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">95%</div>
                <div class="stat-label">نسبة النجاح</div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="features-container">
            <h2 class="section-title">لماذا تختار HireHub؟</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>بحث ذكي</h3>
                    <p>ابحث عن الوظائف المناسبة لك بسهولة باستخدام نظام البحث المتقدم</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>أمان وحماية</h3>
                    <p>بياناتك محمية بأعلى معايير الأمان والخصوصية</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>شركات موثوقة</h3>
                    <p>تواصل مع أفضل الشركات والمؤسسات في المملكة</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>تتبع الطلبات</h3>
                    <p>راقب حالة طلباتك الوظيفية بسهولة</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>إشعارات فورية</h3>
                    <p>احصل على إشعارات بالوظائف الجديدة المناسبة لك</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>سهولة الاستخدام</h3>
                    <p>منصة سهلة وبسيطة للاستخدام على جميع الأجهزة</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works" id="how-it-works">
        <div class="steps-container">
            <h2 class="section-title">كيف يعمل HireHub؟</h2>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3>إنشاء حساب</h3>
                    <p>سجل حسابك مجاناً وأكمل ملفك الشخصي</p>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3>ابحث عن وظيفة</h3>
                    <p>تصفح آلاف الوظائف المتاحة واختر المناسب لك</p>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3>قدم طلبك</h3>
                    <p>قدم على الوظائف بضغطة زر واحدة</p>
                </div>
                <div class="step-card">
                    <div class="step-number">4</div>
                    <h3>احصل على وظيفتك</h3>
                    <p>تواصل مع أصحاب العمل وابدأ مسيرتك المهنية</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <h2>جاهز لبدء رحلتك المهنية؟</h2>
        <p>انضم إلى آلاف الباحثين عن عمل واعثر على وظيفتك المثالية اليوم</p>
        <a href="index-complete.php?page=register" class="btn btn-white">
            <i class="fas fa-user-plus"></i> ابدأ الآن مجاناً
        </a>
    </section>

    <!-- Footer -->
    <footer class="footer" id="about">
        <div class="footer-container">
            <div class="footer-section">
                <h3>HireHub</h3>
                <p>منصة توظيف سعودية متكاملة تهدف لربط الباحثين عن عمل بأفضل الفرص الوظيفية</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-facebook"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>روابط سريعة</h3>
                <a href="index-complete.php?page=jobs">تصفح الوظائف</a>
                <a href="index-complete.php?page=register">إنشاء حساب</a>
                <a href="index-complete.php?page=login">تسجيل الدخول</a>
            </div>
            <div class="footer-section">
                <h3>للشركات</h3>
                <a href="index-complete.php?page=register">نشر وظيفة</a>
                <a href="#">حلول التوظيف</a>
                <a href="#">الأسعار</a>
            </div>
            <div class="footer-section">
                <h3>تواصل معنا</h3>
                <p><i class="fas fa-envelope"></i> <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="721b1c141d321a1b00171a07105c0113">[email&#160;protected]</a></p>
                <p><i class="fas fa-phone"></i> +966 50 123 4567</p>
                <p><i class="fas fa-map-marker-alt"></i> الرياض، المملكة العربية السعودية</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 HireHub. جميع الحقوق محفوظة</p>
        </div>
    </footer>

    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
        function searchJobs() {
            const searchTerm = document.getElementById('homeSearch').value;
            window.location.href = `index.html?page=jobs&search=${encodeURIComponent(searchTerm)}`;
        }

        // Handle Enter key in search box
        document.getElementById('homeSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchJobs();
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
 