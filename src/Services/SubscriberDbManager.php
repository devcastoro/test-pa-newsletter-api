<?php

namespace App\Services;

use App\Entity\Emails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

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

        $subscriber = new Emails();
        $subscriber->setMail($emailAddress);
        $subscriber->setStatus(false);
        $subscriber->setDate($currentDateTime);
        $subscriber->setToken($token);

        $this->em->persist($subscriber);
        $this->em->flush();

        return $subscriber;
    }

    /**
     * switch subscriber status from NOT CONFIRMED to CONFIRMED
     *
     * @return array
     */
    public function confirmSubscriber($email,$token)
    {
        // get the subscriber
        $subscriber = $this->em->getRepository(Emails::class)->findOneBy(["mail" => $email]);

        // check the token
        // todo: consider to generate the REAL token here not to get from DB,
        if ($subscriber->getToken() == $token) {

            // check if already confirmed
            if ($subscriber->getStatus() == true) {
                throw new Exception('This email is already confirmed');
            }

            // switch status
            $subscriber->setStatus(true);
            $this->em->persist($subscriber);
            $this->em->flush();

            // todo: send a final confirmation email
            return $subscriber;
        }
        else{
            throw new Exception('Your confirmation token is not correct');
        }
    }

}

