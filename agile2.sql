-- قاعدة بيانات HireHub - agile2
-- تم التحديث والتحسين

-- حذف قاعدة البيانات إذا كانت موجودة
DROP DATABASE IF EXISTS agile2;

-- إنشاء قاعدة البيانات
CREATE DATABASE agile2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE agile2;

-- =======================
-- جدول المستخدمين
-- =======================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) UNIQUE NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('seeker', 'employer') DEFAULT 'seeker',
    security_question VARCHAR(500) NULL,
    security_answer VARCHAR(255) NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =======================
-- جدول الوظائف
-- =======================
CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    company VARCHAR(255) NOT NULL,
    location VARCHAR(255) NULL,
    salary VARCHAR(255) NULL,
    type VARCHAR(100) DEFAULT 'دوام كامل',
    description TEXT NOT NULL,
    requirements TEXT NULL,
    posted_by INT NOT NULL,
    draft TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    views_count INT DEFAULT 0,
    applications_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_posted_by (posted_by),
    INDEX idx_draft (draft),
    INDEX idx_is_active (is_active),
    INDEX idx_type (type),
    FULLTEXT idx_search (title, company, location, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =======================
-- جدول الملفات الشخصية
-- =======================
CREATE TABLE profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    phone VARCHAR(50) NULL,
    location VARCHAR(255) NULL,
    bio TEXT NULL,
    avatar VARCHAR(255) NULL,

    resume_pdf VARCHAR(255) NULL,
    linkedin_url VARCHAR(255) NULL,
    github_url VARCHAR(255) NULL,
    website_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =======================
-- جدول التعليم
-- =======================
CREATE TABLE education (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    degree VARCHAR(255) NOT NULL,
    institution VARCHAR(255) NOT NULL,
    field_of_study VARCHAR(255) NULL,
    year VARCHAR(50) NOT NULL,
    grade VARCHAR(50) NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =======================
-- جدول الخبرة
-- =======================
CREATE TABLE experience (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    company VARCHAR(255) NOT NULL,
    location VARCHAR(255) NULL,
    duration VARCHAR(100) NOT NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    is_current TINYINT(1) DEFAULT 0,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =======================
-- جدول المهارات
-- =======================
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    skill VARCHAR(255) NOT NULL,
    level ENUM('beginner', 'intermediate', 'advanced', 'expert') DEFAULT 'intermediate',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    UNIQUE KEY unique_user_skill (user_id, skill)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =======================
-- جدول إعادة تعيين كلمة المرور
-- =======================
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    otp VARCHAR(10) NULL,
    expires_at TIMESTAMP NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_email (email),
    INDEX idx_otp (otp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =======================
-- جدول الطلبات
-- =======================
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('pending', 'reviewed', 'accepted', 'rejected') DEFAULT 'pending',
    cover_letter TEXT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (job_id, user_id),
    INDEX idx_job_id (job_id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =======================
-- جدول الوظائف المحفوظة
-- =======================
CREATE TABLE saved_jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    job_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_saved_job (user_id, job_id),
    INDEX idx_user_id (user_id),
    INDEX idx_job_id (job_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =======================
-- جدول الإشعارات
-- =======================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    link VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =======================
-- إدراج بيانات تجريبية
-- =======================

-- مستخدمون (كلمة المرور: 123456)
INSERT INTO users (name, email, phone, password, role, security_question, security_answer) VALUES
('أحمد محمد العلي', 'ahmed@example.com', '0501234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seeker', 'ما اسم مدينتك؟', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('فاطمة أحمد السعيد', 'fatima@example.com', '0509876543', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seeker', 'ما اسم حيوانك الأليف؟', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('محمد عبدالله', 'mohammed@example.com', '0551234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seeker', 'ما اسم أفضل صديق لك؟', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('شركة التقنية المتقدمة', 'tech@company.com', '0112345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employer', NULL, NULL),
('شركة الابتكار السعودية', 'innovation@company.com', '0112345679', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employer', NULL, NULL),
('مجموعة النجاح للأعمال', 'success@company.com', '0112345680', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employer', NULL, NULL),
('شركة المستقبل الرقمي', 'future@company.com', '0112345681', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employer', NULL, NULL);

-- وظائف ثابتة ومتنوعة
INSERT INTO jobs (title, company, location, salary, type, description, requirements, posted_by, draft, is_active, views_count, applications_count) VALUES
-- وظائف التقنية
('مطور Full Stack', 'شركة التقنية المتقدمة', 'الرياض، السعودية', '15,000 - 20,000 ريال', 'دوام كامل', 
'نبحث عن مطور Full Stack محترف للانضمام إلى فريقنا. ستكون مسؤولاً عن تطوير تطبيقات ويب متقدمة باستخدام أحدث التقنيات والأدوات.', 
'• خبرة 3+ سنوات في React و Node.js
• إجادة JavaScript و TypeScript
• معرفة قوية بـ MongoDB أو MySQL
• خبرة في Git و Docker
• القدرة على العمل ضمن فريق', 
4, 0, 1, 156, 12),

('مطور تطبيقات موبايل', 'شركة التقنية المتقدمة', 'الرياض، السعودية', '14,000 - 19,000 ريال', 'دوام كامل',
'نبحث عن مطور تطبيقات iOS و Android محترف للعمل على تطبيقات مبتكرة.',
'• خبرة في React Native أو Flutter
• معرفة بـ Swift أو Kotlin
• خبرة في نشر التطبيقات
• فهم جيد لـ REST APIs
• مهارات UI/UX جيدة',
4, 0, 1, 89, 8),

('مهندس DevOps', 'مجموعة النجاح للأعمال', 'الرياض، السعودية', '18,000 - 25,000 ريال', 'دوام كامل',
'مهندس DevOps محترف لإدارة البنية التحتية السحابية وتحسين عمليات التطوير.',
'• خبرة قوية في AWS أو Azure
• إتقان Docker و Kubernetes
• معرفة بـ CI/CD pipelines
• خبرة في Linux و Bash
• شهادات AWS مفضلة',
6, 0, 1, 134, 15),

('مطور Backend - Python', 'شركة الابتكار السعودية', 'جدة، السعودية', '13,000 - 18,000 ريال', 'دوام كامل',
'مطور Backend متمرس للعمل على بناء APIs قوية وقابلة للتوسع.',
'• خبرة 2+ سنوات في Python (Django/Flask)
• إجادة PostgreSQL و Redis
• معرفة بـ RESTful APIs
• فهم جيد لأمن المعلومات
• خبرة في Microservices',
5, 0, 1, 67, 5),

-- التصميم والإبداع
('مصمم UI/UX', 'شركة الابتكار السعودية', 'جدة، السعودية', '12,000 - 18,000 ريال', 'دوام كامل',
'مصمم واجهات مستخدم مبدع للعمل على تحسين تجربة المستخدم في منتجاتنا الرقمية.',
'• إتقان Figma و Adobe XD و Sketch
• فهم عميق لمبادئ UX/UI
• خبرة في تصميم تطبيقات الموبايل
• محفظة أعمال قوية
• مهارات تواصل ممتازة',
5, 0, 1, 92, 11),

('مصمم جرافيك', 'شركة المستقبل الرقمي', 'جدة، السعودية', '8,000 - 12,000 ريال', 'دوام جزئي',
'مصمم جرافيك مبدع للعمل على تصميم المواد التسويقية والإعلانية.',
'• إتقان Adobe Photoshop و Illustrator
• مهارات إبداعية عالية
• محفظة أعمال متنوعة
• القدرة على العمل تحت الضغط
• معرفة بالهوية البصرية',
7, 0, 1, 45, 3),

-- البيانات والتحليل
('محلل بيانات', 'مجموعة النجاح للأعمال', 'الدمام، السعودية', '10,000 - 15,000 ريال', 'دوام كامل',
'محلل بيانات للعمل على استخراج رؤى قيمة من البيانات الضخمة.',
'• خبرة في Python و SQL
• إجادة Excel و Power BI و Tableau
• معرفة بالتعلم الآلي
• مهارات تحليلية قوية
• شهادة في علوم البيانات مفضلة',
6, 0, 1, 78, 9),

('عالم بيانات (Data Scientist)', 'شركة التقنية المتقدمة', 'الرياض، السعودية', '16,000 - 22,000 ريال', 'دوام كامل',
'عالم بيانات متمرس للعمل على مشاريع الذكاء الاصطناعي والتعلم الآلي.',
'• درجة الماجستير في علوم البيانات أو مجال ذي صلة
• خبرة قوية في Python و R
• معرفة بـ TensorFlow و PyTorch
• خبرة في Big Data (Hadoop, Spark)
• مهارات برمجة متقدمة',
4, 0, 1, 112, 7),

-- التسويق والمبيعات
('مدير تسويق رقمي', 'شركة الابتكار السعودية', 'جدة، السعودية', '16,000 - 22,000 ريال', 'دوام كامل',
'مدير تسويق رقمي لإدارة حملات التسويق الإلكتروني وتحقيق أهداف النمو.',
'• خبرة 5+ سنوات في التسويق الرقمي
• إتقان Google Ads و Facebook Ads
• مهارات SEO و SEM قوية
• خبرة في تحليل البيانات
• شهادات Google مفضلة',
5, 0, 1, 103, 14),

('أخصائي تسويق محتوى', 'شركة المستقبل الرقمي', 'الرياض، السعودية', '9,000 - 14,000 ريال', 'دوام كامل',
'أخصائي تسويق محتوى لإنشاء وإدارة محتوى جذاب عبر منصات التواصل الاجتماعي.',
'• خبرة 2+ سنوات في تسويق المحتوى
• مهارات كتابة إبداعية ممتازة
• معرفة بـ WordPress و Canva
• خبرة في إدارة وسائل التواصل
• القدرة على العمل بشكل مستقل',
7, 0, 1, 56, 6),

('مندوب مبيعات', 'شركة التقنية المتقدمة', 'الرياض، السعودية', '7,000 - 10,000 ريال + عمولة', 'دوام كامل',
'مندوب مبيعات نشيط للترويج لمنتجاتنا التقنية وتحقيق أهداف المبيعات.',
'• مهارات تواصل وإقناع ممتازة
• خبرة في المبيعات B2B
• رخصة قيادة سارية
• القدرة على تحقيق الأهداف
• معرفة بمنطقة الرياض',
4, 0, 1, 34, 4),

-- الموارد البشرية والإدارة
('مدير موارد بشرية', 'مجموعة النجاح للأعمال', 'الدمام، السعودية', '14,000 - 19,000 ريال', 'دوام كامل',
'مدير موارد بشرية للإشراف على جميع عمليات الموارد البشرية والتوظيف.',
'• خبرة 5+ سنوات في الموارد البشرية
• معرفة قوية بقوانين العمل السعودية
• مهارات قيادية وإدارية
• شهادة SHRM أو CIPD مفضلة
• إجادة اللغة الإنجليزية',
6, 0, 1, 89, 10),

('مساعد إداري', 'شركة المستقبل الرقمي', 'الرياض، السعودية', '6,000 - 9,000 ريال', 'دوام كامل',
'مساعد إداري لدعم العمليات اليومية وتنسيق المواعيد والاجتماعات.',
'• دبلوم أو بكالوريوس في الإدارة
• مهارات تنظيمية ممتازة
• إجادة MS Office
• مهارات تواصل جيدة
• القدرة على تعدد المهام',
7, 0, 1, 41, 8),

-- المالية والمحاسبة
('محاسب قانوني', 'مجموعة النجاح للأعمال', 'الدمام، السعودية', '11,000 - 16,000 ريال', 'دوام كامل',
'محاسب قانوني محترف لإدارة الحسابات والتقارير المالية.',
'• بكالوريوس محاسبة + زمالة SOCPA
• خبرة 3+ سنوات
• إجادة برامج المحاسبة (Odoo, SAP)
• دقة عالية في العمل
• معرفة بالضرائب والزكاة',
6, 0, 1, 67, 5),

('محلل مالي', 'شركة الابتكار السعودية', 'جدة، السعودية', '10,000 - 15,000 ريال', 'دوام كامل',
'محلل مالي للعمل على التحليل المالي وإعداد التقارير والتوقعات.',
'• بكالوريوس في المالية أو الاقتصاد
• خبرة 2+ سنوات في التحليل المالي
• إجادة Excel المتقدم
• مهارات تحليلية قوية
• شهادة CFA مفضلة',
5, 0, 1, 52, 4),

-- خدمة العملاء والدعم
('أخصائي خدمة عملاء', 'شركة التقنية المتقدمة', 'الرياض، السعودية', '5,000 - 8,000 ريال', 'دوام كامل',
'أخصائي خدمة عملاء لتقديم دعم فني واستشارات للعملاء.',
'• خبرة سنة في خدمة العملاء
• مهارات تواصل ممتازة
• الصبر والقدرة على حل المشاكل
• إجادة اللغة الإنجليزية
• معرفة بأنظمة CRM',
4, 0, 1, 38, 9),

-- الهندسة
('مهندس مدني', 'مجموعة النجاح للأعمال', 'الدمام، السعودية', '12,000 - 17,000 ريال', 'دوام كامل',
'مهندس مدني للإشراف على مشاريع البناء والتطوير.',
'• بكالوريوس هندسة مدنية
• خبرة 3+ سنوات في المشاريع
• معرفة بـ AutoCAD و Revit
• عضوية الهيئة السعودية للمهندسين
• رخصة قيادة سارية',
6, 0, 1, 71, 6),

-- التعليم
('معلم رياضيات', 'شركة المستقبل الرقمي', 'الرياض، السعودية', '8,000 - 12,000 ريال', 'دوام كامل',
'معلم رياضيات للمرحلة الثانوية في مدرسة خاصة.',
'• بكالوريوس في الرياضيات أو التربية
• خبرة سنتين في التدريس
• مهارات تواصل وشرح ممتازة
• شهادة تدريس معتمدة
• القدرة على استخدام التقنية في التعليم',
7, 0, 1, 44, 7),

-- القانون
('مستشار قانوني', 'شركة الابتكار السعودية', 'جدة، السعودية', '15,000 - 22,000 ريال', 'دوام كامل',
'مستشار قانوني لتقديم الاستشارات القانونية ومراجعة العقود.',
'• بكالوريوس حقوق + ترخيص مزاولة
• خبرة 5+ سنوات في القانون التجاري
• مهارات تفاوض قوية
• إجادة اللغة الإنجليزية
• معرفة بالأنظمة السعودية',
5, 0, 1, 58, 3),

-- وظائف عن بعد
('كاتب محتوى - عن بعد', 'شركة المستقبل الرقمي', 'عن بُعد', '4,000 - 7,000 ريال', 'عن بُعد',
'كاتب محتوى للعمل عن بعد في كتابة المقالات والمحتوى الرقمي.',
'• مهارات كتابة عربية ممتازة
• خبرة في كتابة محتوى SEO
• القدرة على البحث والتحليل
• الالتزام بالمواعيد
• محفظة أعمال جيدة',
7, 0, 1, 95, 18);

-- ملفات شخصية
INSERT INTO profiles (user_id, phone, location, bio, linkedin_url, github_url) VALUES
(1, '0501234567', 'الرياض، السعودية', 'مطور برمجيات متمرس بخبرة 5 سنوات في تطوير تطبيقات الويب باستخدام React و Node.js. شغوف بالتقنية والابتكار.', 'https://linkedin.com/in/ahmed', 'https://github.com/ahmed'),
(2, '0509876543', 'جدة، السعودية', 'مصممة UI/UX مبدعة متخصصة في تصميم تجارب مستخدم استثنائية. خبرة في تصميم التطبيقات والمواقع.', 'https://linkedin.com/in/fatima', NULL),
(3, '0551234567', 'الدمام، السعودية', 'محلل بيانات متخصص في استخراج رؤى قيمة من البيانات الضخمة باستخدام Python و SQL.', 'https://linkedin.com/in/mohammed', 'https://github.com/mohammed');

-- تعليم
INSERT INTO education (user_id, degree, institution, field_of_study, year, grade, description) VALUES
(1, 'بكالوريوس علوم الحاسب', 'جامعة الملك سعود', 'علوم الحاسب', '2018', 'امتياز 4.8/5', 'تخصص في هندسة البرمجيات وقواعد البيانات'),
(1, 'شهادة مطور محترف', 'Udacity', 'Full Stack Web Development', '2019', NULL, 'برنامج نانو ديجري متخصص'),
(2, 'بكالوريوس تصميم جرافيك', 'جامعة الأميرة نورة', 'التصميم الجرافيكي', '2019', 'جيد جداً', 'تخصص في تصميم الواجهات الرقمية'),
(3, 'بكالوريوس إحصاء', 'جامعة الملك فهد', 'الإحصاء التطبيقي', '2020', 'امتياز', 'تخصص في تحليل البيانات');

-- خبرات
INSERT INTO experience (user_id, title, company, location, duration, start_date, is_current, description) VALUES
(1, 'مطور Full Stack', 'شركة التقنية المتقدمة', 'الرياض', '3 سنوات', '2020-01-01', 1, 'تطوير تطبيقات ويب متقدمة باستخدام React و Node.js. إدارة قواعد البيانات وتطوير APIs.'),
(1, 'مطور Frontend', 'شركة الإبداع الرقمي', 'الرياض', 'سنة واحدة', '2019-01-01', 0, 'تطوير واجهات مستخدم تفاعلية باستخدام React و Vue.js.'),
(2, 'مصممة UI/UX', 'استوديو التصميم الحديث', 'جدة', '2 سنة', '2021-06-01', 1, 'تصميم واجهات المستخدم وتجربة المستخدم لتطبيقات الموبايل والويب.'),
(3, 'محلل بيانات', 'شركة التحليلات الذكية', 'الدمام', 'سنة ونصف', '2022-01-01', 1, 'تحليل البيانات الضخمة باستخدام Python و SQL وإنشاء لوحات معلومات تفاعلية.');

-- مهارات
INSERT INTO skills (user_id, skill, level) VALUES
(1, 'JavaScript', 'expert'),
(1, 'React.js', 'advanced'),
(1, 'Node.js', 'advanced'),
(1, 'MongoDB', 'intermediate'),
(1, 'PostgreSQL', 'intermediate'),
(1, 'Docker', 'intermediate'),
(1, 'Git', 'advanced'),
(2, 'Figma', 'expert'),
(2, 'Adobe XD', 'expert'),
(2, 'Sketch', 'advanced'),
(2, 'Photoshop', 'advanced'),
(2, 'Illustrator', 'intermediate'),
(2, 'UI Design', 'expert'),
(2, 'UX Research', 'advanced'),
(3, 'Python', 'advanced'),
(3, 'SQL', 'expert'),
(3, 'Power BI', 'advanced'),
(3, 'Excel', 'expert'),
(3, 'Tableau', 'intermediate'),
(3, 'Machine Learning', 'intermediate');

-- =======================
-- قاعدة البيانات جاهزة!
-- =======================