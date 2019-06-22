    
FROM prooph/composer:7.3
WORKDIR /app
COPY /. /app

LABEL "com.github.actions.name"="Phar Creation"
LABEL "com.github.actions.description"="Create phar"
LABEL "com.github.actions.icon"="mic"
LABEL "com.github.actions.color"="purple"

LABEL "repository"="http://github.com/unglue-workflow/clien"
LABEL "homepage"="http://github.com/unglue-workflow/client"
LABEL "maintainer"="Basil Suter <basil@nadar.io>"

RUN composer install && php -d phar.readonly=0 vendor/bin/phar-builder package composer.json --no-interaction