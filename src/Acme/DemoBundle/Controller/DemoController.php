<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DemoController extends Controller {

    /**
     * Action uses cache and page links to show you how Circuit Breaker changes its decisions based on what you report.
     * 
     * 1. Render page /demo/circuit-breaker/sameFake/fail
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
     * @Route("/apc-circuit-breaker/{name}/{reportStatus}", name="_demo_cb_fail")
     * @Template()
     */
    public function apcCircuitBreakerAction($name, $reportStatus = 0) {
        // use default APC based instance
        $circuitBreaker = $this->get('apcCircuitBreaker');

        if ($reportStatus == 'succeed') {
            $circuitBreaker->reportSuccess($name);
        } elseif ($reportStatus == 'fail') {
            $circuitBreaker->reportFailure($name);
        }

        return array(
            'status' => $circuitBreaker->isAvailable($name) ? "available" : "down",
            'service' => $name,
        );
    }

}

//    /**
//     * @Route("/circuit-breaker-fail/{name}", name="_demo_cb_fail")
//     * @Template()
//     */
//    public function circuitBreakerAction($name) {
// use Ejsmont\CircuitBreakerBundle\Factory;
//
//        // get instance manually with doctrine APC backend (differnet cache keys)
//        $apcCache = new \Doctrine\Common\Cache\ApcCache();
//        $manuallyApcCb = Factory::getDoctrineCacheInstance($apcCache);
//        if ($manuallyApcCb->isAvailable("UserProfileService2")) {
//            $name .= ' DoctrineAPC=ok';
//            $manuallyApcCb->reportFailure('UserProfileService2');
//        }
//
//        // get instance manually with doctrine File backend
//        $fileCache = new \Doctrine\Common\Cache\FilesystemCache('/tmp/cache/', '.cache');
//        $manuallyFileCb = Factory::getDoctrineCacheInstance($fileCache);
//        if ($manuallyFileCb->isAvailable("UserProfileService3")) {
//            $name .= ' DoctrineAPC=ok';
//            $manuallyFileCb->reportFailure('UserProfileService3');
//        }
//
//        return array('name' => $name);
//    }
//    
