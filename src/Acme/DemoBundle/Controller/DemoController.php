<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DemoController extends Controller {

    /**
     * @Route("/hello/{name}", name="_demo_hello")
     * @Template()
     */
    public function helloAction($name) {
        // get instance from services.xml
        $circuitBreaker = $this->get('circuitBreaker');

        // get instance directly from the factory
        //$factory = new \Ejsmont\CircuitBreaker\Factory();
        //$circuitBreaker = $factory->getSingleApcInstance(30, 300);

        if ($circuitBreaker->isAvailable("UserProfileService")) {
            $name .= ' circuit breaker says its ok';
        }

        return array('name' => $name);
    }

}
