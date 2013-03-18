<?php

namespace Acme\DemoBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DemoControllerTest extends WebTestCase {

    public function testIndex() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/demo/circuit-breaker/unitTestFake/status');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("available")')->count());
    }

}
