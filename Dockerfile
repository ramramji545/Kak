# Dockerfile

# बेस इमेज: Alpine Linux पर PHP 8.2 और Apache HTTP Server
FROM php:8.2-apache-alpine

# आवश्यक PHP एक्सटेंशन इंस्टॉल करें: cURL और JSON
RUN apk add --no-cache curl \
    && docker-php-ext-install curl json

# अपनी PHP फ़ाइल (index.php) को container के Apache root में कॉपी करें
COPY index.php /var/www/html/index.php

# पोर्ट 80 को उजागर करें
EXPOSE 80

# container शुरू होने पर Apache को चलाएँ
CMD ["apache2-foreground"]
