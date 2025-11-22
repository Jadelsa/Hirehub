// ==========================================
// HireHub - Complete Fixed Version
// ==========================================

const APP_STATE = {
  currentUser: null,
  currentPage: "login",
  jobs: [],
  applications: [],
  savedJobs: [],
  notifications: [],
  currentJobId: null,
  profileData: null,
};

const API_URL = "api-fixed.php";

// ==================== بدء التطبيق ====================
document.addEventListener("DOMContentLoaded", function () {
  initializeApp();

  // إعداد معالجات الأحداث العامة
  setupGlobalHandlers();
});

function initializeApp() {
  const savedUser = localStorage.getItem("currentUser");
  if (savedUser) {
    try {
      APP_STATE.currentUser = JSON.parse(savedUser);
      initializeUserSession();
    } catch (e) {
      console.error("Error parsing saved user:", e);
      localStorage.removeItem("currentUser");
      showPage("login");
    }
  } else {
    showPage("login");
  }
}

function setupGlobalHandlers() {
  // معالج Escape للنوافذ المنبثقة
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      closeAllModals();
    }
  });
}

function initializeUserSession() {
  document.getElementById("mainNavbar").style.display = "block";
  document.getElementById("userName").textContent = APP_STATE.currentUser.name;
  setupRoleBasedNav();
  loadDashboardData();
  loadJobs();
  loadNotifications();
  showPage("dashboard");

  // بدء تحديث الإشعارات تلقائياً
  setInterval(loadNotifications, 30000); // كل 30 ثانية
}

function setupRoleBasedNav() {
  const role = APP_STATE.currentUser.role;

  if (role === "employer") {
    document.getElementById("postJobLink").style.display = "flex";
    document.getElementById("myJobsLink").style.display = "flex";
    document.getElementById("applicationsLink").style.display = "none";
    document.getElementById("savedJobsLink").style.display = "none";
  } else {
    document.getElementById("applicationsLink").style.display = "flex";
    document.getElementById("savedJobsLink").style.display = "flex";
    document.getElementById("postJobLink").style.display = "none";
    document.getElementById("myJobsLink").style.display = "none";
  }
}

// ==================== تسجيل الدخول ====================
async function handleLogin(event) {
  event.preventDefault();

  const formData = new FormData(event.target);
  const email = formData.get("email").trim();
  const password = formData.get("password");

  if (!email || !password) {
    showNotification("error", "خطأ", "يرجى ملء جميع الحقول");
    return;
  }

  try {
    const response = await fetch(`${API_URL}?action=login`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password }),
    });

    const data = await response.json();

    if (data.success && data.user) {
      APP_STATE.currentUser = data.user;
      localStorage.setItem("currentUser", JSON.stringify(data.user));

      showNotification(
        "success",
        "تم تسجيل الدخول بنجاح",
        `مرحباً ${data.user.name}!`
      );

      setTimeout(() => {
        initializeUserSession();
      }, 1000);
    } else {
      showNotification(
        "error",
        "خطأ في تسجيل الدخول",
        data.error || "البريد الإلكتروني أو كلمة المرور غير صحيحة"
      );
    }
  } catch (error) {
    console.error("Login error:", error);
    showNotification("error", "خطأ", "حدث خطأ في الاتصال بالخادم");
  }
}

// ==================== التسجيل ====================
async function handleRegister(event) {
  event.preventDefault();

  const formData = new FormData(event.target);
  const name = formData.get("name").trim();
  const email = formData.get("email").trim();
  const phone = formData.get("phone").trim();
  const password = formData.get("password");
  const confirmPassword = formData.get("confirm_password");
  const role = formData.get("role");
  const securityQuestion = formData.get("security_question");
  const securityAnswer = formData.get("security_answer").trim();

  if (!name || !email || !password || !role) {
    showNotification("error", "خطأ", "يرجى ملء جميع الحقول المطلوبة");
    return;
  }

  if (password !== confirmPassword) {
    showNotification("error", "خطأ", "كلمات المرور غير متطابقة");
    return;
  }

  if (password.length < 6) {
    showNotification(
      "error",
      "خطأ",
      "كلمة المرور يجب أن تكون 6 أحرف على الأقل"
    );
    return;
  }

  try {
    const response = await fetch(`${API_URL}?action=register`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        name,
        email,
        phone,
        password,
        role,
        security_question: securityQuestion,
        security_answer: securityAnswer,
      }),
    });

    const data = await response.json();

    if (data.success) {
      showNotification(
        "success",
        "تم التسجيل بنجاح",
        "يمكنك الآن تسجيل الدخول"
      );

      setTimeout(() => {
        showPage("login");
        event.target.reset();
      }, 2000);
    } else {
      showNotification("error", "خطأ في التسجيل", data.error);
    }
  } catch (error) {
    console.error("Register error:", error);
    showNotification("error", "خطأ", "حدث خطأ في الاتصال بالخادم");
  }
}

// ==================== تسجيل الخروج ====================
async function logout() {
  if (confirm("هل أنت متأكد من تسجيل الخروج؟")) {
    try {
      await fetch(`${API_URL}?action=logout`, { method: "POST" });
    } catch (e) {
      console.log("Logout API call failed, continuing anyway");
    }

    APP_STATE.currentUser = null;
    localStorage.removeItem("currentUser");
    document.getElementById("mainNavbar").style.display = "none";
    showPage("login");
    showNotification("success", "تم تسجيل الخروج", "نراك قريباً!");
  }
}

// ==================== الإشعارات ====================
async function loadNotifications() {
  if (!APP_STATE.currentUser) return;

  try {
    const response = await fetch(
      `${API_URL}?action=notifications&user_id=${APP_STATE.currentUser.id}`
    );
    const data = await response.json();

    if (data.notifications) {
      APP_STATE.notifications = data.notifications;
      updateNotificationBadge(data.unread_count);
    }
  } catch (error) {
    console.error("Error loading notifications:", error);
  }
}

function updateNotificationBadge(count) {
  const badge = document.getElementById("notificationBadge");
  if (badge) {
    if (count > 0) {
      badge.textContent = count > 99 ? "99+" : count;
      badge.style.display = "block";
    } else {
      badge.style.display = "none";
    }
  }
}

function showNotificationsPanel() {
  const panel = document.getElementById("notificationsPanel");
  const list = document.getElementById("notificationsList");

  if (!panel || !list) return;

  list.innerHTML = "";

  if (APP_STATE.notifications.length === 0) {
    list.innerHTML =
      '<div style="text-align: center; padding: 20px; color: #999;">لا توجد إشعارات</div>';
  } else {
    APP_STATE.notifications.forEach((notification) => {
      const div = document.createElement("div");
      div.className = `notification-item ${
        notification.is_read ? "" : "unread"
      }`;
      div.innerHTML = `
        <div class="notification-title">${notification.title}</div>
        <div class="notification-message">${notification.message}</div>
        <div class="notification-time">${formatDate(
          notification.created_at
        )}</div>
      `;

      div.onclick = () => markNotificationRead(notification.id);
      list.appendChild(div);
    });
  }

  panel.classList.add("active");
}

async function markNotificationRead(notificationId) {
  try {
    await fetch(`${API_URL}?action=markNotificationRead`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        notification_id: notificationId,
        user_id: APP_STATE.currentUser.id,
      }),
    });

    loadNotifications();
  } catch (error) {
    console.error("Error marking notification as read:", error);
  }
}

async function markAllNotificationsRead() {
  try {
    await fetch(`${API_URL}?action=markAllNotificationsRead`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        user_id: APP_STATE.currentUser.id,
      }),
    });

    loadNotifications();
    document.getElementById("notificationsPanel").classList.remove("active");
  } catch (error) {
    console.error("Error marking all notifications as read:", error);
  }
}

// ==================== لوحة التحكم ====================
async function loadDashboardData() {
  const role = APP_STATE.currentUser.role;

  if (role === "seeker") {
    // إحصائيات الباحث عن عمل
    loadMyApplications();
    loadSavedJobs();
  } else {
    // إحصائيات صاحب العمل
    loadMyJobs();
  }
}

// ==================== الوظائف ====================
async function loadJobs(search = "", type = "", location = "") {
  try {
    let url = `${API_URL}?action=jobs`;

    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (type) url += `&type=${encodeURIComponent(type)}`;
    if (location) url += `&location=${encodeURIComponent(location)}`;

    const response = await fetch(url);
    const data = await response.json();

    if (data.jobs) {
      APP_STATE.jobs = data.jobs;
      displayJobs(data.jobs);
    }
  } catch (error) {
    console.error("Error loading jobs:", error);
    showNotification("error", "خطأ", "فشل تحميل الوظائف");
  }
}

function displayJobs(jobs) {
  const container = document.getElementById("jobsList");
  if (!container) return;

  container.innerHTML = "";

  if (jobs.length === 0) {
    container.innerHTML = `
      <div style="text-align: center; padding: 40px; color: #999;">
        <div style="font-size: 48px; margin-bottom: 10px;">🔍</div>
        <p>لا توجد وظائف متاحة</p>
      </div>
    `;
    return;
  }

  jobs.forEach((job) => {
    const card = document.createElement("div");
    card.className = "job-card";
    card.innerHTML = `
      <div class="job-header">
        <h3>${job.title}</h3>
        <span class="job-type">${job.type}</span>
      </div>
      <div class="job-company">
        <span>🏢 ${job.company}</span>
      </div>
      <div class="job-details">
        <span>📍 ${job.location || "غير محدد"}</span>
        <span>💰 ${job.salary || "غير محدد"}</span>
      </div>
      <div class="job-description">
        ${job.description.substring(0, 150)}...
      </div>
      <div class="job-footer">
        <button onclick="viewJob(${job.id})" class="btn btn-primary">
          عرض التفاصيل
        </button>
        <span class="job-date">${formatDate(job.created_at)}</span>
      </div>
    `;
    container.appendChild(card);
  });
}

async function viewJob(jobId) {
  try {
    const response = await fetch(`${API_URL}?action=job&id=${jobId}`);
    const data = await response.json();

    if (data.job) {
      APP_STATE.currentJobId = jobId;
      displayJobDetails(data.job);
      document.getElementById("jobDetailsModal").classList.add("active");
    }
  } catch (error) {
    console.error("Error loading job details:", error);
    showNotification("error", "خطأ", "فشل تحميل تفاصيل الوظيفة");
  }
}

function displayJobDetails(job) {
  const modal = document.getElementById("jobDetailsModal");
  if (!modal) return;

  const isSeeker = APP_STATE.currentUser.role === "seeker";
  const isOwner = job.posted_by === APP_STATE.currentUser.id;

  modal.querySelector(".modal-body").innerHTML = `
    <div class="job-details-full">
      <div class="job-header-full">
        <h2>${job.title}</h2>
        <span class="job-type">${job.type}</span>
      </div>
      
      <div class="job-info-grid">
        <div class="info-item">
          <strong>🏢 الشركة:</strong> ${job.company}
        </div>
        <div class="info-item">
          <strong>📍 الموقع:</strong> ${job.location || "غير محدد"}
        </div>
        <div class="info-item">
          <strong>💰 الراتب:</strong> ${job.salary || "غير محدد"}
        </div>
        <div class="info-item">
          <strong>📅 تاريخ النشر:</strong> ${formatDate(job.created_at)}
        </div>
        <div class="info-item">
          <strong>👥 عدد المتقدمين:</strong> ${job.applications_count || 0}
        </div>
        <div class="info-item">
          <strong>👁️ المشاهدات:</strong> ${job.views_count || 0}
        </div>
      </div>

      <div class="job-section">
        <h3>الوصف الوظيفي</h3>
        <p>${job.description.replace(/\n/g, "<br>")}</p>
      </div>

      ${
        job.requirements
          ? `
        <div class="job-section">
          <h3>المتطلبات</h3>
          <p>${job.requirements.replace(/\n/g, "<br>")}</p>
        </div>
      `
          : ""
      }

      ${
        isSeeker && !isOwner
          ? `
        <div class="job-actions">
          <button onclick="applyForJob(${job.id})" class="btn btn-primary btn-lg">
            📝 التقديم على الوظيفة
          </button>
          <button onclick="saveJob(${job.id})" class="btn btn-secondary">
            ⭐ حفظ الوظيفة
          </button>
        </div>
      `
          : ""
      }

      ${
        isOwner
          ? `
        <div class="job-actions">
          <button onclick="viewJobApplications(${job.id})" class="btn btn-primary">
            👥 عرض المتقدمين
          </button>
          <button onclick="editJob(${job.id})" class="btn btn-secondary">
            ✏️ تعديل الوظيفة
          </button>
          <button onclick="deleteJob(${job.id})" class="btn btn-danger">
            🗑️ حذف الوظيفة
          </button>
        </div>
      `
          : ""
      }
    </div>
  `;
}

// ==================== التقديم على وظيفة ====================
async function applyForJob(jobId) {
  const coverLetter = prompt("رسالة التقديم (اختياري):");

  if (coverLetter === null) return; // المستخدم ألغى

  try {
    const response = await fetch(`${API_URL}?action=applyJob`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        job_id: jobId,
        user_id: APP_STATE.currentUser.id,
        cover_letter: coverLetter || "",
      }),
    });

    const data = await response.json();

    if (data.success) {
      showNotification(
        "success",
        "تم التقديم بنجاح",
        "تم إرسال طلبك إلى الشركة"
      );
      closeModal("jobDetailsModal");
      loadNotifications(); // تحديث الإشعارات
    } else {
      showNotification("error", "خطأ", data.error);
    }
  } catch (error) {
    console.error("Error applying for job:", error);
    showNotification("error", "خطأ", "فشل التقديم على الوظيفة");
  }
}

// ==================== حفظ وظيفة ====================
async function saveJob(jobId) {
  try {
    const response = await fetch(`${API_URL}?action=saveJob`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        job_id: jobId,
        user_id: APP_STATE.currentUser.id,
      }),
    });

    const data = await response.json();

    if (data.success) {
      showNotification("success", "تم الحفظ", "تم حفظ الوظيفة في قائمتك");
    } else {
      showNotification("error", "خطأ", data.error);
    }
  } catch (error) {
    console.error("Error saving job:", error);
    showNotification("error", "خطأ", "فشل حفظ الوظيفة");
  }
}

// ==================== نشر وظيفة جديدة ====================
async function handlePostJob(event) {
  event.preventDefault();

  const formData = new FormData(event.target);
  const jobData = {
    title: formData.get("title").trim(),
    company: formData.get("company").trim(),
    location: formData.get("location").trim(),
    salary: formData.get("salary").trim(),
    type: formData.get("type"),
    description: formData.get("description").trim(),
    requirements: formData.get("requirements").trim(),
    posted_by: APP_STATE.currentUser.id,
    draft: 0,
  };

  if (!jobData.title || !jobData.company || !jobData.description) {
    showNotification("error", "خطأ", "يرجى ملء جميع الحقول المطلوبة");
    return;
  }

  try {
    const response = await fetch(`${API_URL}?action=postJob`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(jobData),
    });

    const data = await response.json();

    if (data.success) {
      showNotification("success", "تم النشر بنجاح", "تم نشر الوظيفة بنجاح");
      event.target.reset();
      setTimeout(() => {
        showPage("myJobs");
        loadMyJobs();
      }, 1500);
    } else {
      showNotification("error", "خطأ", data.error);
    }
  } catch (error) {
    console.error("Error posting job:", error);
    showNotification("error", "خطأ", "فشل نشر الوظيفة");
  }
}

// ==================== وظائفي ====================
async function loadMyJobs() {
  try {
    const response = await fetch(
      `${API_URL}?action=myJobs&user_id=${APP_STATE.currentUser.id}`
    );
    const data = await response.json();

    if (data.jobs) {
      displayMyJobs(data.jobs);
    }
  } catch (error) {
    console.error("Error loading my jobs:", error);
  }
}

function displayMyJobs(jobs) {
  const container = document.getElementById("myJobsList");
  if (!container) return;

  container.innerHTML = "";

  if (jobs.length === 0) {
    container.innerHTML = `
      <div style="text-align: center; padding: 40px; color: #999;">
        <p>لم تقم بنشر أي وظائف بعد</p>
        <button onclick="showPage('postJob')" class="btn btn-primary" style="margin-top: 20px;">
          نشر وظيفة جديدة
        </button>
      </div>
    `;
    return;
  }

  jobs.forEach((job) => {
    const card = document.createElement("div");
    card.className = "job-card";
    card.innerHTML = `
      <div class="job-header">
        <h3>${job.title}</h3>
        <span class="job-type">${job.type}</span>
      </div>
      <div class="job-company">
        <span>🏢 ${job.company}</span>
      </div>
      <div class="job-details">
        <span>📍 ${job.location || "غير محدد"}</span>
        <span>👥 ${job.applications_count || 0} متقدم</span>
      </div>
      <div class="job-footer">
        <button onclick="viewJob(${job.id})" class="btn btn-primary">
          عرض التفاصيل
        </button>
        <button onclick="viewJobApplications(${
          job.id
        })" class="btn btn-secondary">
          عرض المتقدمين
        </button>
        <span class="job-date">${formatDate(job.created_at)}</span>
      </div>
    `;
    container.appendChild(card);
  });
}

// ==================== عرض المتقدمين ====================
async function viewJobApplications(jobId) {
  try {
    const response = await fetch(
      `${API_URL}?action=jobApplications&job_id=${jobId}&user_id=${APP_STATE.currentUser.id}`
    );
    const data = await response.json();

    if (data.error) {
      showNotification("error", "خطأ", data.error);
      return;
    }

    if (data.applications) {
      displayApplications(data.applications);
      document.getElementById("applicationsModal").classList.add("active");
    }
  } catch (error) {
    console.error("Error loading applications:", error);
    showNotification("error", "خطأ", "فشل تحميل المتقدمين");
  }
}

function displayApplications(applications) {
  const container = document.getElementById("applicationsList");
  if (!container) return;

  container.innerHTML = "";

  if (applications.length === 0) {
    container.innerHTML = `
      <div style="text-align: center; padding: 40px; color: #999;">
        <p>لا يوجد متقدمين لهذه الوظيفة</p>
      </div>
    `;
    return;
  }

  applications.forEach((app) => {
    const card = document.createElement("div");
    card.className = "application-card";

    const statusColors = {
      pending: "#ffa726",
      reviewed: "#42a5f5",
      accepted: "#66bb6a",
      rejected: "#ef5350",
    };

    const statusLabels = {
      pending: "قيد المراجعة",
      reviewed: "تمت المراجعة",
      accepted: "مقبول",
      rejected: "مرفوض",
    };

    card.innerHTML = `
      <div class="applicant-header">
        <h4>${app.name}</h4>
        <span class="status-badge" style="background: ${
          statusColors[app.status]
        }">
          ${statusLabels[app.status]}
        </span>
      </div>
      <div class="applicant-info">
        <p><strong>📧 البريد:</strong> ${app.email}</p>
        ${app.phone ? `<p><strong>📱 الهاتف:</strong> ${app.phone}</p>` : ""}
        ${app.bio ? `<p><strong>💼 نبذة:</strong> ${app.bio}</p>` : ""}
        ${
          app.cover_letter
            ? `<p><strong>✉️ رسالة التقديم:</strong> ${app.cover_letter}</p>`
            : ""
        }
        <p><strong>📅 تاريخ التقديم:</strong> ${formatDate(app.created_at)}</p>
      </div>
      <div class="application-actions">
        <button onclick="updateApplicationStatus(${
          app.id
        }, 'reviewed')" class="btn btn-info btn-sm">
          مراجعة
        </button>
        <button onclick="updateApplicationStatus(${
          app.id
        }, 'accepted')" class="btn btn-success btn-sm">
          قبول
        </button>
        <button onclick="updateApplicationStatus(${
          app.id
        }, 'rejected')" class="btn btn-danger btn-sm">
          رفض
        </button>
      </div>
    `;
    container.appendChild(card);
  });
}

async function updateApplicationStatus(applicationId, status) {
  try {
    const response = await fetch(`${API_URL}?action=updateApplicationStatus`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        application_id: applicationId,
        status: status,
        user_id: APP_STATE.currentUser.id,
      }),
    });

    const data = await response.json();

    if (data.success) {
      showNotification("success", "تم التحديث", "تم تحديث حالة الطلب");
      // إعادة تحميل القائمة
      const jobId = APP_STATE.currentJobId;
      if (jobId) {
        viewJobApplications(jobId);
      }
    } else {
      showNotification("error", "خطأ", data.error);
    }
  } catch (error) {
    console.error("Error updating application status:", error);
    showNotification("error", "خطأ", "فشل تحديث حالة الطلب");
  }
}

// ==================== طلباتي ====================
async function loadMyApplications() {
  try {
    const response = await fetch(
      `${API_URL}?action=myApplications&user_id=${APP_STATE.currentUser.id}`
    );
    const data = await response.json();

    if (data.applications) {
      APP_STATE.applications = data.applications;
      displayMyApplications(data.applications);
    }
  } catch (error) {
    console.error("Error loading my applications:", error);
  }
}

function displayMyApplications(applications) {
  const container = document.getElementById("myApplicationsList");
  if (!container) return;

  container.innerHTML = "";

  if (applications.length === 0) {
    container.innerHTML = `
      <div style="text-align: center; padding: 40px; color: #999;">
        <p>لم تتقدم لأي وظائف بعد</p>
        <button onclick="showPage('jobs')" class="btn btn-primary" style="margin-top: 20px;">
          تصفح الوظائف
        </button>
      </div>
    `;
    return;
  }

  applications.forEach((app) => {
    const card = document.createElement("div");
    card.className = "application-card";

    const statusColors = {
      pending: "#ffa726",
      reviewed: "#42a5f5",
      accepted: "#66bb6a",
      rejected: "#ef5350",
    };

    const statusLabels = {
      pending: "قيد المراجعة",
      reviewed: "تمت المراجعة",
      accepted: "مقبول",
      rejected: "مرفوض",
    };

    card.innerHTML = `
      <div class="application-header">
        <h4>${app.title}</h4>
        <span class="status-badge" style="background: ${
          statusColors[app.status]
        }">
          ${statusLabels[app.status]}
        </span>
      </div>
      <div class="application-info">
        <p><strong>🏢 الشركة:</strong> ${app.company}</p>
        <p><strong>📍 الموقع:</strong> ${app.location || "غير محدد"}</p>
        <p><strong>💰 الراتب:</strong> ${app.salary || "غير محدد"}</p>
        <p><strong>📅 تاريخ التقديم:</strong> ${formatDate(app.created_at)}</p>
        ${
          app.cover_letter
            ? `<p><strong>✉️ رسالتك:</strong> ${app.cover_letter}</p>`
            : ""
        }
      </div>
      <div class="application-actions">
        <button onclick="viewJob(${app.job_id})" class="btn btn-primary btn-sm">
          عرض الوظيفة
        </button>
      </div>
    `;
    container.appendChild(card);
  });
}

// ==================== الوظائف المحفوظة ====================
async function loadSavedJobs() {
  try {
    const response = await fetch(
      `${API_URL}?action=savedJobs&user_id=${APP_STATE.currentUser.id}`
    );
    const data = await response.json();

    if (data.jobs) {
      APP_STATE.savedJobs = data.jobs;
      displaySavedJobs(data.jobs);
    }
  } catch (error) {
    console.error("Error loading saved jobs:", error);
  }
}

function displaySavedJobs(jobs) {
  const container = document.getElementById("savedJobsList");
  if (!container) return;

  container.innerHTML = "";

  if (jobs.length === 0) {
    container.innerHTML = `
      <div style="text-align: center; padding: 40px; color: #999;">
        <p>لم تقم بحفظ أي وظائف بعد</p>
        <button onclick="showPage('jobs')" class="btn btn-primary" style="margin-top: 20px;">
          تصفح الوظائف
        </button>
      </div>
    `;
    return;
  }

  jobs.forEach((job) => {
    const card = document.createElement("div");
    card.className = "job-card";
    card.innerHTML = `
      <div class="job-header">
        <h3>${job.title}</h3>
        <span class="job-type">${job.type}</span>
      </div>
      <div class="job-company">
        <span>🏢 ${job.company}</span>
      </div>
      <div class="job-details">
        <span>📍 ${job.location || "غير محدد"}</span>
        <span>💰 ${job.salary || "غير محدد"}</span>
      </div>
      <div class="job-footer">
        <button onclick="viewJob(${job.id})" class="btn btn-primary">
          عرض التفاصيل
        </button>
        <button onclick="unsaveJob(${job.id})" class="btn btn-danger">
          إلغاء الحفظ
        </button>
      </div>
    `;
    container.appendChild(card);
  });
}

async function unsaveJob(jobId) {
  if (!confirm("هل تريد إلغاء حفظ هذه الوظيفة؟")) return;

  try {
    const response = await fetch(`${API_URL}?action=unsaveJob`, {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        job_id: jobId,
        user_id: APP_STATE.currentUser.id,
      }),
    });

    const data = await response.json();

    if (data.success) {
      showNotification("success", "تم الإلغاء", "تم إلغاء حفظ الوظيفة");
      loadSavedJobs();
    } else {
      showNotification("error", "خطأ", data.error);
    }
  } catch (error) {
    console.error("Error unsaving job:", error);
    showNotification("error", "خطأ", "فشل إلغاء حفظ الوظيفة");
  }
}

// ==================== الملف الشخصي ====================
async function loadProfile() {
  try {
    const response = await fetch(
      `${API_URL}?action=profile&user_id=${APP_STATE.currentUser.id}`
    );
    const data = await response.json();

    if (data.error) {
      showNotification("error", "خطأ", data.error);
      return;
    }

    APP_STATE.profileData = data;
    displayProfile(data);
  } catch (error) {
    console.error("Error loading profile:", error);
    showNotification("error", "خطأ", "فشل تحميل الملف الشخصي");
  }
}

function displayProfile(data) {
  const form = document.getElementById("profileForm");
  if (!form) return;

  // معلومات أساسية
  form.querySelector('[name="name"]').value = data.user.name || "";
  form.querySelector('[name="email"]').value = data.user.email || "";
  form.querySelector('[name="phone"]').value = data.profile?.phone || "";
  form.querySelector('[name="location"]').value = data.profile?.location || "";
  form.querySelector('[name="bio"]').value = data.profile?.bio || "";
  form.querySelector('[name="linkedin_url"]').value =
    data.profile?.linkedin_url || "";
  form.querySelector('[name="github_url"]').value =
    data.profile?.github_url || "";
  form.querySelector('[name="website_url"]').value =
    data.profile?.website_url || "";

  // عرض السيرة الذاتية الحالية إن وجدت
  const currentResumeDiv = document.getElementById("currentResumeLink");
  if (currentResumeDiv) {
    if (data.profile?.resume_pdf) {
      currentResumeDiv.innerHTML = `
        <div style="background: #e8f5e9; padding: 12px; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
          <i class="fas fa-check-circle" style="color: #4caf50; font-size: 20px;"></i>
          <span style="color: #2e7d32; flex: 1;">تم رفع السيرة الذاتية بنجاح</span>
          <a href="${API_URL}?action=downloadResumePDF&user_id=${APP_STATE.currentUser.id}" 
             target="_blank"
             class="btn-secondary" 
             style="padding: 8px 16px; font-size: 14px; text-decoration: none;">
            <i class="fas fa-download"></i> تحميل
          </a>
        </div>
      `;
    } else {
      currentResumeDiv.innerHTML = `
        <div style="background: #fff3e0; padding: 12px; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
          <i class="fas fa-info-circle" style="color: #ff9800;"></i>
          <span style="color: #e65100;">لم يتم رفع سيرة ذاتية بعد</span>
        </div>
      `;
    }
  }

  // التعليم
  const educationContainer = document.getElementById("educationList");
  if (educationContainer) {
    educationContainer.innerHTML = "";
    data.education.forEach((edu, index) => {
      addEducationField(edu);
    });
  }

  // الخبرة
  const experienceContainer = document.getElementById("experienceList");
  if (experienceContainer) {
    experienceContainer.innerHTML = "";
    data.experience.forEach((exp, index) => {
      addExperienceField(exp);
    });
  }

  // المهارات
  const skillsContainer = document.getElementById("skillsList");
  if (skillsContainer) {
    skillsContainer.innerHTML = "";
    data.skills.forEach((skill, index) => {
      addSkillField(skill);
    });
  }
}

function addEducationField(data = {}) {
  const container = document.getElementById("educationList");
  const div = document.createElement("div");
  div.className = "education-item";
  div.innerHTML = `
    <input type="text" name="education_degree[]" placeholder="الدرجة العلمية" value="${
      data.degree || ""
    }" required>
    <input type="text" name="education_institution[]" placeholder="الجامعة/المعهد" value="${
      data.institution || ""
    }" required>
    <input type="text" name="education_field[]" placeholder="التخصص" value="${
      data.field_of_study || ""
    }">
    <input type="text" name="education_year[]" placeholder="السنة" value="${
      data.year || ""
    }">
    <input type="text" name="education_grade[]" placeholder="التقدير" value="${
      data.grade || ""
    }">
    <textarea name="education_description[]" placeholder="وصف (اختياري)" rows="2">${
      data.description || ""
    }</textarea>
    <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger btn-sm">حذف</button>
  `;
  container.appendChild(div);
}

function addExperienceField(data = {}) {
  const container = document.getElementById("experienceList");
  const div = document.createElement("div");
  div.className = "experience-item";
  div.innerHTML = `
    <input type="text" name="experience_title[]" placeholder="المسمى الوظيفي" value="${
      data.title || ""
    }" required>
    <input type="text" name="experience_company[]" placeholder="الشركة" value="${
      data.company || ""
    }" required>
    <input type="text" name="experience_location[]" placeholder="الموقع" value="${
      data.location || ""
    }">
    <input type="text" name="experience_duration[]" placeholder="المدة" value="${
      data.duration || ""
    }">
    <input type="date" name="experience_start_date[]" placeholder="تاريخ البدء" value="${
      data.start_date || ""
    }">
    <input type="date" name="experience_end_date[]" placeholder="تاريخ الانتهاء" value="${
      data.end_date || ""
    }">
    <label><input type="checkbox" name="experience_is_current[]" ${
      data.is_current ? "checked" : ""
    }> أعمل حالياً</label>
    <textarea name="experience_description[]" placeholder="الوصف" rows="3">${
      data.description || ""
    }</textarea>
    <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger btn-sm">حذف</button>
  `;
  container.appendChild(div);
}

function addSkillField(data = {}) {
  const container = document.getElementById("skillsList");
  const div = document.createElement("div");
  div.className = "skill-item";
  div.innerHTML = `
    <input type="text" name="skill_name[]" placeholder="اسم المهارة" value="${
      data.skill || ""
    }" required>
    <select name="skill_level[]">
      <option value="beginner" ${
        data.level === "beginner" ? "selected" : ""
      }>مبتدئ</option>
      <option value="intermediate" ${
        data.level === "intermediate" ? "selected" : ""
      }>متوسط</option>
      <option value="advanced" ${
        data.level === "advanced" ? "selected" : ""
      }>متقدم</option>
      <option value="expert" ${
        data.level === "expert" ? "selected" : ""
      }>خبير</option>
    </select>
    <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger btn-sm">حذف</button>
  `;
  container.appendChild(div);
}

// ==================== رفع السيرة الذاتية PDF ====================
async function handleResumePDFUpload() {
  const fileInput = document.getElementById("resumePdfInput");
  const file = fileInput.files[0];

  if (!file) {
    showNotification("error", "خطأ", "يرجى اختيار ملف PDF");
    return;
  }

  // التحقق من نوع الملف
  if (file.type !== "application/pdf") {
    showNotification("error", "خطأ", "الملف يجب أن يكون بصيغة PDF");
    fileInput.value = "";
    return;
  }

  // التحقق من حجم الملف (5 ميجابايت)
  if (file.size > 5 * 1024 * 1024) {
    showNotification("error", "خطأ", "حجم الملف يجب أن لا يتجاوز 5 ميجابايت");
    fileInput.value = "";
    return;
  }

  // عرض حالة الرفع
  const statusSpan = document.getElementById("resumePdfStatus");
  statusSpan.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الرفع...';
  statusSpan.style.color = "#667eea";

  const formData = new FormData();
  formData.append("resume_pdf", file);
  formData.append("user_id", APP_STATE.currentUser.id);

  try {
    const response = await fetch(`${API_URL}?action=uploadResumePDF`, {
      method: "POST",
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      statusSpan.innerHTML =
        '<i class="fas fa-check-circle"></i> تم الرفع بنجاح';
      statusSpan.style.color = "#4caf50";
      showNotification("success", "تم الرفع", "تم رفع السيرة الذاتية بنجاح");

      // تحديث عرض الملف الحالي
      loadProfile();
    } else {
      statusSpan.innerHTML =
        '<i class="fas fa-exclamation-circle"></i> فشل الرفع';
      statusSpan.style.color = "#e74c3c";
      showNotification("error", "خطأ", data.error);
    }
  } catch (error) {
    console.error("Error uploading resume:", error);
    statusSpan.innerHTML =
      '<i class="fas fa-exclamation-circle"></i> فشل الرفع';
    statusSpan.style.color = "#e74c3c";
    showNotification("error", "خطأ", "فشل رفع الملف");
  }
}

// ==================== حفظ الملف الشخصي ====================
async function handleSaveProfile(event) {
  event.preventDefault();

  const formData = new FormData(event.target);

  // بناء كائن البيانات
  const profileData = {
    user_id: APP_STATE.currentUser.id,
    profile: {
      phone: formData.get("phone"),
      location: formData.get("location"),
      bio: formData.get("bio"),
      linkedin_url: formData.get("linkedin_url"),
      github_url: formData.get("github_url"),
      website_url: formData.get("website_url"),
    },
    education: [],
    experience: [],
    skills: [],
  };

  // التعليم
  const degrees = formData.getAll("education_degree[]");
  const institutions = formData.getAll("education_institution[]");
  const fields = formData.getAll("education_field[]");
  const years = formData.getAll("education_year[]");
  const grades = formData.getAll("education_grade[]");
  const eduDescriptions = formData.getAll("education_description[]");

  for (let i = 0; i < degrees.length; i++) {
    if (degrees[i] && institutions[i]) {
      profileData.education.push({
        degree: degrees[i],
        institution: institutions[i],
        field_of_study: fields[i],
        year: years[i],
        grade: grades[i],
        description: eduDescriptions[i],
      });
    }
  }

  // الخبرة
  const titles = formData.getAll("experience_title[]");
  const companies = formData.getAll("experience_company[]");
  const locations = formData.getAll("experience_location[]");
  const durations = formData.getAll("experience_duration[]");
  const startDates = formData.getAll("experience_start_date[]");
  const endDates = formData.getAll("experience_end_date[]");
  const isCurrent = formData.getAll("experience_is_current[]");
  const expDescriptions = formData.getAll("experience_description[]");

  for (let i = 0; i < titles.length; i++) {
    if (titles[i] && companies[i]) {
      profileData.experience.push({
        title: titles[i],
        company: companies[i],
        location: locations[i],
        duration: durations[i],
        start_date: startDates[i],
        end_date: endDates[i],
        is_current: isCurrent.includes("on") ? 1 : 0,
        description: expDescriptions[i],
      });
    }
  }

  // المهارات
  const skillNames = formData.getAll("skill_name[]");
  const skillLevels = formData.getAll("skill_level[]");

  for (let i = 0; i < skillNames.length; i++) {
    if (skillNames[i]) {
      profileData.skills.push({
        skill: skillNames[i],
        level: skillLevels[i],
      });
    }
  }

  try {
    const response = await fetch(`${API_URL}?action=saveProfile`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(profileData),
    });

    const data = await response.json();

    if (data.success) {
      showNotification("success", "تم الحفظ بنجاح", "تم حفظ ملفك الشخصي بنجاح");

      // تحديث البيانات المحلية
      loadProfile();
    } else {
      showNotification("error", "خطأ", data.error);
    }
  } catch (error) {
    console.error("Error saving profile:", error);
    showNotification("error", "خطأ", "فشل حفظ الملف الشخصي");
  }
}

// ==================== وظائف مساعدة ====================
function showPage(pageName) {
  document.querySelectorAll(".page").forEach((page) => {
    page.classList.remove("active");
  });

  const page = document.getElementById(pageName + "Page");
  if (page) {
    page.classList.add("active");
    APP_STATE.currentPage = pageName;

    // تحميل البيانات عند فتح الصفحة
    if (pageName === "jobs") {
      loadJobs();
    } else if (pageName === "myJobs") {
      loadMyJobs();
    } else if (pageName === "applications") {
      loadMyApplications();
    } else if (pageName === "savedJobs") {
      loadSavedJobs();
    } else if (pageName === "profile") {
      loadProfile();
    }
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove("active");
  }
}

function closeAllModals() {
  document.querySelectorAll(".modal").forEach((modal) => {
    modal.classList.remove("active");
  });

  const panel = document.getElementById("notificationsPanel");
  if (panel) {
    panel.classList.remove("active");
  }
}

function showNotification(type, title, message) {
  const notification = document.createElement("div");
  notification.className = `notification notification-${type}`;
  notification.innerHTML = `
    <div class="notification-content">
      <strong>${title}</strong>
      <p>${message}</p>
    </div>
  `;

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.classList.add("show");
  }, 100);

  setTimeout(() => {
    notification.classList.remove("show");
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 3000);
}

function formatDate(dateString) {
  if (!dateString) return "غير محدد";

  const date = new Date(dateString);
  const now = new Date();
  const diff = now - date;
  const days = Math.floor(diff / (1000 * 60 * 60 * 24));

  if (days === 0) return "اليوم";
  if (days === 1) return "أمس";
  if (days < 7) return `منذ ${days} أيام`;
  if (days < 30) return `منذ ${Math.floor(days / 7)} أسابيع`;
  if (days < 365) return `منذ ${Math.floor(days / 30)} شهور`;

  return date.toLocaleDateString("ar-SA");
}

// ==================== البحث ====================
function handleJobSearch(event) {
  event.preventDefault();

  const formData = new FormData(event.target);
  const search = formData.get("search") || "";
  const type = formData.get("type") || "";
  const location = formData.get("location") || "";

  loadJobs(search, type, location);
}

// ==================== تصدير الوظائف ====================
window.showPage = showPage;
window.handleLogin = handleLogin;
window.handleRegister = handleRegister;
window.handlePostJob = handlePostJob;
window.handleSaveProfile = handleSaveProfile;
window.handleResumePDFUpload = handleResumePDFUpload;
window.handleJobSearch = handleJobSearch;
window.logout = logout;
window.viewJob = viewJob;
window.applyForJob = applyForJob;
window.saveJob = saveJob;
window.unsaveJob = unsaveJob;
window.viewJobApplications = viewJobApplications;
window.updateApplicationStatus = updateApplicationStatus;
window.closeModal = closeModal;
window.addEducationField = addEducationField;
window.addExperienceField = addExperienceField;
window.addSkillField = addSkillField;
window.showNotificationsPanel = showNotificationsPanel;
window.markAllNotificationsRead = markAllNotificationsRead;

// ==================== إعداد معالج رفع PDF ====================
/*document.addEventListener("DOMContentLoaded", function () {
  // إضافة event listener لحقل رفع السيرة الذاتية
  setTimeout(() => {
    const resumePdfInput = document.getElementById("resumePdfInput");
    if (resumePdfInput) {
      resumePdfInput.addEventListener("change", handleResumePDFUpload);
    }
  }, 1000);
});*/
document
  .getElementById("resumeUploadForm")
  .addEventListener("submit", function (e) {
    const fileInput = document.getElementById("resumePdfInput");
    if (!fileInput.files.length) {
      alert("يرجى اختيار ملف للرفع.");
      e.preventDefault(); // منع الإرسال إذا لم يتم اختيار ملف
    }
    // سيتم إرسال النموذج مباشرة إلى ملف api-fixed.php
  });
