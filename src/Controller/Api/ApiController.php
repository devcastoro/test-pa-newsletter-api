<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller {

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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

        // todo: validation phase (check if: is a mail, is a new subscriber, ...)

        // todo: if the validation phase is OK.
            // todo Save in DB
            // todo Send a confirmation email with email+token


        return $this->json([
            "email"   => $email
        ],200);

    }




}

