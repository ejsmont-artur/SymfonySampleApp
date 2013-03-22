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
     * This example shows three different ways to obrain the circuit breaker instance each using different persistance
     * backend (apc, memcached, fileCache).
     * 
     * Actual application would not check user input and the code would have different flow. This is just to show
     * how state changes based on history results. It also shows that component keeps state between requests and 
     * decides itself if service should be accessed or not. Your code does not have to worry about that any more.
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
     * @Route("/circuit-breaker/{type}/{name}/{reportStatus}", name="_demo_cb_fail")
     * @Template()
     */
    public function circuitBreakerAction($type, $name, $reportStatus = 0) {
        // obtain instance in any way you want, here are three different examples
        if ($type == "apc") {
            // use default APC based instance
            $circuitBreaker = $this->get('apcCircuitBreaker');
        } elseif ($type == "doctrineCache") {
            // use doctrine/cache backend injected using "circuitBreakerCacheBackend" service name
            // "circuitBreaker" service uses "circuitBreakerCacheBackend" which you can override in service.yaml
            $circuitBreaker = $this->get('circuitBreaker');
        } else {
            // use manually assembled instance, allow 7 failures, retry after 10 sec
            $type = "manual";
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
