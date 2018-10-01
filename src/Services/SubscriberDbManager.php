<?php

namespace App\Services;

use App\Entity\Emails;
use Doctrine\ORM\EntityManagerInterface;


class SubscriberDbManager {

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * save a new validated email-subscriber in DB
     *
     * @return array
     */
    public function saveNewSubscriber($emailAddress)
    {
        //todo eliminate token from db
        // set registerDateTime and set a token based on datetime + email address
        $currentDateTime = new \DateTime();
        $token = md5($currentDateTime->format('Y-m-d H:i:s').$emailAddress);

        $email = new Emails();
        $email->setMail($emailAddress);
        $email->setStatus(false);
        $email->setDate($currentDateTime);
        $email->setToken($token);

        $this->em->persist($email);
        $this->em->flush();

        return $email;
    }



}

