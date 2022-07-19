<?php

namespace App\Tests\CoreLogic;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProgressControllerTest extends WebTestCase
{
    public function testStatusMethodReturns200()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/statuses/1/2/2022-07-15T15:52:01+00:00/2022-07-16T15:52:01+00:00');
        $this->assertResponseIsSuccessful();
    }

    public function testWrongDurationType()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/statuses/notnumber/2/3/4');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString("Duration must be an integer!", $client->getResponse()->getContent());
    }

    public function testNegativeDurationValue()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/statuses/-100/2/3/4');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString("Duration must be positive!", $client->getResponse()->getContent());
    }

    public function testProgressOutOfScope()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/statuses/1/200/3/4');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString("Progress in % must be an integer between 0 and 100!", $client->getResponse()->getContent());
    }

    public function testInvalidStartDate()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/statuses/1/2/2022-07-11 15:52:01+00:00/2022-07-15T15:52:01+00:00');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString("Date of creation in wrong format, must use RFC3339!", $client->getResponse()->getContent());
    }

    public function testInvalidSDueDate()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/statuses/1/2/2022-07-17T15:52:01+00:00/17-07-2022');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString("Due date in wrong format, must use RFC3339!", $client->getResponse()->getContent());
    }

}