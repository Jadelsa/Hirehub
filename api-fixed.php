<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// بدء الجلسة
session_start();

// إعدادات قاعدة البيانات
$host = 'localhost';
$dbname = 'agile2';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode([
        'error' => 'فشل الاتصال بقاعدة البيانات',
        'details' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';
if ($action === 'upload_pdf') {

    if (!isset($_FILES['pdf'])) {
        echo json_encode(["status" => "error", "message" => "لم يتم استلام أي ملف"]);
        exit;
    }

    $file = $_FILES['pdf'];

    if ($file['error'] !== 0) {
        echo json_encode(["status" => "error", "message" => "خطأ في رفع الملف"]);
        exit;
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($extension !== "pdf") {
        echo json_encode(["status" => "error", "message" => "فقط ملفات PDF مسموحة"]);
        exit;
    }

    $uploadDir = "uploads/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $newName = uniqid() . ".pdf";
    $uploadPath = $uploadDir . $newName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        echo json_encode(["status" => "success", "message" => "تم رفع PDF بنجاح", "file" => $newName]);
    } else {
        echo json_encode(["status" => "error", "message" => "فشل حفظ الملف"]);
    }

    exit;
}


function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    // التحقق من رقم سعودي
    return preg_match('/^(05|5)[0-9]{8}$/', $phone);
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function generateOTP() {
    return sprintf("%06d", mt_rand(1, 999999));
}

function createNotification($pdo, $userId, $title, $message, $type = 'info', $link = null) {
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, link, is_read, created_at) 
                              VALUES (?, ?, ?, ?, ?, 0, NOW())");
        $stmt->execute([$userId, $title, $message, $type, $link]);
        return true;
    } catch(PDOException $e) {
        error_log("Failed to create notification: " . $e->getMessage());
        return false;
    }
}

// ===============================
// تسجيل مستخدم جديد
// ===============================
if ($method === 'POST' && $action === 'register') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $password = $data['password'] ?? '';
    $role = $data['role'] ?? 'seeker';
    $security_question = trim($data['security_question'] ?? '');
    $security_answer = trim($data['security_answer'] ?? '');
    
    if (empty($name) || empty($email) || empty($password)) {
        sendResponse(['error' => 'يرجى ملء جميع الحقول المطلوبة'], 400);
    }
    
    if (!validateEmail($email)) {
        sendResponse(['error' => 'البريد الإلكتروني غير صالح'], 400);
    }
    
    if (strlen($password) < 6) {
        sendResponse(['error' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل'], 400);
    }
    
    // التحقق من البريد الإلكتروني
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        sendResponse(['error' => 'البريد الإلكتروني مسجل مسبقاً'], 400);
    }
    
    // التحقق من رقم الهاتف إذا تم إدخاله
    if (!empty($phone)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        if ($stmt->fetch()) {
            sendResponse(['error' => 'رقم الهاتف مسجل مسبقاً'], 400);
        }
    }
    
    $hashedPassword = hashPassword($password);
    $hashedAnswer = !empty($security_answer) ? hashPassword(strtolower($security_answer)) : null;
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, security_question, security_answer, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    
    if ($stmt->execute([$name, $email, $phone, $hashedPassword, $role, $security_question, $hashedAnswer])) {
        $userId = $pdo->lastInsertId();
        
        // إنشاء ملف شخصي فارغ
        $stmt = $pdo->prepare("INSERT INTO profiles (user_id, created_at) VALUES (?, NOW())");
        $stmt->execute([$userId]);
        
        sendResponse([
            'success' => true,
            'message' => 'تم التسجيل بنجاح',
            'user_id' => $userId
        ]);
    } else {
        sendResponse(['error' => 'حدث خطأ أثناء التسجيل'], 500);
    }
}

// ===============================
// تسجيل الدخول
// ===============================
if ($method === 'POST' && $action === 'login') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        sendResponse(['error' => 'يرجى إدخال البريد الإلكتروني وكلمة المرور'], 400);
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !verifyPassword($password, $user['password'])) {
        sendResponse(['error' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'], 401);
    }
    
    // حفظ في الجلسة
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    
    unset($user['password']);
    unset($user['security_answer']);
    
    sendResponse([
        'success' => true,
        'user' => $user
    ]);
}

// ===============================
// التحقق من خيارات استعادة كلمة المرور
// ===============================
if ($method === 'POST' && $action === 'checkResetOptions') {
    $data = json_decode(file_get_contents('php://input'), true);
    $identifier = trim($data['identifier'] ?? ''); // يمكن أن يكون email أو phone
    
    if (empty($identifier)) {
        sendResponse(['error' => 'يرجى إدخال البريد الإلكتروني أو رقم الهاتف'], 400);
    }
    
    // البحث عن المستخدم
    if (preg_match('/^05[0-9]{8}$/', $identifier)) {
        $stmt = $pdo->prepare("SELECT id, name, email, phone, security_question FROM users WHERE phone = ?");
    } else {
        $stmt = $pdo->prepare("SELECT id, name, email, phone, security_question FROM users WHERE email = ?");
    }
    
    $stmt->execute([$identifier]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        sendResponse(['error' => 'المستخدم غير موجود'], 404);
    }
    
    // إرجاع خيارات الاستعادة المتاحة
    $options = [];
    
    if (!empty($user['email'])) {
        $options[] = [
            'type' => 'email',
            'label' => 'البريد الإلكتروني',
            'value' => $user['email'],
            'masked' => substr($user['email'], 0, 3) . '***@' . explode('@', $user['email'])[1]
        ];
    }
    
    if (!empty($user['phone'])) {
        $options[] = [
            'type' => 'phone',
            'label' => 'رقم الهاتف',
            'value' => $user['phone'],
            'masked' => substr($user['phone'], 0, 4) . '****' . substr($user['phone'], -2)
        ];
    }
    
    if (!empty($user['security_question'])) {
        $options[] = [
            'type' => 'security',
            'label' => 'السؤال الأمني',
            'question' => $user['security_question']
        ];
    }
    
    sendResponse([
        'success' => true,
        'user_id' => $user['id'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'options' => $options
    ]);
}

// ===============================
// طلب استعادة كلمة المرور - إرسال OTP
// ===============================
if ($method === 'POST' && $action === 'sendResetCode') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = trim($data['email'] ?? '');
    $method_type = $data['method'] ?? 'email'; // email, phone
    
    if (empty($email)) {
        sendResponse(['error' => 'البريد الإلكتروني مطلوب'], 400);
    }
    
    $stmt = $pdo->prepare("SELECT id, name, email, phone FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        sendResponse(['error' => 'المستخدم غير موجود'], 404);
    }
    
    // توليد رمز OTP
    $otp = generateOTP();
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
    // حذف الرموز القديمة
    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->execute([$user['email']]);
    
    // حفظ الرمز الجديد
    $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, otp, expires_at, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$user['email'], $token, $otp, $expiry]);
    
    // في الواقع، هنا يجب إرسال OTP عبر SMS أو Email
    // لكن للتجربة سنرجعه في الـ Response
    
    sendResponse([
        'success' => true,
        'message' => 'تم إرسال رمز التحقق',
        'token' => $token,
        'otp' => $otp, // في الإنتاج، لا ترسل OTP في الـ response!
        'method' => $method_type,
        'sent_to' => $method_type === 'phone' ? $user['phone'] : $user['email']
    ]);
}

// ===============================
// التحقق من OTP
// ===============================
if ($method === 'POST' && $action === 'verifyOTP') {
    $data = json_decode(file_get_contents('php://input'), true);
    $token = $data['token'] ?? '';
    $otp = $data['otp'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND otp = ? AND expires_at > NOW() AND used = 0");
    $stmt->execute([$token, $otp]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$reset) {
        sendResponse(['error' => 'رمز التحقق غير صحيح أو منتهي الصلاحية'], 400);
    }
    
    sendResponse([
        'success' => true,
        'message' => 'تم التحقق بنجاح',
        'token' => $token
    ]);
}

// ===============================
// التحقق من السؤال الأمني
// ===============================
if ($method === 'POST' && $action === 'verifySecurityAnswer') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = trim($data['email'] ?? '');
    $answer = strtolower(trim($data['answer'] ?? ''));
    
    if (empty($email) || empty($answer)) {
        sendResponse(['error' => 'البريد الإلكتروني والإجابة مطلوبان'], 400);
    }
    
    $stmt = $pdo->prepare("SELECT id, security_answer FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || empty($user['security_answer'])) {
        sendResponse(['error' => 'المستخدم غير موجود أو لا يوجد سؤال أمني'], 400);
    }
    
    if (!verifyPassword($answer, $user['security_answer'])) {
        sendResponse(['error' => 'الإجابة غير صحيحة'], 400);
    }
    
    // توليد token للسماح بتغيير كلمة المرور
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
    // حذف الرموز القديمة
    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->execute([$email]);
    
    $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$email, $token, $expiry]);
    
    sendResponse([
        'success' => true,
        'message' => 'تم التحقق بنجاح',
        'token' => $token
    ]);
}

// ===============================
// إعادة تعيين كلمة المرور
// ===============================
if ($method === 'POST' && $action === 'resetPassword') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $token = $data['token'] ?? '';
    $newPassword = $data['password'] ?? '';
    
    if (strlen($newPassword) < 6) {
        sendResponse(['error' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل'], 400);
    }
    
    $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW() AND used = 0");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$reset) {
        sendResponse(['error' => 'رمز إعادة التعيين غير صالح أو منتهي الصلاحية'], 400);
    }
    
    $hashedPassword = hashPassword($newPassword);
    $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE email = ?");
    $stmt->execute([$hashedPassword, $reset['email']]);
    
    // تحديث حالة الرمز
    $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
    $stmt->execute([$token]);
    
    sendResponse([
        'success' => true,
        'message' => 'تم تغيير كلمة المرور بنجاح'
    ]);
}

// ===============================
// الحصول على جميع الوظائف
// ===============================
if ($method === 'GET' && $action === 'jobs') {
    $search = $_GET['search'] ?? '';
    $type = $_GET['type'] ?? '';
    $location = $_GET['location'] ?? '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 9;
    $offset = ($page - 1) * $limit;
    
    $query = "SELECT j.*, u.name as company_name 
              FROM jobs j
              LEFT JOIN users u ON j.posted_by = u.id
              WHERE j.is_active = 1 AND j.draft = 0";
    
    $params = [];
    
    if (!empty($search)) {
        $query .= " AND (j.title LIKE ? OR j.company LIKE ? OR j.location LIKE ? OR j.description LIKE ?)";
        $searchTerm = "%$search%";
        $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    if (!empty($type)) {
        $query .= " AND j.type = ?";
        $params[] = $type;
    }
    
    if (!empty($location)) {
        $query .= " AND j.location LIKE ?";
        $params[] = "%$location%";
    }
    
    // عدد الوظائف الكلي
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $totalJobs = $stmt->rowCount();
    
    $query .= " ORDER BY j.created_at DESC LIMIT $limit OFFSET $offset";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    sendResponse([
        'jobs' => $jobs,
        'total' => $totalJobs,
        'page' => $page,
        'totalPages' => ceil($totalJobs / $limit)
    ]);
}

// ===============================
// الحصول على وظيفة واحدة
// ===============================
if ($method === 'GET' && $action === 'job') {
    $jobId = (int)($_GET['id'] ?? 0);
    
    if ($jobId === 0) {
        sendResponse(['error' => 'معرف الوظيفة غير صالح'], 400);
    }
    
    $stmt = $pdo->prepare("SELECT j.*, u.name as company_name, u.email as company_email, u.phone as company_phone 
                          FROM jobs j
                          LEFT JOIN users u ON j.posted_by = u.id
                          WHERE j.id = ?");
    $stmt->execute([$jobId]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$job) {
        sendResponse(['error' => 'الوظيفة غير موجودة'], 404);
    }
    
    // زيادة عدد المشاهدات
    $stmt = $pdo->prepare("UPDATE jobs SET views_count = views_count + 1 WHERE id = ?");
    $stmt->execute([$jobId]);
    
    sendResponse(['job' => $job]);
}

// ===============================
// نشر وظيفة
// ===============================
if ($method === 'POST' && $action === 'postJob') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $title = trim($data['title'] ?? '');
    $company = trim($data['company'] ?? '');
    $location = trim($data['location'] ?? '');
    $salary = trim($data['salary'] ?? '');
    $type = trim($data['type'] ?? 'دوام كامل');
    $description = trim($data['description'] ?? '');
    $requirements = trim($data['requirements'] ?? '');
    $postedBy = (int)($data['posted_by'] ?? 0);
    $draft = (int)($data['draft'] ?? 0);
    
    if (empty($title) || empty($company) || empty($description) || $postedBy === 0) {
        sendResponse(['error' => 'يرجى ملء الحقول المطلوبة'], 400);
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO jobs (title, company, location, salary, type, description, requirements, posted_by, draft, is_active, created_at) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())");
        
        if ($stmt->execute([$title, $company, $location, $salary, $type, $description, $requirements, $postedBy, $draft])) {
            $jobId = $pdo->lastInsertId();
            
            sendResponse([
                'success' => true,
                'message' => $draft ? 'تم حفظ المسودة' : 'تم نشر الوظيفة بنجاح',
                'job_id' => $jobId
            ]);
        } else {
            sendResponse(['error' => 'حدث خطأ أثناء نشر الوظيفة'], 500);
        }
    } catch(PDOException $e) {
        sendResponse(['error' => 'حدث خطأ: ' . $e->getMessage()], 500);
    }
}

// ===============================
// تحديث وظيفة
// ===============================
if ($method === 'PUT' && $action === 'updateJob') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $jobId = (int)($data['job_id'] ?? 0);
    $userId = (int)($data['user_id'] ?? 0);
    
    // التحقق من الصلاحية
    $stmt = $pdo->prepare("SELECT id FROM jobs WHERE id = ? AND posted_by = ?");
    $stmt->execute([$jobId, $userId]);
    
    if (!$stmt->fetch()) {
        sendResponse(['error' => 'غير مصرح لك بتعديل هذه الوظيفة'], 403);
    }
    
    $updates = [];
    $params = [];
    
    if (isset($data['title'])) {
        $updates[] = "title = ?";
        $params[] = trim($data['title']);
    }
    if (isset($data['company'])) {
        $updates[] = "company = ?";
        $params[] = trim($data['company']);
    }
    if (isset($data['location'])) {
        $updates[] = "location = ?";
        $params[] = trim($data['location']);
    }
    if (isset($data['salary'])) {
        $updates[] = "salary = ?";
        $params[] = trim($data['salary']);
    }
    if (isset($data['type'])) {
        $updates[] = "type = ?";
        $params[] = trim($data['type']);
    }
    if (isset($data['description'])) {
        $updates[] = "description = ?";
        $params[] = trim($data['description']);
    }
    if (isset($data['requirements'])) {
        $updates[] = "requirements = ?";
        $params[] = trim($data['requirements']);
    }
    if (isset($data['draft'])) {
        $updates[] = "draft = ?";
        $params[] = (int)$data['draft'];
    }
    
    if (empty($updates)) {
        sendResponse(['error' => 'لا توجد بيانات للتحديث'], 400);
    }
    
    $updates[] = "updated_at = NOW()";
    $params[] = $jobId;
    
    $query = "UPDATE jobs SET " . implode(", ", $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($query);
    
    if ($stmt->execute($params)) {
        sendResponse([
            'success' => true,
            'message' => 'تم تحديث الوظيفة بنجاح'
        ]);
    } else {
        sendResponse(['error' => 'حدث خطأ أثناء التحديث'], 500);
    }
}

// ===============================
// حذف وظيفة
// ===============================
if ($method === 'DELETE' && $action === 'deleteJob') {
    $data = json_decode(file_get_contents('php://input'), true);
    $jobId = (int)($data['job_id'] ?? 0);
    $userId = (int)($data['user_id'] ?? 0);
    
    if ($jobId === 0 || $userId === 0) {
        sendResponse(['error' => 'معرف الوظيفة أو المستخدم غير صالح'], 400);
    }
    
    $stmt = $pdo->prepare("SELECT id FROM jobs WHERE id = ? AND posted_by = ?");
    $stmt->execute([$jobId, $userId]);
    
    if (!$stmt->fetch()) {
        sendResponse(['error' => 'غير مصرح لك بحذف هذه الوظيفة'], 403);
    }
    
    $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
    
    if ($stmt->execute([$jobId])) {
        sendResponse([
            'success' => true,
            'message' => 'تم حذف الوظيفة بنجاح'
        ]);
    } else {
        sendResponse(['error' => 'حدث خطأ أثناء حذف الوظيفة'], 500);
    }
}

// ===============================
// الحصول على الملف الشخصي
// ===============================
if ($method === 'GET' && $action === 'profile') {
    $userId = (int)($_GET['user_id'] ?? 0);
    
    if ($userId === 0) {
        sendResponse(['error' => 'معرف المستخدم غير صالح'], 400);
    }
    
    // معلومات المستخدم الأساسية
    $stmt = $pdo->prepare("SELECT id, name, email, phone, role FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        sendResponse(['error' => 'المستخدم غير موجود'], 404);
    }
    
    // الملف الشخصي
    $stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // إذا لم يكن هناك ملف شخصي، إنشاء واحد
    if (!$profile) {
        $stmt = $pdo->prepare("INSERT INTO profiles (user_id, created_at) VALUES (?, NOW())");
        $stmt->execute([$userId]);
        
        $stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // التعليم
    $stmt = $pdo->prepare("SELECT * FROM education WHERE user_id = ? ORDER BY year DESC");
    $stmt->execute([$userId]);
    $education = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // الخبرة
    $stmt = $pdo->prepare("SELECT * FROM experience WHERE user_id = ? ORDER BY start_date DESC");
    $stmt->execute([$userId]);
    $experience = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // المهارات
    $stmt = $pdo->prepare("SELECT skill, level FROM skills WHERE user_id = ?");
    $stmt->execute([$userId]);
    $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    sendResponse([
        'user' => $user,
        'profile' => $profile,
        'education' => $education,
        'experience' => $experience,
        'skills' => $skills
    ]);
}


// ===============================
// حفظ الملف الشخصي
// ===============================
if ($method === 'POST' && $action === 'saveProfile') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $userId = (int)($data['user_id'] ?? 0);
    
    if ($userId === 0) {
        sendResponse(['error' => 'معرف المستخدم غير صالح'], 400);
    }
    
    try {
        // التحقق من وجود المستخدم
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        if (!$stmt->fetch()) {
            sendResponse(['error' => 'المستخدم غير موجود'], 404);
        }
        
        // حفظ المعلومات الأساسية
        if (isset($data['profile'])) {
            $profile = $data['profile'];
            
            // التحقق من وجود الملف الشخصي
            $stmt = $pdo->prepare("SELECT id FROM profiles WHERE user_id = ?");
            $stmt->execute([$userId]);
            $exists = $stmt->fetch();
            
            if ($exists) {
                $stmt = $pdo->prepare("UPDATE profiles SET 
                    phone = ?, 
                    location = ?, 
                    bio = ?,
                    resume_pdf = ?,
                    linkedin_url = ?,
                    github_url = ?,
                    website_url = ?,
                    updated_at = NOW() 
                    WHERE user_id = ?");
                $stmt->execute([
                    $profile['phone'] ?? null,
                    $profile['location'] ?? null,
                    $profile['bio'] ?? null,
                    $profile['resume_pdf'] ?? null,
                    $profile['linkedin_url'] ?? null,
                    $profile['github_url'] ?? null,
                    $profile['website_url'] ?? null,
                    $userId
                ]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO profiles 
                    (user_id, phone, location, bio, resume_pdf, linkedin_url, github_url, website_url, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([
                    $userId,
                    $profile['phone'] ?? null,
                    $profile['location'] ?? null,
                    $profile['bio'] ?? null,
                    $profile['resume_pdf'] ?? null,
                    $profile['linkedin_url'] ?? null,
                    $profile['github_url'] ?? null,
                    $profile['website_url'] ?? null
                ]);
            }
        }
        
        // حفظ التعليم
        if (isset($data['education']) && is_array($data['education'])) {
            // حذف القديم
            $stmt = $pdo->prepare("DELETE FROM education WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // إضافة الجديد
            foreach ($data['education'] as $edu) {
                if (!empty($edu['degree']) && !empty($edu['institution'])) {
                    $stmt = $pdo->prepare("INSERT INTO education 
                        (user_id, degree, institution, field_of_study, year, grade, description, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                    $stmt->execute([
                        $userId,
                        $edu['degree'],
                        $edu['institution'],
                        $edu['field_of_study'] ?? null,
                        $edu['year'] ?? null,
                        $edu['grade'] ?? null,
                        $edu['description'] ?? null
                    ]);
                }
            }
        }
        
        // حفظ الخبرة
        if (isset($data['experience']) && is_array($data['experience'])) {
            // حذف القديم
            $stmt = $pdo->prepare("DELETE FROM experience WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // إضافة الجديد
            foreach ($data['experience'] as $exp) {
                if (!empty($exp['title']) && !empty($exp['company'])) {
                    $stmt = $pdo->prepare("INSERT INTO experience 
                        (user_id, title, company, location, duration, start_date, end_date, is_current, description, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                    $stmt->execute([
                        $userId,
                        $exp['title'],
                        $exp['company'],
                        $exp['location'] ?? null,
                        $exp['duration'] ?? null,
                        $exp['start_date'] ?? null,
                        $exp['end_date'] ?? null,
                        $exp['is_current'] ?? 0,
                        $exp['description'] ?? null
                    ]);
                }
            }
        }
        
        // حفظ المهارات
        if (isset($data['skills']) && is_array($data['skills'])) {
            // حذف القديم
            $stmt = $pdo->prepare("DELETE FROM skills WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // إضافة الجديد
            foreach ($data['skills'] as $skill) {
                if (!empty($skill['skill'])) {
                    $stmt = $pdo->prepare("INSERT INTO skills (user_id, skill, level, created_at) VALUES (?, ?, ?, NOW())");
                    $stmt->execute([
                        $userId,
                        $skill['skill'],
                        $skill['level'] ?? 'intermediate'
                    ]);
                }
            }
        }
        
        sendResponse([
            'success' => true,
            'message' => 'تم حفظ الملف الشخصي بنجاح'
        ]);
    } catch(PDOException $e) {
        sendResponse(['error' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()], 500);
    }
}

// ===============================
// التقديم على وظيفة
// ===============================
if ($method === 'POST' && $action === 'applyJob') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $jobId = (int)($data['job_id'] ?? 0);
    $userId = (int)($data['user_id'] ?? 0);
    $coverLetter = trim($data['cover_letter'] ?? '');
    
    if ($jobId === 0 || $userId === 0) {
        sendResponse(['error' => 'معرف الوظيفة أو المستخدم غير صالح'], 400);
    }
    
    // التحقق من عدم التقديم مسبقاً
    $stmt = $pdo->prepare("SELECT id FROM applications WHERE job_id = ? AND user_id = ?");
    $stmt->execute([$jobId, $userId]);
    
    if ($stmt->fetch()) {
        sendResponse(['error' => 'لقد قمت بالتقديم على هذه الوظيفة مسبقاً'], 400);
    }
    
    // الحصول على معلومات الوظيفة والشركة
    $stmt = $pdo->prepare("SELECT j.title, j.posted_by, u.name as applicant_name, u.email as applicant_email 
                          FROM jobs j
                          CROSS JOIN users u
                          WHERE j.id = ? AND u.id = ?");
    $stmt->execute([$jobId, $userId]);
    $jobInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$jobInfo) {
        sendResponse(['error' => 'الوظيفة غير موجودة'], 404);
    }
    
    try {
        // بدء Transaction
        $pdo->beginTransaction();
        
        // إضافة الطلب
        $stmt = $pdo->prepare("INSERT INTO applications (job_id, user_id, cover_letter, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
        $stmt->execute([$jobId, $userId, $coverLetter]);
        $applicationId = $pdo->lastInsertId();
        
        // تحديث عدد الطلبات
        $stmt = $pdo->prepare("UPDATE jobs SET applications_count = applications_count + 1 WHERE id = ?");
        $stmt->execute([$jobId]);
        
        // إنشاء إشعار للشركة صاحبة الوظيفة
        $notificationTitle = 'طلب توظيف جديد';
        $notificationMessage = "قدم {$jobInfo['applicant_name']} على وظيفة {$jobInfo['title']}";
        createNotification($pdo, $jobInfo['posted_by'], $notificationTitle, $notificationMessage, 'application', "/applications/{$applicationId}");
        
        // إنشاء إشعار للمتقدم
        $applicantNotificationTitle = 'تم إرسال طلبك بنجاح';
        $applicantNotificationMessage = "تم إرسال طلبك لوظيفة {$jobInfo['title']} بنجاح";
        createNotification($pdo, $userId, $applicantNotificationTitle, $applicantNotificationMessage, 'success', "/my-applications");
        
        // إتمام Transaction
        $pdo->commit();
        
        sendResponse([
            'success' => true,
            'message' => 'تم التقديم على الوظيفة بنجاح',
            'application_id' => $applicationId
        ]);
    } catch(PDOException $e) {
        // التراجع عن Transaction في حالة الخطأ
        $pdo->rollBack();
        sendResponse(['error' => 'حدث خطأ أثناء التقديم: ' . $e->getMessage()], 500);
    }
}

// ===============================
// حفظ وظيفة
// ===============================
if ($method === 'POST' && $action === 'saveJob') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $jobId = (int)($data['job_id'] ?? 0);
    $userId = (int)($data['user_id'] ?? 0);
    
    if ($jobId === 0 || $userId === 0) {
        sendResponse(['error' => 'معرف الوظيفة أو المستخدم غير صالح'], 400);
    }
    
    // التحقق من عدم الحفظ مسبقاً
    $stmt = $pdo->prepare("SELECT id FROM saved_jobs WHERE job_id = ? AND user_id = ?");
    $stmt->execute([$jobId, $userId]);
    
    if ($stmt->fetch()) {
        sendResponse(['error' => 'الوظيفة محفوظة مسبقاً'], 400);
    }
    
    $stmt = $pdo->prepare("INSERT INTO saved_jobs (job_id, user_id, created_at) VALUES (?, ?, NOW())");
    
    if ($stmt->execute([$jobId, $userId])) {
        sendResponse([
            'success' => true,
            'message' => 'تم حفظ الوظيفة'
        ]);
    } else {
        sendResponse(['error' => 'حدث خطأ أثناء الحفظ'], 500);
    }
}

// ===============================
// إلغاء حفظ وظيفة
// ===============================
if ($method === 'DELETE' && $action === 'unsaveJob') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $jobId = (int)($data['job_id'] ?? 0);
    $userId = (int)($data['user_id'] ?? 0);
    
    $stmt = $pdo->prepare("DELETE FROM saved_jobs WHERE job_id = ? AND user_id = ?");
    
    if ($stmt->execute([$jobId, $userId])) {
        sendResponse([
            'success' => true,
            'message' => 'تم إلغاء حفظ الوظيفة'
        ]);
    } else {
        sendResponse(['error' => 'حدث خطأ'], 500);
    }
}

// ===============================
// الحصول على الوظائف المحفوظة
// ===============================
if ($method === 'GET' && $action === 'savedJobs') {
    $userId = (int)($_GET['user_id'] ?? 0);
    
    if ($userId === 0) {
        sendResponse(['error' => 'معرف المستخدم غير صالح'], 400);
    }
    
    $stmt = $pdo->prepare("SELECT j.*, u.name as company_name, sj.created_at as saved_at 
                          FROM saved_jobs sj
                          INNER JOIN jobs j ON sj.job_id = j.id
                          LEFT JOIN users u ON j.posted_by = u.id
                          WHERE sj.user_id = ?
                          ORDER BY sj.created_at DESC");
    $stmt->execute([$userId]);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    sendResponse(['jobs' => $jobs]);
}

// ===============================
// الحصول على طلباتي
// ===============================
if ($method === 'GET' && $action === 'myApplications') {
    $userId = (int)($_GET['user_id'] ?? 0);
    
    if ($userId === 0) {
        sendResponse(['error' => 'معرف المستخدم غير صالح'], 400);
    }
    
    $stmt = $pdo->prepare("SELECT a.*, j.title, j.company, j.location, j.salary, j.type, u.name as company_name 
                          FROM applications a
                          INNER JOIN jobs j ON a.job_id = j.id
                          LEFT JOIN users u ON j.posted_by = u.id
                          WHERE a.user_id = ?
                          ORDER BY a.created_at DESC");
    $stmt->execute([$userId]);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    sendResponse(['applications' => $applications]);
}

// ===============================
// الحصول على وظائفي (للمنشئ)
// ===============================
if ($method === 'GET' && $action === 'myJobs') {
    $userId = (int)($_GET['user_id'] ?? 0);
    
    if ($userId === 0) {
        sendResponse(['error' => 'معرف المستخدم غير صالح'], 400);
    }
    
    $stmt = $pdo->prepare("SELECT j.*, 
                          (SELECT COUNT(*) FROM applications WHERE job_id = j.id) as applications_count
                          FROM jobs j
                          WHERE j.posted_by = ?
                          ORDER BY j.created_at DESC");
    $stmt->execute([$userId]);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    sendResponse(['jobs' => $jobs]);
}

// ===============================
// الحصول على طلبات الوظيفة (للشركة)
// ===============================
if ($method === 'GET' && $action === 'jobApplications') {
    $jobId = (int)($_GET['job_id'] ?? 0);
    $userId = (int)($_GET['user_id'] ?? 0);
    
    if ($jobId === 0 || $userId === 0) {
        sendResponse(['error' => 'معرف الوظيفة أو المستخدم غير صالح'], 400);
    }
    
    // التحقق من أن المستخدم هو صاحب الوظيفة
    $stmt = $pdo->prepare("SELECT id FROM jobs WHERE id = ? AND posted_by = ?");
    $stmt->execute([$jobId, $userId]);
    
    if (!$stmt->fetch()) {
        sendResponse(['error' => 'غير مصرح لك بعرض طلبات هذه الوظيفة'], 403);
    }
    
    $stmt = $pdo->prepare("SELECT a.*, u.name, u.email, u.phone,
                          p.bio, p.linkedin_url, p.github_url
                          FROM applications a
                          INNER JOIN users u ON a.user_id = u.id
                          LEFT JOIN profiles p ON u.id = p.user_id
                          WHERE a.job_id = ?
                          ORDER BY a.created_at DESC");
    $stmt->execute([$jobId]);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    sendResponse(['applications' => $applications]);
}

// ===============================
// تحديث حالة الطلب
// ===============================
if ($method === 'PUT' && $action === 'updateApplicationStatus') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $applicationId = (int)($data['application_id'] ?? 0);
    $status = $data['status'] ?? '';
    $notes = trim($data['notes'] ?? '');
    $userId = (int)($data['user_id'] ?? 0);
    
    if ($applicationId === 0 || empty($status)) {
        sendResponse(['error' => 'معرف الطلب أو الحالة غير صالحة'], 400);
    }
    
    // التحقق من الصلاحية
    $stmt = $pdo->prepare("SELECT a.id, a.user_id, j.title, j.posted_by, u.name as applicant_name
                          FROM applications a
                          INNER JOIN jobs j ON a.job_id = j.id
                          INNER JOIN users u ON a.user_id = u.id
                          WHERE a.id = ? AND j.posted_by = ?");
    $stmt->execute([$applicationId, $userId]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$application) {
        sendResponse(['error' => 'غير مصرح لك بتحديث هذا الطلب'], 403);
    }
    
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("UPDATE applications SET status = ?, notes = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $notes, $applicationId]);
        
        // إنشاء إشعار للمتقدم
        $statusMessages = [
            'reviewed' => 'تم مراجعة طلبك',
            'accepted' => 'تم قبول طلبك',
            'rejected' => 'تم رفض طلبك'
        ];
        
        $notificationMessage = $statusMessages[$status] ?? 'تم تحديث حالة طلبك';
        $notificationMessage .= " لوظيفة {$application['title']}";
        
        createNotification($pdo, $application['user_id'], 'تحديث حالة الطلب', $notificationMessage, 'status_update', "/my-applications");
        
        $pdo->commit();
        
        sendResponse([
            'success' => true,
            'message' => 'تم تحديث حالة الطلب بنجاح'
        ]);
    } catch(PDOException $e) {
        $pdo->rollBack();
        sendResponse(['error' => 'حدث خطأ: ' . $e->getMessage()], 500);
    }
}

// ===============================
// الحصول على الإشعارات
// ===============================
if ($method === 'GET' && $action === 'notifications') {
    $userId = (int)($_GET['user_id'] ?? 0);
    $unreadOnly = isset($_GET['unread_only']) ? (bool)$_GET['unread_only'] : false;
    
    if ($userId === 0) {
        sendResponse(['error' => 'معرف المستخدم غير صالح'], 400);
    }
    
    $query = "SELECT * FROM notifications WHERE user_id = ?";
    
    if ($unreadOnly) {
        $query .= " AND is_read = 0";
    }
    
    $query .= " ORDER BY created_at DESC LIMIT 50";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // عدد الإشعارات غير المقروءة
    $stmt = $pdo->prepare("SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$userId]);
    $unreadCount = $stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
    
    sendResponse([
        'notifications' => $notifications,
        'unread_count' => $unreadCount
    ]);
}

// ===============================
// تحديد الإشعار كمقروء
// ===============================
if ($method === 'PUT' && $action === 'markNotificationRead') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $notificationId = (int)($data['notification_id'] ?? 0);
    $userId = (int)($data['user_id'] ?? 0);
    
    if ($notificationId === 0) {
        sendResponse(['error' => 'معرف الإشعار غير صالح'], 400);
    }
    
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    
    if ($stmt->execute([$notificationId, $userId])) {
        sendResponse([
            'success' => true,
            'message' => 'تم تحديث الإشعار'
        ]);
    } else {
        sendResponse(['error' => 'حدث خطأ'], 500);
    }
}

// ===============================
// تحديد جميع الإشعارات كمقروءة
// ===============================
if ($method === 'PUT' && $action === 'markAllNotificationsRead') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $userId = (int)($data['user_id'] ?? 0);
    
    if ($userId === 0) {
        sendResponse(['error' => 'معرف المستخدم غير صالح'], 400);
    }
    
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
    
    if ($stmt->execute([$userId])) {
        sendResponse([
            'success' => true,
            'message' => 'تم تحديث جميع الإشعارات'
        ]);
    } else {
        sendResponse(['error' => 'حدث خطأ'], 500);
    }
}

// ===============================
// تسجيل الخروج
// ===============================
if ($method === 'POST' && $action === 'logout') {
    session_destroy();
    sendResponse([
        'success' => true,
        'message' => 'تم تسجيل الخروج بنجاح'
    ]);
}

sendResponse(['error' => 'طلب غير صالح'], 400);
?>