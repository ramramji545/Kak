# Dockerfile

# बेस इमेज: Official php:8.2-apache (यह Debian पर आधारित है और स्थिर है)
FROM php:8.2-apache

# 1. पैकेज लिस्ट को अपडेट करें और आवश्यक PHP एक्सटेंशन इंस्टॉल करें: cURL और JSON
# cURL Telegram API रिक्वेस्ट के लिए ज़रूरी है
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    && docker-php-ext-install curl json \
    # अनावश्यक फ़ाइलों को हटाकर इमेज साइज़ को कम करें
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. अपनी PHP फ़ाइल (index.php) को container के Apache root में कॉपी करें
COPY index.php /var/www/html/index.php

# 3. पोर्ट 80 को उजागर करें (Render के लिए डिफ़ॉल्ट)
EXPOSE 80

# 4. container शुरू होने पर Apache को foreground में चलाएँ (php:8.2-apache का डिफ़ॉल्ट कमांड)
# यह Webhook को लगातार सुनते रहने देता है
# CMD ["apache2-foreground"]
