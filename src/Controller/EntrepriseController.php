<?php

namespace App\Controller;

use App\Entity\Commentaire;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use App\Form\CommentaireType;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EntrepriseController extends AbstractController
{
    /**
     * @Route("/entreprises", name="app_entreprise")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        // Récupére les entreprises de la base de données, on récupère le repository de la class Entreprise

        $entreprises = $doctrine->getRepository(Entreprise::class)->findAll();
        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entreprises
        ]);
    }


    // Fonction pour ajouter ou éditer une nouvelle entreprise, on utilise ManagerRegistry pour dire qu'on va intéragir avec la base de données, Entreprise pour dire à quel élément on veut rajouter et request

    /**
     * @Route("/entreprise/add", name ="add_entreprise")
     * @Route("/entreprise/{id}/edit", name="edit_entreprise")
     */
    public function add(ManagerRegistry $doctrine, Entreprise $entreprise = null, Request $request): Response
    {
        // Si l'entreprise n'existe pas on appelle la method add() sinon on appelle la method edit()
        if( !$entreprise){
            $entreprise = new Entreprise();
        }

        // Crée un form en se basant sur l'objet Entreprise, il va récupérer les propriétés de la class entreprise
        $form = $this->createForm(EntrepriseType::class, $entreprise);

        // Permet d'analyser les données insérées dans le form et de récupérer les données pour les mettre dans le formulaire
        $form->handleRequest($request);

        // Vérifie que le formulaire a été soumit et que les champs sont valides (similiaire à filter_input)
        if ($form->isSubmitted() && $form->isValid()) {
            $entreprise = $form->getData(); //Permet d'hydrater l'objet entreprise

            $entityManager = $doctrine->getManager(); 
            $entityManager->persist($entreprise); // Prepare les données
            $entityManager->flush(); // Execute la request (insert into)

            return $this->redirectToRoute('app_entreprise');
        }
        // View pour afficher le formulaire d'ajout
        return $this->render('entreprise/add.html.twig', [
            'formAddEntreprise' => $form->createView(), // Génère le formulaire visuellement
            'edit'=> $entreprise->getId() // Renvoie un booleen pour changer le h1
        ]);
    }

    /**
     * @Route("entreprise/{id}/delete", name="delete_entreprise")
     */
    public function delete(ManagerRegistry $doctrine, Entreprise $entreprise): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($entreprise);
        $entityManager->flush();

        return $this->redirectToRoute('app_entreprise');
    }
    

    // Récupère automatiquement l'ID en passant l'objet employe en paramètre de la fonction
    /**
     * @Route("entreprise/{id}", name="show_entreprise")
     */
    public function show(ManagerRegistry $doctrine, Entreprise $entreprise, Commentaire $commentaire = null, Request $request): Response
    {
        // Le commentaire doit forcément être vide de base
        $commentaire = new Commentaire();
        $commentaire->setCreatedAt(new \DateTime());
        $commentaire->setEntreprise($entreprise);
        
        $form = $this->createForm(CommentaireType::class, $commentaire);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire = $form->getData(); //Permet d'hydrater l'objet commentaire

            $entityManager = $doctrine->getManager(); 
            $entityManager->persist($commentaire); // Prepare les données
            $entityManager->flush(); // Execute la request (insert into)

            return $this->redirectToRoute('show_entreprise', ['id' => $entreprise->getId() ]);
        }
        

        return $this->render('entreprise/show.html.twig', [
            'entreprise' => $entreprise,
            'formAddComment' => $form->createView()
        ]);
    }
    
}