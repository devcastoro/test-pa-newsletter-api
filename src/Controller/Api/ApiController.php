<?php

namespace App\Controller\Api;

use App\Controller\EmailValidatorController;
use App\Services\EmailManager;
use App\Services\SubscriberDbManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller {

    public function __construct(EmailValidatorController $emailValidatorController, SubscriberDbManager $subscriberDbManager, EmailManager $emailManager)
    {
        $this->emailValidatorController = $emailValidatorController;
        $this->subscriberManager = $subscriberDbManager;
        $this->emailManager = $emailManager;
    }

    /**
     * get new email/subscriber and, after a validation, save it into DB and send a confirmation email
     *
     * @FOSRest\Post("/newSubscriber")
     *
     * @return array
     */
    public function postSubscriber(Request $request)
    {
        // get and validate input parameters
        $email = $request->get('email');

        try {
            // validate email
            $this->emailValidatorController->emailValidation($email);

            // register the new email in DB
            $userRecord = $this->subscriberManager->saveNewSubscriber($email);

            // send email confirmation
            $this->emailManager->sendConfirmationEmail($userRecord->getMail(),$userRecord->getToken());

            return $this->json([
                "mail"   => $userRecord->getMail(),
                "status" => "NotConfirmedRegistration",
            ],200);

        } catch (Exception $e) {

            return $this->json(["Error" => $e->getMessage()],400);
        }
    }

    /**
     * confirm a subscriber Email
     *
     * @FOSRest\Get("/confirmEmail")
     * @param string $email
     * @param string $token
     *
     */
    public function confirmEmail(Request $request)
    {
        $email = $request->query->get('email');
        $token = $request->query->get('token');

        try {
            // validate email (check if is valid format and if exist
            $this->emailValidatorController->emailFormatValidation($email);

            // check check status and token
            $confirmationSubscriber = $this->subscriberManager->confirmSubscriber($email,$token);

            // send end process email
            $this->emailManager->confirmedSubscriberEmail($confirmationSubscriber->getMail());

            return $this->json([
                "mail"   => $confirmationSubscriber->getMail(),
                "status" => $confirmationSubscriber->getStatus(),
            ],200);

        } catch (Exception $e) {

            return $this->json(["Error" => $e->getMessage()],400);
        }
    }

}

