<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\Common\Cache\FilesystemCache;
use Ejsmont\CircuitBreakerBundle\Factory;

class DemoController extends Controller {

    /**
     * Action uses cache and page links to show you how Circuit Breaker changes its decisions based on what you report.
     * 
     * 1. Go to one of the test urls. Each obrains circuit breaker in different way.
     *      /demo/circuit-breaker/apc/sameFake/status
     *      /demo/circuit-breaker/doctrineCache/sameFake/status
     *      /demo/circuit-breaker/manual/sameFake/status
     * 2. Keep clicking "report failure" till you see that service is marked as down
     * 3. Now service is marked as down so clicking refresh status will tell you its down
     * 4. Once RetryTimeout elapses you will see status available once and then it will be failing again
     *      this single available status is to let client code retry connecting and see if it is really failing
     * 5. When you start clicking report success service will come back online but will be more likely to 
     *      be marked as failing untill you gaining more confidence (each success reduces counter from threshold -> 0)
     * 
     * Actual application would not check user input, it would detect service failures when they happen
     * This is just a simple way to show how component keeps state between requests and decides should the service
     * be accessed or not.
     * 
     * @Route("/circuit-breaker/{type}/{name}/{reportStatus}", name="_demo_cb_fail")
     * @Template()
     */
    public function circuitBreakerAction($type, $name, $reportStatus = 0) {
        // obtain instance in any way you want, here are three different examples
        if ($type == "apc") {
            // use default APC based instance
            $circuitBreaker = $this->get('apcCircuitBreaker');
        } elseif ($type == "doctrineCache") {
            // use doctrine/cache backend wired by circuitBreakerCacheBackend cache service name
            // you can override circuitBreakerCacheBackend service in your application service.yaml
            $circuitBreaker = $this->get('circuitBreaker');
        } else {
            $type = "manual";
            // use manually assembled instance, allow 7 failures, retry after 10 sec
            $fileCache = new FilesystemCache('/tmp/cache/', '.cache');
            $circuitBreaker = Factory::getDoctrineCacheInstance($fileCache, 7, 10);
        }

        // this is how you would tell circuit breaker weather service is alive or dead
        if ($reportStatus == 'succeed') {
            $circuitBreaker->reportSuccess($name);
        } elseif ($reportStatus == 'fail') {
            $circuitBreaker->reportFailure($name);
        }

        return array(
            'status' => $circuitBreaker->isAvailable($name) ? "available" : "down",
            'service' => $name,
            'type' => $type,
        );
    }

}
