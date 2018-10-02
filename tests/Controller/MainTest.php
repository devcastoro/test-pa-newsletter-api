<?php

namespace Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainTest extends WebTestCase
{
    const REALEMAIL = 'xyz@gmail.com'; // change for every new test
    const INVALIDMAIL = '∆∆∆∆000∆0∆0∆@gmail.com';

    /**
     * Simulate a new subscriber request and confirmation
     *
     */
    public function testSimulateNewSubscriberRequest()
    {
        $client = static::createClient(array(
            'environment' => 'test',
        ),array('HTTPS' => true));

        // check new subscriber
        $client->request('POST', '/newSubscriber', array(
            'email'         => self::REALEMAIL
        ));
        $content = json_decode($client->getResponse()->getContent());
        $this->assertEquals("NotConfirmedRegistration", $content->status);

        // check the switch status
        $client->request('GET', '/confirmEmail', array(
            'email'          => self::REALEMAIL,
            'token'          => $content->token
        ));
        $content = json_decode($client->getResponse()->getContent());
        $this->assertEquals(true, $content->status);
    }

    /**
     * Simulate a new subscriber with invalid email
     *
     */
    public function testSimulateNewSubscriberInvalidRequest()
    {
        $client = static::createClient(array(
            'environment' => 'test',
        ),array('HTTPS' => true));

        // check new subscriber
        $client->request('POST', '/newSubscriber', array(
            'email'         => self::INVALIDMAIL
        ));
        $content = json_decode($client->getResponse()->getContent());
        $this->assertEquals("Email format is not valid", $content->error);
    }


    /**
     * Simulate a new subscriber with an already registered email
     *
     */
    public function testSimulateAlreadySubscribedRequest()
    {
        $client = static::createClient(array(
            'environment' => 'test',
        ),array('HTTPS' => true));

        // check already subscriber
        $client->request('POST', '/newSubscriber', array(
            'email'         => self::REALEMAIL
        ));
        $content = json_decode($client->getResponse()->getContent());
        $this->assertEquals("Email already registered in DB", $content->error);
        
    }
}



