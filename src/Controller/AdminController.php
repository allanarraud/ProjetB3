<?php

namespace App\Controller;

use App\Entity\Compte;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Un utilisateur non-admin a tenté d\'accéder à l\'accueil du back-office.');

        return $this->render('admin/admin.html.twig');
    }

    /**
     * @Route("admin/comptes", name="liste_comptes")
     */
    public function listerComptes()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Un utilisateur non-admin a tenté d\'accéder à la liste des utilisateurs du back-office.');

        $compteRepository = $this->getDoctrine()->getRepository(Compte::class);

        $lesComptes = $compteRepository->findAll();

        return $this->render('admin/listeComptes.html.twig', [
            'lesComptes' => $lesComptes,
        ]);
    }

}
