<?php

namespace App\Controller\Api;

use App\Entity\Contact;
use App\Service\SignUpNotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use ReCaptcha\ReCaptcha;

class ContactController extends AbstractController
{
    #[Route('/api/contact', name: 'contact-api'), Method('POST')]
    public function create(Request $request, SignUpNotificationService $notify, ValidatorInterface $validator): Response
    {
        $data = $request->request->all();
        $entityManager = $this->getDoctrine()->getManager();

        $recaptcha = new ReCaptcha('6LcBmDwaAAAAAOKvagqeDHp6xWe_J-1N6quDTlKe');
        $resp = $recaptcha->verify($data['g-recaptcha-response'], $request->getClientIp());
        if ($resp->isSuccess()) {
            $contact = new Contact;

            $contact->setFirstName($data['form']['first_name']);
            $contact->setLastName($data['form']['last_name']);
            $contact->setEmail($data['form']['email']);

            $errors = $validator->validate($contact);
            if (count($errors) > 0) {
                $errorsString = $errors[0]->getMessage();
                return $this->json(['success' => FALSE, 'message' => $errorsString]);
            }

            $entityManager->persist($contact);

            $entityManager->flush();
            
            $notify->sendNotification($contact);
            
            return $this->json(['success' => TRUE, 'message' => "Contact Saved."]);
        } else {
            return $this->json(['success' => FALSE, 'message' => 'Unverified captcha.']);
        }

        return $this->json(['success' => FALSE, 'message' => "Something went wrong."]);
    }
}
