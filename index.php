<?php
// index.php - Render-Optimized Telegram File Forwarder/Saver Bot (Docker Ready)

// Render Environment Variables से कॉन्फ़िगरेशन लोड करें
// ये मान Render Web Service Settings में सेट किए जाएंगे।
$BOT_TOKEN = $_ENV['BOT_TOKEN'] ?? die("Error: BOT_TOKEN is not set in Render environment variables.");
$TARGET_CHANNEL_ID = $_ENV['TARGET_CHANNEL_ID'] ?? die("Error: TARGET_CHANNEL_ID is not set in Render environment variables.");

// Telegram Bot API URL
$api_url = "https://api.telegram.org/bot{$BOT_TOKEN}/";

/**
 * Telegram को एक साधारण मैसेज भेजता है (cURL का उपयोग करके)
 */
function sendMessage($chat_id, $text, $reply_to = null) {
    global $api_url;
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    
    if ($reply_to) {
        $data['reply_to_message_id'] = $reply_to;
    }
    
    $url = $api_url . "sendMessage";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

/**
 * फ़ाइल को TARGET_CHANNEL_ID में फॉरवर्ड करता है
 */
function forwardFileToChannel($from_chat_id, $message_id) {
    global $api_url, $TARGET_CHANNEL_ID;
    
    $data = [
        'chat_id' => $TARGET_CHANNEL_ID,
        'from_chat_id' => $from_chat_id,
        'message_id' => $message_id
    ];
    
    $url = $api_url . "forwardMessage?" . http_build_query($data);
    
    $response = @file_get_contents($url);
    $result = json_decode($response, true);
    return $result && $result['ok'];
}

/**
 * Telegram से प्राप्त अपडेट को संभालता है
 */
function handleUpdate($update) {
    global $TARGET_CHANNEL_ID;

    if (!isset($update['message'])) {
        return;
    }
    
    $message = $update['message'];
    $chat_id = $message['chat']['id'];
    $message_id = $message['message_id'];
    $user_id = $message['from']['id'];

    // /start कमांड
    if (isset($message['text']) && strpos($message['text'], '/start') === 0) {
        $start_text = "👋 नमस्ते! मैं आपकी फ़ाइलें आपके चैनल में सेव कर दूँगा।\n\nआपका User ID: <code>{$user_id}</code>\nसेविंग चैनल ID: <code>" . $TARGET_CHANNEL_ID . "</code>";
        sendMessage($chat_id, $start_text, $message_id);
        return;
    }

    // फ़ाइल (Document, Photo, Video, Audio) हैंडल करें
    if (isset($message['document']) || isset($message['photo']) || isset($message['video']) || isset($message['audio'])) {
        
        if (forwardFileToChannel($chat_id, $message_id)) {
            $success_text = "✅ फ़ाइल सफलतापूर्वक चैनल में सेव/फॉरवर्ड कर दी गई है!";
            sendMessage($chat_id, $success_text, $message_id);
        } else {
            $error_text = "❌ फ़ाइल फॉरवर्ड करने में त्रुटि आई।\n\n<b>संभवित कारण:</b>\n1. बॉट चैनल का <b>एडमिन</b> नहीं है।\n2. बॉट के पास चैनल में फ़ाइलें भेजने की अनुमति नहीं है।";
            sendMessage($chat_id, $error_text, $message_id);
        }
        return;
    }

    // अन्य टेक्स्ट मैसेज
    if (isset($message['text'])) {
         sendMessage($chat_id, "कृपया मुझे केवल कोई फ़ाइल (Document, Photo, Video, etc.) भेजें।", $message_id);
    }
}

// === मुख्य Webhook लॉजिक ===
$update_json = file_get_contents("php://input");
$update = json_decode($update_json, true);

if ($update) {
    handleUpdate($update);
} else {
    // Render Health Check
    http_response_code(200);
    echo "Bot is running. Ready for Webhook updates.";
}
?>
