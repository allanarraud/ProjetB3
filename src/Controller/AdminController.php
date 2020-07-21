<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Form\CompteModificationType;
use App\Form\ConfirmationBanCompteType;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ConfirmationSuppressionCompteType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, '[Back-Office] Un utilisateur non-admin a tenté d\'accéder à l\'URL de l\'accueil du back-office.');

        return $this->render('admin/admin.html.twig');
    }

    /**
     * @Route("admin/comptes", name="liste_comptes")
     */
    public function listerComptes()
    {
        // Si un utilisateur non-admin tente d'accéder à une url du back-office, on retourne une erreur 403.
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, '[Back-Office] Un utilisateur non-admin a tenté d\'accéder à l\'URL de la liste des comptes utilisateurs.');

        $compteRepository = $this->getDoctrine()->getRepository(Compte::class);

        $lesComptes = $compteRepository->findAll();

        return $this->render('admin/listeComptes.html.twig', [
            'lesComptes' => $lesComptes,
        ]);
    }

    /**
     * @Route("admin/comptes/ajouter", name="compte_ajouter")
     */
    public function ajouter(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, '[Back-Office] Un utilisateur non-admin a tenté d\'accéder à l\'URL d\'ajout d\'un compte utilisateur.');

        $compte = new Compte();
        
        $form = $this->createForm(CompteType::class, $chaton);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compte=$form->getData();

            //Une connexion à la BDD par l'entity manager (em).
            $em = $this->getDoctrine()->getManager();
            $em->persist($compte);
            $em->flush();

            return $this->redirectToRoute("admin/comptes");
        }

        return $this->render('admin/formulaireAjoutCompte.html.twig', [
            "form"=>$form->createView(),
            "titre" => "Ajouter un compte"
        ]);
    }

    /**
     * @Route("admin/comptes/modifier/{id_compte}", name="compte_modifier")
     */
    public function modifier($id_compte, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, '[Back-Office] Un utilisateur non-admin a tenté d\'accéder à l\'URL de modification d\'un compte utilisateur.');

        //récupérer le repository (une connexion à la table en gros...)
        $compteRepository=$this->getDoctrine()->getRepository(Compte::class);
        //récupérer la catégorie à modifier
        $compte = $compteRepository->find($id_compte);

        $form = $this->createForm(CompteModificationType::class, $compte);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compte=$form->getData();

            //une connexion à la BDD par l'entity manager
            $em = $this->getDoctrine()->getManager();
            $em->persist($compte);   
            $em->flush();

            return $this->redirectToRoute("liste_comptes");
        }

        return $this->render('admin/formulaireModificationCompte.html.twig', [
            "form"=>$form->createView(),
            "titre" => "Modification du compte N°".$compte->getId().", ".$compte->getUsername()
        ]);
    }

    /**
     * @Route("admin/comptes/supprimer/{id_compte}", name="compte_supprimer")
     */
    public function supprimer($id_compte, Request $request){
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, '[Back-Office] Un utilisateur non-admin a tenté d\'accéder à l\'URL de suppression d\'un compte utilisateur.');

        $compteRepository = $this->getDoctrine()->getRepository(Compte::class);
        $compte = $compteRepository->Find($id_compte);
    
        $form = $this->createForm(ConfirmationSuppressionCompteType::class, $compte);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compte=$form->getData();

            //Une connexion à la BDD par l'entity manager (em).
            $em = $this->getDoctrine()->getManager();
            $em->remove($compte);  
            $em->flush();

            return $this->redirectToRoute("liste_comptes");
        }

        return $this->render('admin/confirmationSuppressionCompte.html.twig', [
            "form"=>$form->createView(),
            "titre" => "Supprimer l'utilisateur '".$compte->getUsername()."' ? [ID: ".$compte->getId()."]"
        ]);
    }

    /**
     * @Route("admin/comptes/bannir/{id_compte}", name="compte_bannir")
     */
    public function bannir($id_compte, Request $request){
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, '[Back-Office] Un utilisateur non-admin a tenté d\'accéder à l\'URL de banissement d\'un compte utilisateur.');
        $compteRepository = $this->getDoctrine()->getRepository(Compte::class);
        $compte = $compteRepository->Find($id_compte);
    
        $form = $this->createForm(ConfirmationBanCompteType::class, $compte);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compte=$form->getData();
            $compte->setBanni(true);

            //Une connexion à la BDD par l'entity manager (em).
            $em = $this->getDoctrine()->getManager();
            $em->persist($compte);
            $em->flush();

            return $this->redirectToRoute("liste_comptes");
        }

        return $this->render('admin/confirmationBanCompte.html.twig', [
            "form"=>$form->createView(),
            "titre" => "Bannir l'utilisateur '".$compte->getUsername()."' [ID: ".$compte->getId()."]",
            "action" => "Bannir"
        ]);
    }

    /**
     * @Route("admin/comptes/debannir/{id_compte}", name="compte_debannir")
     */
    public function debannir($id_compte, Request $request){
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, '[Back-Office] Un utilisateur non-admin a tenté d\'accéder à l\'URL de débanissement d\'un compte utilisateur.');

        $compteRepository = $this->getDoctrine()->getRepository(Compte::class);
        $compte = $compteRepository->Find($id_compte);
    
        $form = $this->createForm(ConfirmationBanCompteType::class, $compte);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compte=$form->getData();
            $compte->setBanni(false);

            //Une connexion à la BDD par l'entity manager (em).
            $em = $this->getDoctrine()->getManager();
            $em->persist($compte);
            $em->flush();

            return $this->redirectToRoute("liste_comptes");
        }

        return $this->render('admin/confirmationBanCompte.html.twig', [
            "form"=>$form->createView(),
            "titre" => "Débannir l'utilisateur N°".$compte->getUsername()." [ID: ".$compte->getId()."]",
            "action" => "Débannir"
        ]);
    }

}
