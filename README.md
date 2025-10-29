# 💾 Telegram File Saver/Forwarder Bot (Render & Docker)

यह बॉट यूजर द्वारा भेजी गई किसी भी फ़ाइल को आपके चैनल में सेव (फॉरवर्ड) करता है।

## 🔑 कॉन्फ़िगरेशन विवरण

* **BOT_TOKEN:** `8008467900:AAHd-_VQUNGkPAYVSt8NLPfQn6fA1Iq04Sw`
* **TARGET_CHANNEL_ID:** `-1003161993313`

*(Note: API_ID और API_HASH इस Webhook-आधारित कोड में सीधे उपयोग नहीं किए जाते हैं, लेकिन वे Telegram बॉट डेवलपमेंट के लिए आवश्यक होते हैं।)*

## 🛠️ Render डिप्लॉयमेंट

GitHub पर `index.php`, `Dockerfile`, और `.env.example` अपलोड करने के बाद, Render Web Service बनाते समय इन चरणों का पालन करें:

### 1. Render Service Settings

* **Runtime:** `Docker`
* **Dockerfile Path:** `./Dockerfile`

### 2. Environment Variables सेट करें

Render Web Service Settings में, दो **Environment Variables** जोड़ें:

| Key | Value |
| :--- | :--- |
| **`BOT_TOKEN`** | `8008467900:AAHd-_VQUNGkPAYVSt8NLPfQn6fA1Iq04Sw` |
| **`TARGET_CHANNEL_ID`** | `-1003161993313` |

### 3. Webhook सेट करें

डिप्लॉयमेंट पूरा होने के बाद, अपने ब्राउज़र में इस URL पर जाकर Webhook सेट करें (जहां `<YOUR_RENDER_URL>` Render द्वारा दिया गया URL है):

