<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Form\CompteType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $encoder)
    {

        $em = $this->getDoctrine()->getManager();
        $compte = new Compte();

        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $compte->setPassword($encoder->encodePassword($compte, $compte->getPassword()));
            $compte->setRoles('ROLE_USER');

            $em->persist($compte);
            $em->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('register/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
