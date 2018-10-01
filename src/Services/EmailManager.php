<?php

namespace App\Services;



class EmailManager {


    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig_Environment)
    {

        $this->mailer = $mailer;
        $this->twig = $twig_Environment;
    }

    /**
     * build a confirmation link and send a mail to a new subscriber
     *
     */
    public function sendConfirmationEmail($emailAddress,$token)
    {

        $confirmationLink = getenv('WEBSITE_URL')."confirmEmail?email=".$emailAddress."&token=".$token;

        $message = (new \Swift_Message('Confirmation email' ))
            ->setFrom(getenv('MAILER_FROM'))
            ->setTo($emailAddress)
            ->setBody(
                $this->twig->render('/emailTemplate.html.twig',
                    array('confirmationLink' => $confirmationLink,
                    )),
                'text/html'
            );

        $this->mailer->send($message);
    }

    /**
     * send the last email to new user that confirm the status
     *
     */
    public function confirmedSubscriberEmail($emailAddress)
    {
        $message = (new \Swift_Message('Email Confirmed' ))
            ->setFrom(getenv('MAILER_FROM'))
            ->setTo($emailAddress)
            ->setBody($this->twig->render('/confirmedEmailTemplate.html.twig'),'text/html');

        $this->mailer->send($message);

    }


}

