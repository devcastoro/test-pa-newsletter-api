<?php

namespace App\Controller\Api;

use App\Controller\EmailValidatorController;
use App\Services\SubscriberDbManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller {

    public function __construct(EmailValidatorController $emailValidatorController, SubscriberDbManager $subscriberDbManager)
    {
        $this->emailValidatorController = $emailValidatorController;
        $this->subscriberManager = $subscriberDbManager;
    }

    /**
     * get new email/subscriber and, after a validation, save it into DB and send a confirmation email
     *
     * @FOSRest\Post("/subscribe-new-email")
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

            // todo Send a confirmation email with email+token


            return $this->json([
                "mail"   => $userRecord->getMail(),
                "status" => "NotConfirmedRegistration",
            ],200);

        } catch (Exception $e) {

            return $this->json(["Error" => $e->getMessage()],400);
        }



    }

}

