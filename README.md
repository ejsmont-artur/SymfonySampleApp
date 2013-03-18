# Purpose of this project

SymfonySampleApp is a sample Symfony2 application using a few of my components.

I will use it in some examples, tutorials etc.

# Example 1 Use of [php-circuit-breaker-bundle](https://github.com/ejsmont-artur/php-circuit-breaker-bundle)

Step 1. Add composer.json dependency on php-circuit-breaker-bundle

    "require": {
        "ejsmont-artur/php-circuit-breaker-bundle": "0.1.*"
    },

Step 2. Override defaults of threshold and timeout in your application services.yaml

    # Override defaults of the circuit breaker bundle
    parameters:
        # Allowed amount of failures before marking service as unavailable
        ejsmont_circuit_breaker.threshold: 3
        # how many seconds should we wait before allowing a single request
        ejsmont_circuit_breaker.retry_timeout: 5

Step 3. Start using default APC instance in your classes

    # you can get predefined circuit breaker instances from DIC anywhere
    $circuitBreaker = $this->get('apcCircuitBreaker');

You can see more details in the [DemoController::apcCircuitBreakerAction](https://github.com/ejsmont-artur/SymfonySampleApp/blob/master/src/Acme/DemoBundle/Controller/DemoController.php) method.

# How this project was initially created

    curl -s http://getcomposer.org/installer | php
    chmod a+x composer.phar
    ./composer.phar create-project symfony/framework-standard-edition SymfonySampleApp 2.1.6

Then i have updated composer.json to include namespace and removed some Acme files to reduce distractions.

Added autoloader section to composer.json

    "psr-0": { "Acme": "src/" }

Updated dependencies:

    curl -s http://getcomposer.org/installer | php
    php app/check.php
    php app/console cache:clear
    php composer.phar update
    phpunit -c app src/Acme/DemoBundle

