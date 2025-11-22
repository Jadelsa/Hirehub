<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HireHub - منصة التوظيف</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link rel="stylesheet" href="sprint2-style.css" />
    <link rel="stylesheet" href="additional-styles.css" />
    <link rel="stylesheet" href="reset-password-styles.css" />
    <style>
      /* تحسينات عامة للواجهة */
      body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        font-family: 'Cairo', sans-serif;
      }

      /* تحسين شريط التنقل */
      .navbar {
        background: linear-gradient(135deg,rgb(0, 0, 0) 0%, #764ba2 100%);
        box-shadow: 0 4px 20px rgb(0, 0, 0);
      }

      /* صفحة Dashboard محسّنة */
      .dashboard-welcome {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 60px 40px;
        border-radius: 20px;
        margin-bottom: 40px;
        box-shadow: 0 10px 40px rgba(98, 111, 173, 0.8);
        text-align: center;
      }

      .dashboard-welcome h1 {
        font-size: 42px;
        margin-bottom: 15px;
        font-weight: 700;
      }

      .dashboard-welcome p {
        font-size: 18px;
        opacity: 0.9;
      }

      /* بطاقات الإحصائيات */
      .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
      }

      .stat-card {
        background: white;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
      }

      .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
      }

      .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      }

      .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 20px;
      }

      .stat-number {
        font-size: 36px;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
      }

      .stat-label {
        color: #666;
        font-size: 16px;
      }

      /* الأنشطة الأخيرة */
      .recent-activity {
        background: white;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        margin-bottom: 30px;
      }

      .recent-activity h3 {
        font-size: 24px;
        margin-bottom: 25px;
        color: #333;
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .activity-item {
        padding: 20px;
        border-radius: 15px;
        background: #f8f9ff;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s;
      }

      .activity-item:hover {
        background: #e8ecff;
        transform: translateX(-5px);
      }

      .activity-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: white;
        flex-shrink: 0;
      }

      .activity-content {
        flex: 1;
      }

      .activity-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
      }

      .activity-time {
        color: #999;
        font-size: 13px;
      }

      /* أزرار الإجراءات السريعة */
      .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
      }

      .quick-action-btn {
        background: white;
        padding: 25px;
        border-radius: 15px;
        border: 2px solid #e0e0e0;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
        text-decoration: none;
        color: inherit;
        display: block;
      }

      .quick-action-btn:hover {
        border-color: #667eea;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
      }

      .quick-action-btn i {
        font-size: 32px;
        margin-bottom: 15px;
        display: block;
      }

      /* تحسين بطاقات الوظائف */
      .job-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        transition: all 0.3s;
        border-right: 5px solid transparent;
      }

      .job-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        border-right-color: #667eea;
      }

      .job-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 15px;
      }

      .job-header h3 {
        color: #333;
        font-size: 22px;
        margin: 0;
      }

      .job-type {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
      }

      .job-company {
        color: #666;
        font-size: 16px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
      }

      .job-details {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 15px;
        font-size: 14px;
        color: #888;
      }

      .job-details span {
        display: flex;
        align-items: center;
        gap: 5px;
      }

      .job-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid #f0f0f0;
      }

      .job-date {
        color: #999;
        font-size: 13px;
      }

      /* تحسين الأزرار */
      .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
      }

      .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
      }

      .btn-secondary {
        background: white;
        color: #667eea;
        border: 2px solid #667eea;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 14px;
      }

      .btn-secondary:hover {
        background: #667eea;
        color: white;
      }

      /* الحالة الفارغة */
      .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: #999;
      }

      .empty-state i {
        font-size: 80px;
        margin-bottom: 25px;
        color: #ddd;
        display: block;
      }

      .empty-state h3 {
        font-size: 24px;
        color: #666;
        margin-bottom: 15px;
      }

      .empty-state p {
        font-size: 16px;
        margin-bottom: 30px;
      }

      /* تحسين النماذج */
      .form-group {
        margin-bottom: 25px;
      }

      .form-group label {
        display: block;
        font-weight: 600;
        color: #555;
        margin-bottom: 10px;
        font-size: 15px;
      }

      .form-group input,
      .form-group textarea,
      .form-group select {
        width: 100%;
        padding: 15px;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        font-size: 15px;
        transition: all 0.3s;
        font-family: 'Cairo', sans-serif;
      }

      .form-group input:focus,
      .form-group textarea:focus,
      .form-group select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
      }

      /* تحسين الصفحات */
      .page-container {
        max-width: 1400px;
        margin: 100px auto 40px;
        padding: 0 30px;
      }

      .page-header {
        margin-bottom: 40px;
      }

      .page-header h1 {
        font-size: 36px;
        color: #333;
        margin-bottom: 10px;
      }

      .page-header h1 i {
        color: #667eea;
        margin-left: 15px;
      }

      /* رسوم متحركة */
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

      .fade-in-up {
        animation: fadeInUp 0.6s ease;
      }

      /* تحسينات للأجهزة المحمولة */
      @media (max-width: 768px) {
        .dashboard-welcome h1 {
          font-size: 28px;
        }

        .stats-grid {
          grid-template-columns: 1fr;
        }

        .page-container {
          margin-top: 80px;
          padding: 0 15px;
        }
      }
    </style>
  </head>
  <body>
    <!-- شريط التنقل -->
    <nav class="navbar" id="mainNavbar" style="display: none">
      <div class="nav-container">
        <div class="nav-logo">
          <i class="fas fa-briefcase"></i>
          <span>HireHub</span>
        </div>

        <div class="nav-menu">
          <a href="#" onclick="showPage('dashboard')" class="nav-link">
            <i class="fas fa-home"></i>
            <span>الرئيسية</span>
          </a>
          <a href="#" onclick="showPage('jobs')" class="nav-link">
            <i class="fas fa-search"></i>
            <span>الوظائف</span>
          </a>
          <a href="#" onclick="showPage('profile')" class="nav-link" id="profileLink">
            <i class="fas fa-user"></i>
            <span>الملف الشخصي</span>
          </a>
          <a href="#" onclick="showPage('applications')" class="nav-link" id="applicationsLink">
            <i class="fas fa-file-alt"></i>
            <span>طلباتي</span>
          </a>
          <a href="#" onclick="showPage('savedJobs')" class="nav-link" id="savedJobsLink">
            <i class="fas fa-bookmark"></i>
            <span>المحفوظات</span>
          </a>
          <a href="#" onclick="showPage('postJob')" class="nav-link" id="postJobLink" style="display: none">
            <i class="fas fa-plus-circle"></i>
            <span>نشر وظيفة</span>
          </a>
          <a href="#" onclick="showPage('myJobs')" class="nav-link" id="myJobsLink" style="display: none">
            <i class="fas fa-briefcase"></i>
            <span>وظائفي</span>
          </a>
        </div>

        <div class="nav-actions">
          <div class="notification-bell" onclick="showNotificationsPanel()">
            <i class="fas fa-bell"></i>
            <span class="notification-badge" id="notificationBadge"></span>
          </div>
          <div class="user-menu">
            <button class="user-btn" onclick="toggleUserMenu()">
              <i class="fas fa-user-circle"></i>
              <span id="userName">المستخدم</span>
              <i class="fas fa-chevron-down"></i>
            </button>
            <div class="user-dropdown" id="userDropdown">
              <a href="#" onclick="showPage('profile')">
                <i class="fas fa-user"></i>
                الملف الشخصي
              </a>
              <a href="#" onclick="logout()">
                <i class="fas fa-sign-out-alt"></i>
                تسجيل الخروج
              </a>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- لوحة الإشعارات -->
    <div class="notifications-panel" id="notificationsPanel">
      <div class="notifications-header">
        <h3>الإشعارات</h3>
        <button onclick="markAllNotificationsRead()" class="mark-all-read">
          تحديد الكل كمقروء
        </button>
      </div>
      <div class="notifications-list" id="notificationsList">
        <div style="text-align: center; padding: 20px; color: #999;">
          لا توجد إشعارات
        </div>
      </div>
    </div>

    <!-- صفحة تسجيل الدخول -->
    <div id="loginPage" class="page active">
      <div class="auth-container">
        <div class="auth-card fade-in-up">
          <div class="auth-header">
            <i class="fas fa-briefcase"></i>
            <h2>تسجيل الدخول</h2>
            <p>مرحباً بعودتك إلى HireHub</p>
          </div>

          <form id="loginForm" onsubmit="handleLogin(event)">
            <div class="form-group">
              <label><i class="fas fa-envelope"></i> البريد الإلكتروني</label>
              <input
                type="email"
                name="email"
                placeholder="example@email.com"
                required
              />
            </div>

            <div class="form-group">
              <label><i class="fas fa-lock"></i> كلمة المرور</label>
              <div class="password-input">
                <input
                  type="password"
                  name="password"
                  id="loginPassword"
                  placeholder="••••••••"
                  required
                />
                <button
                  type="button"
                  onclick="togglePassword('loginPassword')"
                  class="toggle-password"
                >
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>

            <div class="form-options">
              <label class="checkbox-label">
                <input type="checkbox" name="remember" />
                <span>تذكرني</span>
              </label>
              <a href="#" onclick="showPage('resetPassword')" class="forgot-link">
                نسيت كلمة المرور؟
              </a>
            </div>

            <button type="submit" class="btn-submit">
              <i class="fas fa-sign-in-alt"></i>
              تسجيل الدخول
            </button>
          </form>

          <p class="auth-footer">
            ليس لديك حساب؟
            <a href="#" onclick="showPage('register')">إنشاء حساب جديد</a>
          </p>
        </div>
      </div>
    </div>

    <!-- صفحة التسجيل -->
    <div id="registerPage" class="page">
      <div class="auth-container">
        <div class="auth-card fade-in-up">
          <div class="auth-header">
            <i class="fas fa-user-plus"></i>
            <h2>إنشاء حساب جديد</h2>
            <p>انضم إلى HireHub اليوم</p>
          </div>

          <form id="registerForm" onsubmit="handleRegister(event)">
            <div class="form-group">
              <label><i class="fas fa-user"></i> الاسم الكامل</label>
              <input type="text" name="name" placeholder="أدخل اسمك الكامل" required />
            </div>

            <div class="form-group">
              <label><i class="fas fa-envelope"></i> البريد الإلكتروني</label>
              <input type="email" name="email" placeholder="example@email.com" required />
            </div>

            <div class="form-group">
              <label><i class="fas fa-phone"></i> رقم الهاتف</label>
              <input type="tel" name="phone" placeholder="05xxxxxxxx" pattern="05[0-9]{8}" />
            </div>

            <div class="form-group">
              <label><i class="fas fa-lock"></i> كلمة المرور</label>
              <input type="password" name="password" id="registerPassword" placeholder="6 أحرف على الأقل" required minlength="6" />
            </div>

            <div class="form-group">
              <label><i class="fas fa-lock"></i> تأكيد كلمة المرور</label>
              <input type="password" name="confirm_password" id="confirmPassword" placeholder="أعد إدخال كلمة المرور" required />
            </div>

            <div class="form-group">
              <label><i class="fas fa-user-tag"></i> نوع الحساب</label>
              <div class="role-selection">
                <label class="role-option">
                  <input type="radio" name="role" value="seeker" checked required />
                  <div class="role-card">
                    <i class="fas fa-user"></i>
                    <span>باحث عن عمل</span>
                  </div>
                </label>
                <label class="role-option">
                  <input type="radio" name="role" value="employer" required />
                  <div class="role-card">
                    <i class="fas fa-building"></i>
                    <span>صاحب عمل</span>
                  </div>
                </label>
              </div>
            </div>

            <div class="form-group">
              <label><i class="fas fa-question-circle"></i> السؤال الأمني (اختياري)</label>
              <select name="security_question">
                <option value="">اختر سؤالاً أمنياً...</option>
                <option value="ما اسم مدينتك؟">ما اسم مدينتك؟</option>
                <option value="ما اسم حيوانك الأليف؟">ما اسم حيوانك الأليف؟</option>
                <option value="ما اسم أفضل صديق لك؟">ما اسم أفضل صديق لك؟</option>
                <option value="ما اسم مدرستك الأولى؟">ما اسم مدرستك الأولى؟</option>
              </select>
            </div>

            <div class="form-group">
              <label><i class="fas fa-key"></i> إجابة السؤال الأمني</label>
              <input type="text" name="security_answer" placeholder="أدخل إجابتك" />
            </div>

            <button type="submit" class="btn-submit">
              <i class="fas fa-user-plus"></i>
              إنشاء الحساب
            </button>
          </form>

          <p class="auth-footer">
            لديك حساب بالفعل؟
            <a href="#" onclick="showPage('login')">تسجيل الدخول</a>
          </p>
        </div>
      </div>
    </div>

    <!-- صفحة استعادة كلمة المرور -->
    <div id="resetPasswordPage" class="page">
      <div class="auth-container">
        <div class="auth-card fade-in-up">
          <div id="resetAlertBox"></div>

          <!-- الخطوة 1 -->
          <div class="reset-step active" id="resetStep1">
            <div class="auth-header">
              <i class="fas fa-lock"></i>
              <h2>استعادة كلمة المرور</h2>
              <p>ابحث عن حسابك</p>
            </div>

            <div class="form-group">
              <label>البريد الإلكتروني أو رقم الهاتف</label>
              <input type="text" id="resetIdentifier" placeholder="أدخل بريدك الإلكتروني أو رقم الهاتف" required />
            </div>
            
            <button class="btn-submit" onclick="checkResetOptions()">
              <i class="fas fa-arrow-left"></i>
              التالي
            </button>
            
            <p class="auth-footer">
              <a href="#" onclick="showPage('login')">العودة لتسجيل الدخول</a>
            </p>
          </div>

          <!-- الخطوة 2 -->
          <div class="reset-step" id="resetStep2">
            <div class="auth-header">
              <i class="fas fa-shield-alt"></i>
              <h2>اختر طريقة الاستعادة</h2>
            </div>

            <div id="resetOptionsContainer"></div>

            <button class="btn-submit" onclick="proceedWithResetMethod()" id="proceedResetBtn" disabled>
              التالي
            </button>
            
            <button class="btn-submit btn-secondary" onclick="goToResetStep(1)" style="margin-top: 10px;">
              رجوع
            </button>
          </div>

          <!-- الخطوة 3أ: OTP -->
          <div class="reset-step" id="resetStep3a">
            <div class="auth-header">
              <i class="fas fa-mobile-alt"></i>
              <h2>أدخل رمز التحقق</h2>
              <p>تم إرسال رمز التحقق إلى <span id="resetSentTo"></span></p>
            </div>

            <div class="otp-inputs">
              <input type="text" class="otp-input" maxlength="1" id="otp1" />
              <input type="text" class="otp-input" maxlength="1" id="otp2" />
              <input type="text" class="otp-input" maxlength="1" id="otp3" />
              <input type="text" class="otp-input" maxlength="1" id="otp4" />
              <input type="text" class="otp-input" maxlength="1" id="otp5" />
              <input type="text" class="otp-input" maxlength="1" id="otp6" />
            </div>

            <button class="btn-submit" onclick="verifyResetOTP()">
              تحقق
            </button>
            
            <button class="btn-submit btn-secondary" onclick="goToResetStep(2)" style="margin-top: 10px;">
              رجوع
            </button>
          </div>

          <!-- الخطوة 3ب: السؤال الأمني -->
          <div class="reset-step" id="resetStep3b">
            <div class="auth-header">
              <i class="fas fa-question-circle"></i>
              <h2>أجب على السؤال الأمني</h2>
            </div>

            <div class="security-question-box">
              <div class="question" id="resetSecurityQuestion"></div>
            </div>

            <div class="form-group">
              <label>إجابتك</label>
              <input type="text" id="resetSecurityAnswer" placeholder="أدخل إجابتك" />
            </div>

            <button class="btn-submit" onclick="verifyResetSecurityAnswer()">
              تحقق
            </button>
            
            <button class="btn-submit btn-secondary" onclick="goToResetStep(2)" style="margin-top: 10px;">
              رجوع
            </button>
          </div>

          <!-- الخطوة 4 -->
          <div class="reset-step" id="resetStep4">
            <div class="auth-header">
              <i class="fas fa-key"></i>
              <h2>إنشاء كلمة مرور جديدة</h2>
            </div>

            <div class="form-group">
              <label>كلمة المرور الجديدة</label>
              <input type="password" id="resetNewPassword" placeholder="أدخل كلمة المرور الجديدة" minlength="6" />
            </div>

            <div class="form-group">
              <label>تأكيد كلمة المرور</label>
              <input type="password" id="resetConfirmPassword" placeholder="أعد إدخال كلمة المرور" />
            </div>

            <button class="btn-submit" onclick="submitNewPassword()">
              تغيير كلمة المرور
            </button>
          </div>

          <!-- الخطوة 5 -->
          <div class="reset-step" id="resetStep5">
            <div style="text-align: center; padding: 40px 0;">
              <div style="font-size: 60px; color: #4caf50; margin-bottom: 20px;">✓</div>
              <h2 style="color: #4caf50; margin-bottom: 15px;">تم بنجاح!</h2>
              <p style="color: #666; margin-bottom: 30px;">تم تغيير كلمة المرور بنجاح</p>
              <button class="btn-submit" onclick="showPage('login')">
                تسجيل الدخول
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- صفحة لوحة التحكم - محسّنة -->
    <div id="dashboardPage" class="page">
      <div class="page-container">
        <!-- ترحيب -->
        <div class="dashboard-welcome fade-in-up">
          <h1>مرحباً بك، <span id="dashboardUserName"></span>! 👋</h1>
          <p>نتمنى لك يوماً مثمراً في رحلة البحث عن الوظيفة المثالية</p>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="stats-grid fade-in-up" id="dashboardStats"></div>

        <!-- إجراءات سريعة -->
        <h2 style="margin-bottom: 25px; color: #333;">
          <i class="fas fa-bolt" style="color: #667eea;"></i>
          إجراءات سريعة
        </h2>
        <div class="quick-actions fade-in-up" id="quickActions"></div>

        <!-- الأنشطة الأخيرة -->
        <div class="recent-activity fade-in-up">
          <h3>
            <i class="fas fa-clock" style="color: #667eea;"></i>
            آخر الأنشطة
          </h3>
          <div id="recentActivities"></div>
        </div>
      </div>
    </div>

    <!-- باقي الصفحات (Jobs, Profile, إلخ) -->
    <!-- سأضيف نسخة مختصرة منها -->
    
    <div id="jobsPage" class="page">
      <div class="page-container">
        <div class="page-header fade-in-up">
          <h1><i class="fas fa-search"></i> تصفح الوظائف</h1>
          <p style="color: #666; font-size: 16px;">اعثر على الوظيفة المثالية لك</p>
        </div>

        <!-- مربع البحث -->
        <div style="background: white; padding: 30px; border-radius: 20px; margin-bottom: 30px; box-shadow: 0 5px 25px rgba(0,0,0,0.08);">
          <form onsubmit="handleJobSearch(event)">
            <div style="display: grid; grid-template-columns: 1fr 200px 200px auto; gap: 15px;">
              <input type="text" name="search" placeholder="ابحث عن وظيفة..." style="padding: 15px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 15px;" />
              <select name="type" style="padding: 15px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 15px;">
                <option value="">كل الأنواع</option>
                <option value="دوام كامل">دوام كامل</option>
                <option value="دوام جزئي">دوام جزئي</option>
                <option value="عن بُعد">عن بُعد</option>
              </select>
              <input type="text" name="location" placeholder="الموقع" style="padding: 15px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 15px;" />
              <button type="submit" class="btn-primary">
                <i class="fas fa-search"></i>
                بحث
              </button>
            </div>
          </form>
        </div>

        <div id="jobsList"></div>
      </div>
    </div>

    <div id="profilePage" class="page">
  <div class="page-container">
    <div class="page-header fade-in-up">
      <h1><i class="fas fa-user"></i> الملف الشخصي</h1>
      <p style="color: #666; font-size: 16px;">أكمل ملفك الشخصي لزيادة فرصك</p>
    </div>

    <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 5px 25px rgba(0,0,0,0.08);">
      <form id="profileForm" onsubmit="handleSaveProfile(event)" enctype="multipart/form-data">

        <h3 style="margin-bottom: 25px; color: #333; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">
          <i class="fas fa-info-circle" style="color: #667eea;"></i>
          المعلومات الأساسية
        </h3>

        <div class="form-group">
          <label>الاسم</label>
          <input type="text" name="name" required />
        </div>

        <div class="form-group">
          <label>البريد الإلكتروني</label>
          <input type="email" name="email" readonly style="background: #f5f5f5;" />
        </div>

        <div class="form-group">
          <label>رقم الهاتف</label>
          <input type="tel" name="phone" />
        </div>

        <div class="form-group">
          <label>الموقع</label>
          <input type="text" name="location" placeholder="مثال: الرياض، السعودية" />
        </div>

        <div class="form-group">
          <label>نبذة عني</label>
          <textarea name="bio" rows="4" placeholder="اكتب نبذة مختصرة عنك..."></textarea>
        </div>

        <!-- رفع السيرة الذاتية PDF -->
        <div class="form-group">
          <label>
            <i class="fas fa-file-pdf" style="color: #e74c3c;"></i>
            السيرة الذاتية (PDF)
          </label>

          <div style="display: flex; gap: 10px; align-items: center;">
            <input type="file" id="pdf" name="pdf" accept=".pdf" 
            style="flex: 1; padding: 12px; border: 2px dashed #ddd; border-radius: 10px; background: #f9f9f9;" />
            <span id="resumePdfStatus" style="color: #666; font-size: 14px;"></span>
          </div>

          <small style="color: #999; display: block; margin-top: 8px;">
            <i class="fas fa-info-circle"></i>
            الحد الأقصى: 5 ميجابايت | الصيغة المقبولة: PDF فقط
          </small>

          <div id="currentResumeLink" style="margin-top: 10px;"></div>
        </div>

        <div class="form-group">
          <label>LinkedIn</label>
          <input type="url" name="linkedin_url" placeholder="https://linkedin.com/in/..." />
        </div>

        <div class="form-group">
          <label>GitHub</label>
          <input type="url" name="github_url" placeholder="https://github.com/..." />
        </div>

        <div class="form-group">
          <label>الموقع الشخصي</label>
          <input type="url" name="website_url" placeholder="https://..." />
        </div>

        <h3 style="margin: 40px 0 25px; color: #333; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">
          <i class="fas fa-graduation-cap" style="color: #667eea;"></i>
          التعليم
        </h3>
        <div id="educationList"></div>
        <button type="button" onclick="addEducationField()" class="btn-secondary" style="margin-bottom: 30px;">
          <i class="fas fa-plus"></i> إضافة تعليم
        </button>

        <h3 style="margin: 40px 0 25px; color: #333; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">
          <i class="fas fa-briefcase" style="color: #667eea;"></i>
          الخبرات
        </h3>
        <div id="experienceList"></div>
        <button type="button" onclick="addExperienceField()" class="btn-secondary" style="margin-bottom: 30px;">
          <i class="fas fa-plus"></i> إضافة خبرة
        </button>

        <h3 style="margin: 40px 0 25px; color: #333; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">
          <i class="fas fa-tools" style="color: #667eea;"></i>
          المهارات
        </h3>
        <div id="skillsList"></div>
        <button type="button" onclick="addSkillField()" class="btn-secondary" style="margin-bottom: 30px;">
          <i class="fas fa-plus"></i> إضافة مهارة
        </button>

        <button type="submit" class="btn-primary" style="width: 100%; padding: 18px; font-size: 16px;">
          <i class="fas fa-save"></i>
          حفظ التغييرات
        </button>
      </form>
    </div>
  </div>
</div>
<!-- Fixed Profile Page HTML -->
<div id="profilePage" class="page">
  <div class="page-container">
    <div class="page-header fade-in-up">
      <h1><i class="fas fa-user"></i> الملف الشخصي</h1>
      <p style="color: #666; font-size: 16px;">أكمل ملفك الشخصي لزيادة فرصك</p>
    </div>

    <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 5px 25px rgba(0,0,0,0.08);">
      <form id="profileForm" onsubmit="handleSaveProfile(event)">
        <h3 style="margin-bottom: 25px; color: #333; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">
          <i class="fas fa-info-circle" style="color: #667eea;"></i>
          المعلومات الأساسية
        </h3>

        <div class="form-group">
          <label>الاسم</label>
          <input type="text" name="name" required />
        </div>

        <div class="form-group">
          <label>البريد الإلكتروني</label>
          <input type="email" name="email" readonly style="background: #f5f5f5;" />
        </div>

        <div class="form-group">
          <label>رقم الهاتف</label>
          <input type="tel" name="phone" />
        </div>

        <div class="form-group">
          <label>الموقع</label>
          <input type="text" name="location" placeholder="مثال: الرياض، السعودية" />
        </div>

        <div class="form-group">
          <label>نبذة عني</label>
          <textarea name="bio" rows="4" placeholder="اكتب نبذة مختصرة عنك..."></textarea>
        </div>

        <!-- Upload PDF Section (Fixed) -->
        <div class="form-group">
          <label>
            <i class="fas fa-file-pdf" style="color: #e74c3c;"></i>
            السيرة الذاتية (PDF)
          </label>

          <div style="display: flex; gap: 10px; align-items: center;">
            <input type="file" id="resume" name="resume" accept="application/pdf" 
              style="flex: 1; padding: 12px; border: 2px dashed #ddd; border-radius: 10px; background: #f9f9f9;" />

            <span id="resumePdfStatus" style="color: #666; font-size: 14px;"></span>
          </div>

          <small style="color: #999; display: block; margin-top: 8px;">
            <i class="fas fa-info-circle"></i>
            الحد الأقصى: 5 ميجابايت | الصيغة المقبولة: PDF فقط
          </small>

          <div id="currentResumeLink" style="margin-top: 10px;"></div>
        </div>

        <div class="form-group">
          <label>LinkedIn</label>
          <input type="url" name="linkedin_url" placeholder="https://linkedin.com/in/..." />
        </div>

        <div class="form-group">
          <label>GitHub</label>
          <input type="url" name="github_url" placeholder="https://github.com/..." />
        </div>

        <div class="form-group">
          <label>الموقع الشخصي</label>
          <input type="url" name="website_url" placeholder="https://..." />
        </div>

        <h3 style="margin: 40px 0 25px; color: #333; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">
          <i class="fas fa-graduation-cap" style="color: #667eea;"></i>
          التعليم
        </h3>
        <div id="educationList"></div>
        <button type="button" onclick="addEducationField()" class="btn-secondary" style="margin-bottom: 30px;">
          <i class="fas fa-plus"></i> إضافة تعليم
        </button>

        <h3 style="margin: 40px 0 25px; color: #333; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">
          <i class="fas fa-briefcase" style="color: #667eea;"></i>
          الخبرات
        </h3>
        <div id="experienceList"></div>
        <button type="button" onclick="addExperienceField()" class="btn-secondary" style="margin-bottom: 30px;">
          <i class="fas fa-plus"></i> إضافة خبرة
        </button>

        <h3 style="margin: 40px 0 25px; color: #333; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">
          <i class="fas fa-tools" style="color: #667eea;"></i>
          المهارات
        </h3>
        <div id="skillsList"></div>
        <button type="button" onclick="addSkillField()" class="btn-secondary" style="margin-bottom: 30px;">
          <i class="fas fa-plus"></i> إضافة مهارة
        </button>

        <button type="submit" class="btn-primary" style="width: 100%; padding: 18px; font-size: 16px;">
          <i class="fas fa-save"></i>
          حفظ التغييرات
        </button>
      </form>
    </div>
  </div>
</div>


    <div id="applicationsPage" class="page">
      <div class="page-container">
        <div class="page-header fade-in-up">
          <h1><i class="fas fa-file-alt"></i> طلباتي</h1>
          <p style="color: #666; font-size: 16px;">تابع حالة طلباتك</p>
        </div>
        <div id="myApplicationsList"></div>
      </div>
    </div>

    <div id="savedJobsPage" class="page">
      <div class="page-container">
        <div class="page-header fade-in-up">
          <h1><i class="fas fa-bookmark"></i> الوظائف المحفوظة</h1>
          <p style="color: #666; font-size: 16px;">الوظائف التي قمت بحفظها</p>
        </div>
        <div id="savedJobsList"></div>
      </div>
    </div>

    <div id="postJobPage" class="page">
      <div class="page-container">
        <div class="page-header fade-in-up">
          <h1><i class="fas fa-plus-circle"></i> نشر وظيفة جديدة</h1>
          <p style="color: #666; font-size: 16px;">انشر وظيفة واعثر على المرشح المثالي</p>
        </div>

        <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 5px 25px rgba(0,0,0,0.08);">
          <form id="postJobForm" onsubmit="handlePostJob(event)">
            <div class="form-group">
              <label>عنوان الوظيفة *</label>
              <input type="text" name="title" placeholder="مثال: مطور Full Stack" required />
            </div>

            <div class="form-group">
              <label>اسم الشركة *</label>
              <input type="text" name="company" placeholder="مثال: شركة التقنية" required />
            </div>

            <div class="form-group">
              <label>الموقع</label>
              <input type="text" name="location" placeholder="مثال: الرياض، السعودية" />
            </div>

            <div class="form-group">
              <label>الراتب</label>
              <input type="text" name="salary" placeholder="مثال: 10,000 - 15,000 ريال" />
            </div>

            <div class="form-group">
              <label>نوع الوظيفة</label>
              <select name="type">
                <option value="دوام كامل">دوام كامل</option>
                <option value="دوام جزئي">دوام جزئي</option>
                <option value="عن بُعد">عن بُعد</option>
                <option value="تدريب">تدريب</option>
              </select>
            </div>

            <div class="form-group">
              <label>الوصف الوظيفي *</label>
              <textarea name="description" rows="6" placeholder="اكتب وصفاً تفصيلياً للوظيفة..." required></textarea>
            </div>

            <div class="form-group">
              <label>المتطلبات</label>
              <textarea name="requirements" rows="4" placeholder="اذكر متطلبات الوظيفة..."></textarea>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 18px; font-size: 16px;">
              <i class="fas fa-paper-plane"></i>
              نشر الوظيفة
            </button>
          </form>
        </div>
      </div>
    </div>

    <div id="myJobsPage" class="page">
      <div class="page-container">
        <div class="page-header fade-in-up">
          <h1><i class="fas fa-briefcase"></i> وظائفي</h1>
          <p style="color: #666; font-size: 16px;">إدارة الوظائف التي نشرتها</p>
        </div>
        <div id="myJobsList"></div>
      </div>
    </div>

    <!-- النوافذ المنبثقة -->
    <div id="jobDetailsModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>تفاصيل الوظيفة</h2>
          <button onclick="closeModal('jobDetailsModal')" class="modal-close">×</button>
        </div>
        <div class="modal-body"></div>
      </div>
    </div>

    <div id="applicationsModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>المتقدمون</h2>
          <button onclick="closeModal('applicationsModal')" class="modal-close">×</button>
        </div>
        <div class="modal-body">
          <div id="applicationsList"></div>
        </div>
      </div>
    </div>

    <script src="script-fixed.js"></script>
    <script>
      // السكريبت الخاص باستعادة كلمة المرور
      let resetUserData = {};
      let selectedResetMethod = null;
      let resetToken = null;

      function goToResetStep(stepNum) {
        document.querySelectorAll('.reset-step').forEach(step => step.classList.remove('active'));
        document.getElementById(`resetStep${stepNum}`).classList.add('active');
      }

      function showResetAlert(message, type = 'error') {
        const alertBox = document.getElementById('resetAlertBox');
        alertBox.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        setTimeout(() => {
          alertBox.innerHTML = '';
        }, 5000);
      }

      async function checkResetOptions() {
        const identifier = document.getElementById('resetIdentifier').value.trim();
        
        if (!identifier) {
          showResetAlert('يرجى إدخال البريد الإلكتروني أو رقم الهاتف');
          return;
        }

        try {
          const response = await fetch(`${API_URL}?action=checkResetOptions`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ identifier })
          });

          const data = await response.json();

          if (data.error) {
            showResetAlert(data.error);
            return;
          }

          resetUserData = data;
          displayResetOptions(data.options);
          goToResetStep(2);

        } catch (error) {
          showResetAlert('حدث خطأ في الاتصال بالخادم');
        }
      }

      function displayResetOptions(options) {
        const container = document.getElementById('resetOptionsContainer');
        container.innerHTML = '';

        const icons = {
          email: '📧',
          phone: '📱',
          security: '❓'
        };

        const labels = {
          email: 'البريد الإلكتروني',
          phone: 'رسالة نصية (SMS)',
          security: 'السؤال الأمني'
        };

        options.forEach(option => {
          const card = document.createElement('div');
          card.className = 'option-card';
          card.innerHTML = `
            <div class="option-icon">${icons[option.type]}</div>
            <div class="option-content">
              <div class="option-title">${labels[option.type]}</div>
              <div class="option-desc">
                ${option.type === 'security' ? option.question : option.masked || option.value}
              </div>
            </div>
          `;
          card.onclick = () => selectResetOption(option.type, card);
          container.appendChild(card);
        });
      }

      function selectResetOption(type, card) {
        document.querySelectorAll('.option-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        selectedResetMethod = type;
        document.getElementById('proceedResetBtn').disabled = false;
      }

      async function proceedWithResetMethod() {
        if (!selectedResetMethod) {
          showResetAlert('يرجى اختيار طريقة الاستعادة');
          return;
        }

        if (selectedResetMethod === 'security') {
          const securityOption = resetUserData.options.find(o => o.type === 'security');
          document.getElementById('resetSecurityQuestion').textContent = securityOption.question;
          goToResetStep('3b');
        } else {
          await sendResetCode(selectedResetMethod);
        }
      }

      async function sendResetCode(method) {
        try {
          const response = await fetch(`${API_URL}?action=sendResetCode`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              email: resetUserData.email,
              method: method
            })
          });

          const data = await response.json();

          if (data.error) {
            showResetAlert(data.error);
            return;
          }

          resetToken = data.token;
          const sentTo = method === 'phone' ? resetUserData.phone : resetUserData.email;
          document.getElementById('resetSentTo').textContent = sentTo;
          
          if (data.otp) {
            showResetAlert(`رمز التحقق (للتجربة): ${data.otp}`, 'info');
          }
          
          goToResetStep('3a');
          setupResetOTPInputs();

        } catch (error) {
          showResetAlert('حدث خطأ في الاتصال بالخادم');
        }
      }

      function setupResetOTPInputs() {
        const inputs = document.querySelectorAll('.otp-input');
        inputs.forEach((input, index) => {
          input.value = '';
          input.addEventListener('input', (e) => {
            if (e.target.value.length === 1 && index < inputs.length - 1) {
              inputs[index + 1].focus();
            }
          });
          input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
              inputs[index - 1].focus();
            }
          });
        });
        inputs[0].focus();
      }

      async function verifyResetOTP() {
        const otp = Array.from(document.querySelectorAll('.otp-input'))
          .map(input => input.value)
          .join('');

        if (otp.length !== 6) {
          showResetAlert('يرجى إدخال رمز التحقق كاملاً');
          return;
        }

        try {
          const response = await fetch(`${API_URL}?action=verifyOTP`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              token: resetToken,
              otp: otp
            })
          });

          const data = await response.json();

          if (data.error) {
            showResetAlert(data.error);
            return;
          }

          showResetAlert('تم التحقق بنجاح!', 'success');
          resetToken = data.token;
          goToResetStep(4);

        } catch (error) {
          showResetAlert('حدث خطأ في الاتصال بالخادم');
        }
      }

      async function verifyResetSecurityAnswer() {
        const answer = document.getElementById('resetSecurityAnswer').value.trim();

        if (!answer) {
          showResetAlert('يرجى إدخال الإجابة');
          return;
        }

        try {
          const response = await fetch(`${API_URL}?action=verifySecurityAnswer`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              email: resetUserData.email,
              answer: answer
            })
          });

          const data = await response.json();

          if (data.error) {
            showResetAlert(data.error);
            return;
          }

          showResetAlert('تم التحقق بنجاح!', 'success');
          resetToken = data.token;
          goToResetStep(4);

        } catch (error) {
          showResetAlert('حدث خطأ في الاتصال بالخادم');
        }
      }

      async function submitNewPassword() {
        const newPassword = document.getElementById('resetNewPassword').value;
        const confirmPassword = document.getElementById('resetConfirmPassword').value;

        if (!newPassword || !confirmPassword) {
          showResetAlert('يرجى ملء جميع الحقول');
          return;
        }

        if (newPassword.length < 6) {
          showResetAlert('كلمة المرور يجب أن تكون 6 أحرف على الأقل');
          return;
        }

        if (newPassword !== confirmPassword) {
          showResetAlert('كلمات المرور غير متطابقة');
          return;
        }

        try {
          const response = await fetch(`${API_URL}?action=resetPassword`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              token: resetToken,
              password: newPassword
            })
          });

          const data = await response.json();

          if (data.error) {
            showResetAlert(data.error);
            return;
          }

          goToResetStep(5);

        } catch (error) {
          showResetAlert('حدث خطأ في الاتصال بالخادم');
        }
      }

      function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = event.target.closest('button').querySelector('i');
        
        if (input.type === 'password') {
          input.type = 'text';
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
        } else {
          input.type = 'password';
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        }
      }

      function toggleUserMenu() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('active');
      }

      // إغلاق القوائم المنسدلة عند النقر خارجها
      document.addEventListener('click', function(event) {
        if (!event.target.closest('.user-menu')) {
          document.getElementById('userDropdown')?.classList.remove('active');
        }
        if (!event.target.closest('.notification-bell') && !event.target.closest('.notifications-panel')) {
          document.getElementById('notificationsPanel')?.classList.remove('active');
        }
      });

      // دوال Dashboard
      function loadDashboardContent() {
        const user = APP_STATE.currentUser;
        if (!user) return;

        // عرض اسم المستخدم
        document.getElementById('dashboardUserName').textContent = user.name;

        // الإحصائيات
        loadDashboardStats(user.role);

        // الإجراءات السريعة
        loadQuickActions(user.role);

        // الأنشطة الأخيرة
        loadRecentActivities(user.role);
      }

      function loadDashboardStats(role) {
        const statsContainer = document.getElementById('dashboardStats');
        
        if (role === 'seeker') {
          statsContainer.innerHTML = `
            <div class="stat-card">
              <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-file-alt"></i>
              </div>
              <div class="stat-number" id="myApplicationsCount">0</div>
              <div class="stat-label">طلباتي</div>
            </div>
            <div class="stat-card">
              <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <i class="fas fa-bookmark"></i>
              </div>
              <div class="stat-number" id="savedJobsCount">0</div>
              <div class="stat-label">الوظائف المحفوظة</div>
            </div>
            <div class="stat-card">
              <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <i class="fas fa-eye"></i>
              </div>
              <div class="stat-number" id="profileViews">0</div>
              <div class="stat-label">مشاهدات الملف الشخصي</div>
            </div>
            <div class="stat-card">
              <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <i class="fas fa-check-circle"></i>
              </div>
              <div class="stat-number" id="completionRate">0%</div>
              <div class="stat-label">اكتمال الملف الشخصي</div>
            </div>
          `;

          // تحميل الأرقام الفعلية
          if (APP_STATE.applications) {
            document.getElementById('myApplicationsCount').textContent = APP_STATE.applications.length;
          }
          if (APP_STATE.savedJobs) {
            document.getElementById('savedJobsCount').textContent = APP_STATE.savedJobs.length;
          }
        } else {
          statsContainer.innerHTML = `
            <div class="stat-card">
              <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-briefcase"></i>
              </div>
              <div class="stat-number">0</div>
              <div class="stat-label">الوظائف المنشورة</div>
            </div>
            <div class="stat-card">
              <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <i class="fas fa-users"></i>
              </div>
              <div class="stat-number">0</div>
              <div class="stat-label">إجمالي المتقدمين</div>
            </div>
            <div class="stat-card">
              <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <i class="fas fa-eye"></i>
              </div>
              <div class="stat-number">0</div>
              <div class="stat-label">مشاهدات الوظائف</div>
            </div>
            <div class="stat-card">
              <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <i class="fas fa-chart-line"></i>
              </div>
              <div class="stat-number">0%</div>
              <div class="stat-label">معدل الاستجابة</div>
            </div>
          `;
        }
      }

      function loadQuickActions(role) {
        const actionsContainer = document.getElementById('quickActions');
        
        if (role === 'seeker') {
          actionsContainer.innerHTML = `
            <a href="#" onclick="showPage('jobs')" class="quick-action-btn">
              <i class="fas fa-search"></i>
              <div>تصفح الوظائف</div>
            </a>
            <a href="#" onclick="showPage('profile')" class="quick-action-btn">
              <i class="fas fa-user-edit"></i>
              <div>تحديث الملف الشخصي</div>
            </a>
            <a href="#" onclick="showPage('applications')" class="quick-action-btn">
              <i class="fas fa-file-alt"></i>
              <div>طلباتي</div>
            </a>
            <a href="#" onclick="showPage('savedJobs')" class="quick-action-btn">
              <i class="fas fa-bookmark"></i>
              <div>الوظائف المحفوظة</div>
            </a>
          `;
        } else {
          actionsContainer.innerHTML = `
            <a href="#" onclick="showPage('postJob')" class="quick-action-btn">
              <i class="fas fa-plus-circle"></i>
              <div>نشر وظيفة جديدة</div>
            </a>
            <a href="#" onclick="showPage('myJobs')" class="quick-action-btn">
              <i class="fas fa-briefcase"></i>
              <div>وظائفي</div>
            </a>
            <a href="#" onclick="showPage('applications')" class="quick-action-btn">
              <i class="fas fa-users"></i>
              <div>المتقدمون</div>
            </a>
          `;
        }
      }

      function loadRecentActivities(role) {
        const activitiesContainer = document.getElementById('recentActivities');
        
        // عرض نشاطات وهمية لحين تحميل البيانات الفعلية
        activitiesContainer.innerHTML = `
          <div class="activity-item">
            <div class="activity-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
              <i class="fas fa-bell"></i>
            </div>
            <div class="activity-content">
              <div class="activity-title">مرحباً بك في HireHub!</div>
              <div class="activity-time">الآن</div>
            </div>
          </div>
        `;
      }

      // تحديث دالة initializeUserSession
      const originalInitializeUserSession = window.initializeUserSession;
      window.initializeUserSession = function() {
        if (originalInitializeUserSession) {
          originalInitializeUserSession();
        }
        loadDashboardContent();
      };
    </script>
  </body>
</html>


   