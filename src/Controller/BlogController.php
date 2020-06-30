<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Evenements;
use App\Repository\EvenementsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\EvenementType;
class BlogController extends AbstractController

{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(EvenementsRepository $repo)
    {
       
        $evenements=$repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'evenements'=> $evenements
        ]);
    }
    /**
     * @Route("/", name="accueil")
     */
    public function accueil(){
        return $this->render('blog/accueil.html.twig');

    }
     /**
     * @Route("/blog/new",name="blog_create")
     * @Route("/blog/{id}/edit",name="blog_edit")
     */
    public function form( Evenements $evenement=null,Request $request){
        $manager = $this->getDoctrine()->getManager();
        if (!$evenement){
            $evenement =new Evenements();
        }
        
        //$form=$this->createFormBuilder($evenement)
                 //  ->add('titre')
                 //  ->add('contenu')
                 //  ->add('video')
                
                  // ->getForm();
                  $form=$this->createForm(EvenementType::class,$evenement);
                  $form->handleRequest($request);
                  if($form->isSubmitted() && $form->isValid()){
                      if(!$evenement->getId()){
                        $evenement->setDateCreation(new \DateTime());
                       }
                      

                       $manager->persist($evenement);
                       $manager->flush();

                       return $this->redirectToRoute('blog_show',['id'=>$evenement->getId()]);

                   }

        return $this->render('blog/create.html.twig',[
            'formulaireEvenement'=> $form->createView(),
            'editMode' =>$evenement->getId() !== null
        ]);
        
      
    }
     /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(EvenementsRepository $repo, $id){
        
        $evenement=$repo->find($id);

        return $this->render('blog/show.html.twig',[
            'evenement'=>$evenement
        ]);  
    }


    
   
}
