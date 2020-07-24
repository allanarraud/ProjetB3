<?php

namespace App\Controller;

use App\Entity\Commentaires;
use App\Form\CommentairesType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ConfirmationSuppressionCommentaireType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentaireController extends AbstractController
{
    /**
     * @Route("commentaires/modifier/{id_commentaire}", name="commentaire_modifier")
     */
    public function modifier($id_commentaire, Request $request)
    {
        $commentaireRepository = $this->getDoctrine()->getRepository(Commentaires::class);
        $commentaire = $commentaireRepository->find($id_commentaire);

        $evenement = $commentaire->getEvenements();
        $id_evenement = $evenement->getId();

        $form = $this->createForm(CommentairesType::class, $commentaire);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire = $form->getData();

            //une connexion à la BDD par l'entity manager
            $em = $this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();

            return $this->redirectToRoute("evenement_details", array('id_evenement' => $id_evenement));
        }

        return $this->render('commentaire/formulaireModificationCommentaire.html.twig', [
            "form" => $form->createView(),
            'commentaire' => $commentaire,
            "titre" => "Modification d'un commentaire.",
        ]);
    }

    /**
     * @Route("commentaires/supprimer/{id_commentaire}", name="commentaire_supprimer")
     */
    public function supprimer($id_commentaire, Request $request)
    {
        $commentaireRepository = $this->getDoctrine()->getRepository(Commentaires::class);
        $commentaire = $commentaireRepository->find($id_commentaire);

        $evenement = $commentaire->getEvenements();
        $id_evenement = $evenement->getId();

        $form = $this->createForm(ConfirmationSuppressionCommentaireType::class, $commentaire);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire = $form->getData();

            //une connexion à la BDD par l'entity manager
            $em = $this->getDoctrine()->getManager();
            $em->remove($commentaire);
            $em->flush();

            return $this->redirectToRoute("evenement_details", array('id_evenement' => $id_evenement));
        }

        return $this->render('commentaire/ConfirmationSuppressionCommentaire.html.twig', [
            "form" => $form->createView(),
            'commentaire' => $commentaire,
            "titre" => "Suppression d'un commentaire.",
        ]);
    }
}
