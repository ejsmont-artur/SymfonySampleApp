symfony-php-proxy-builder-example
=================================

Example Symfony2 application using php-proxy-builder

How this project was created:
<pre>
    curl -s http://getcomposer.org/installer | php
    chmod a+x composer.phar
    ./composer.phar create-project symfony/framework-standard-edition symfony-php-proxy-builder-example 2.1.6
</pre>

Then i have updated composer.json to include namespace and removed some Acme files to reduce distractions.

Added two sections to composer.json
    "psr-0": { "Acme": "src/" }
    "ejsmont-artur/php-circuit-breaker": "0.0.1"

Updated dependencies:
    curl -s http://getcomposer.org/installer | php
    php app/check.php
    php app/console cache:clear
    php composer.phar update

phpunit -c app src/Acme/DemoBundle

