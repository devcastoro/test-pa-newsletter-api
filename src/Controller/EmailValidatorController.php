<?php
namespace App\Controller;

use App\Entity\Emails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmailValidatorController extends AbstractController
{
    private $em;
    private $validator;

    public function __construct(ValidatorInterface $validator, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * validate email checking format and presence in DB
     *
     * @return boolean
     */
    public function emailValidation($email)
    {
        $this->emailFormatValidation($email);
        $this->emailRecordValidation($email);
    }

    /**
     * validate email format using Symfony Validator
     *
     * @return boolean
     */
    public function emailFormatValidation($email)
    {
        $emailConstraint = new EmailConstraint();
        $errors = $this->validator->validate($email,$emailConstraint);

        if (count($errors) > 0) {
            throw new Exception('Email format is not valid');
        }

        return true;
    }

    /**
     * check if the email is already registered in DB
     *
     * @return boolean
     */
    public function emailRecordValidation($email)
    {
        $emailRecord = $this->em->getRepository(Emails::class)->findOneBy(["mail" => $email]);

        if ($emailRecord){
            throw new Exception('Email already registered in DB');
        }

        return true;
    }
}