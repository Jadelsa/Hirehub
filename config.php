<?php
// إعدادات قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_NAME', 'agile2');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// إعدادات التطبيق
define('APP_NAME', 'HireHub');
define('APP_URL', 'http://localhost/hirehubfinal');
define('API_URL', APP_URL . '/api.php');

// إعدادات الجلسة
define('SESSION_LIFETIME', 86400);
define('SESSION_NAME', 'hirehub_session');

// إعدادات الأمان
define('PASSWORD_MIN_LENGTH', 6);
define('TOKEN_LENGTH', 32);
define('TOKEN_EXPIRY', 900); // 15 دقيقة
define('OTP_EXPIRY', 900); // 15 دقيقة

// إعدادات الترقيم
define('JOBS_PER_PAGE', 9);

// إعدادات رفع الملفات
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// إعدادات البريد الإلكتروني (للمستقبل)
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'noreply@hirehub.com');
define('SMTP_PASS', '');
define('SMTP_FROM', 'noreply@hirehub.com');
define('SMTP_FROM_NAME', 'HireHub');

// إعدادات SMS (للمستقبل)
define('SMS_PROVIDER', 'twilio'); // أو 'unifonic' للسوق السعودي
define('SMS_API_KEY', '');
define('SMS_SENDER_NAME', 'HireHub');

// الإبلاغ عن الأخطاء (غيّر إلى 0 في الإنتاج)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// المنطقة الزمنية
date_default_timezone_set('Asia/Riyadh');

// بدء الجلسة
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// وظائف مساعدة
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            die('فشل الاتصال بقاعدة البيانات');
        }
    }
    
    return $pdo;
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    // التحقق من رقم سعودي
    return preg_match('/^(05|5)[0-9]{8}$/', $phone);
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function generateToken($length = TOKEN_LENGTH) {
    return bin2hex(random_bytes($length));
}

function generateOTP($length = 6) {
    return sprintf("%0{$length}d", mt_rand(1, pow(10, $length) - 1));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id, name, email, phone, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    return $stmt->fetch();
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . APP_URL . '/home.html');
        exit();
    }
}

function requireRole($role) {
    $user = getCurrentUser();
    
    if (!$user || $user['role'] !== $role) {
        http_response_code(403);
        die('غير مصرح لك بالوصول');
    }
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

function sendEmail($to, $subject, $message) {
    // هنا يمكن إضافة إرسال البريد الإلكتروني الفعلي
    // باستخدام PHPMailer أو مكتبة أخرى
    
    // للتجربة:
    error_log("Email to: $to, Subject: $subject");
    return true;
}

function sendSMS($phone, $message) {
    // هنا يمكن إضافة إرسال SMS الفعلي
    // باستخدام Twilio أو Unifonic
    
    // للتجربة:
    error_log("SMS to: $phone, Message: $message");
    return true;
}

function logActivity($userId, $action, $details = null) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, action, details, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([
        $userId,
        $action,
        $details ? json_encode($details, JSON_UNESCAPED_UNICODE) : null,
        $_SERVER['REMOTE_ADDR'] ?? null
    ]);
}

function formatDate($date, $format = 'Y-m-d H:i:s') {
    if (empty($date)) return null;
    
    try {
        $dt = new DateTime($date);
        return $dt->format($format);
    } catch (Exception $e) {
        return $date;
    }
}

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) {
        return 'الآن';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return "منذ $mins " . ($mins == 1 ? 'دقيقة' : 'دقائق');
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return "منذ $hours " . ($hours == 1 ? 'ساعة' : 'ساعات');
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return "منذ $days " . ($days == 1 ? 'يوم' : 'أيام');
    } else {
        return date('Y-m-d', $time);
    }
}
define('UPLOAD_DIR', __DIR__ . '/uploads/');
function uploadFile($file, $allowedTypes = null) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'فشل رفع الملف الأساسي (قد يكون بسبب حجم الملف في إعدادات الخادم).'];
    }
    
    $allowedTypes = $allowedTypes ?: ALLOWED_FILE_TYPES;
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($fileExtension, $allowedTypes)) {
        return ['success' => false, 'error' => 'نوع الملف غير مسموح'];
    }
    
    // تأكد من أن هذا الثابت تم تعريفه في بداية الملف
    // define('MAX_FILE_SIZE', 5242880); // 5MB
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'حجم الملف كبير جداً'];
    }
    
    $newFilename = uniqid() . '_' . time() . '.' . $fileExtension;
    
    
    
    $uploadPath = UPLOAD_DIR . $newFilename;

    // ===============================================
    // 💡 التعديلات الجديدة تبدأ هنا (للتحقق من مجلد الرفع والصلاحيات)
    // ===============================================

    // 1. محاولة إنشاء المجلد إذا لم يكن موجوداً
    if (!is_dir(UPLOAD_DIR)) {
        // إذا فشل إنشاء المجلد، فهناك مشكلة في صلاحيات المجلد الأب
        if (!mkdir(UPLOAD_DIR, 0755, true)) {
             return ['success' => false, 'error' => '❌ خطأ حرج: تعذر إنشاء مجلد الرفع (uploads/). يرجى التحقق من صلاحيات المجلد الأب.'];
        }
    }
    
    // 2. التحقق من أن مجلد الرفع قابل للكتابة
    // (هذه النقطة هي السبب الأرجح للخطأ)
    if (!is_writable(UPLOAD_DIR)) {
        return ['success' => false, 'error' => '⚠️ خطأ الصلاحيات: مجلد الرفع (uploads/) غير قابل للكتابة. يرجى ضبط صلاحياته إلى (755 أو 777).'];
    }

    // 3. محاولة نقل الملف
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return [
            'success' => true,
            'filename' => $newFilename,
            'path' => $uploadPath,
            'url' => APP_URL . '/uploads/' . $newFilename
        ];
    }
    
    // إذا وصل الكود إلى هنا، فالخطأ متعلق بالخادم (PHP) رغم وجود الصلاحيات
    return ['success' => false, 'error' => 'فشل حفظ الملف. تحقق من إعدادات الخادم (مثل post_max_size و upload_max_filesize).'];
}

// تحميل التكوينات الإضافية إذا وجدت
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}
?>
