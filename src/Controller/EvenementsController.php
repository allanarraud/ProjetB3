<?php

namespace App\Controller;

use App\Entity\Evenements;
use App\Form\EvenementType;
use App\Entity\Commentaires;
use App\Form\CommentairesType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ConfirmationSuppressionEvenementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EvenementsController extends AbstractController
{
    /**
     * @Route("/evenements", name="liste_evenements")
     */
    public function index()
    {

        $evenementsRepository = $this->getDoctrine()->getRepository(Evenements::class);

        $lesEvenements = $evenementsRepository->findAll();

        return $this->render('evenements/listeEvenements.html.twig', [
            'lesEvenements' => $lesEvenements,
        ]);
    }

    /**
     * @Route("evenements/ajouter", name="evenement_ajouter")
     */
    public function ajouter(Request $request)
    {
        $user = $this->getUser();
        $evenement = new Evenements();

        $form = $this->createForm(EvenementType::class, $evenement);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenement = $form->getData();
            $dateCommentaire = new \DateTime();
            $dateCommentaire->setTimezone(new \DateTimeZone('Europe/Paris'));
            $commentaire->setDateCommentaire($dateCommentaire);
            $evenement->setCompte($user);

            //Une connexion à la BDD par l'entity manager (em).
            $em = $this->getDoctrine()->getManager();
            $em->persist($evenement);
            $em->flush();

            return $this->redirectToRoute("liste_evenements");
        }

        return $this->render('evenements/formulaireAjoutEvenement.html.twig', [
            "form" => $form->createView(),
            "titre" => "Création d'un évènement.",
        ]);
    }

    /**
     * @Route("evenements/modifier/{id_evenement}", name="evenement_modifier")
     */
    public function modifier($id_evenement, Request $request)
    {
        //récupérer le repository (une connexion à la table en gros...)
        $evenementsRepository = $this->getDoctrine()->getRepository(Evenements::class);
        //récupérer l'évènement à modifier
        $evenement = $evenementsRepository->find($id_evenement);

        $form = $this->createForm(EvenementType::class, $evenement);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenement = $form->getData();

            //une connexion à la BDD par l'entity manager
            $em = $this->getDoctrine()->getManager();
            $em->persist($evenement);
            $em->flush();

            return $this->redirectToRoute("liste_evenements");
        }

        return $this->render('evenements/formulaireModificationEvenement.html.twig', [
            "form" => $form->createView(),
            'evenement' => $evenement,
            "titre" => "Modification de l'évènement \"" . $evenement->getTitre() . "\".",
        ]);
    }

    /**
     * @Route("evenements/supprimer/{id_evenement}", name="evenement_supprimer")
     */
    public function supprimer($id_evenement, Request $request)
    {

        $evenementRepository = $this->getDoctrine()->getRepository(Evenements::class);
        $evenement = $evenementRepository->Find($id_evenement);

        $form = $this->createForm(ConfirmationSuppressionEvenementType::class, $evenement);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenement = $form->getData();

            //Une connexion à la BDD par l'entity manager (em).
            $em = $this->getDoctrine()->getManager();
            $em->remove($evenement);
            $em->flush();

            return $this->redirectToRoute("liste_evenements");
        }

        return $this->render('evenements/confirmationSuppressionEvenement.html.twig', [
            "form" => $form->createView(),
            'evenement' => $evenement,
            "titre" => "Supprimer l'événement \"" . $evenement->getTitre() . "\" ?",
        ]);
    }

    /**
     * @Route("/evenement/details/{id_evenement}", name="evenement_details")
     */
    public function details($id_evenement, Request $request)
    {
        $user = $this->getUser();

        $evenementsRepository = $this->getDoctrine()->getRepository(Evenements::class);
        $evenement = $evenementsRepository->find($id_evenement);

        $commentaire = new Commentaires();
        $form = $this->createForm(CommentairesType::class, $commentaire);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire = $form->getData();
            $dateCommentaire = new \DateTime();
            $dateCommentaire->setTimezone(new \DateTimeZone('Europe/Paris'));
            $commentaire->setDateCommentaire($dateCommentaire);
            $commentaire->setCompte($user);
            $commentaire->setEvenements($evenement);

            //Une connexion à la BDD par l'entity manager (em).
            $em = $this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();

            return $this->redirectToRoute("evenement_details", array('id_evenement' => $id_evenement));
        }

        $commentairesRepository = $this->getDoctrine()->getRepository(Commentaires::class);
        $lesCommentaires = $commentairesRepository->findBy(array('Evenements' => $id_evenement));

        return $this->render('evenements/evenementDetails.html.twig', [
            'titre' => "Détails de l'évènement \"" . $evenement->getTitre() . "\".",
            'evenement' => $evenement,
            'lesCommentaires' => $lesCommentaires,
            "form" => $form->createView(),
        ]);
    }
}