<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Employe;
use App\Form\EmployeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeController extends AbstractController
{
    /**
     * @Route("/employes", name="app_employe")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        // Similaire à :
        //SELECT prenom FROM employe WHERE prenom = "John" ORDER BY prenom ASC

        // $employes = $doctrine->getRepository(Employe::class)->findBy(["ville" => "Avignon"], ["prenom" => "ASC"]);
        // return $this->render('employe/index.html.twig', [
        //     "employes" => $employes
        // ]);

        $employes = $doctrine->getRepository(Employe::class)->findBy([], ["prenom" => "ASC"]);

        return $this->render('employe/index.html.twig', [
            "employes" => $employes
        ]);

    }

    // Fonction pour ajouter ou éditer un nouvel employe, on utilise ManagerRegistry pour dire qu'on va intéragir avec la base de données, Employe pour dire à quel élément on veut rajouter et request

    /**
     * @Route("/employe/add", name ="add_employe")
     * @Route("/employe/{id}/edit", name ="edit_employe")
     */
    public function add(ManagerRegistry $doctrine, Employe $employe = null, Request $request): Response
    {
        // Si l'employé n'existe pas on appelle la method add() sinon on appelle la method edit()
        if( !$employe){
            $employe = new Employe();
        }

        // Crée un form en se basant sur l'objet Employe, il va récupérer les propriétés de la class employe
        $form = $this->createForm(EmployeType::class, $employe);

        // Permet d'analyser les données insérées dans le form et de récupérer les données pour les mettre dans le formulaire
        $form->handleRequest($request);

        // Vérifie que le formulaire a été soumit et que les champs sont valides (similiaire à filter_input)
        if ($form->isSubmitted() && $form->isValid()) {
            $employe = $form->getData(); //Permet d'hydrater l'objet employe

            $entityManager = $doctrine->getManager(); 
            $entityManager->persist($employe); // Prepare les données
            $entityManager->flush(); // Execute la request (insert into)

            return $this->redirectToRoute('app_employe');
        }
        // View pour afficher le formulaire d'ajout
        return $this->render('employe/add.html.twig', [
            'formAddEmploye' => $form->createView(), // Génère le formulaire visuellement
            'edit' => $employe->getId()
        ]);
    }

    /**
     * @Route("employe/{id}/delete", name="delete_employe")
     */
    public function delete(ManagerRegistry $doctrine, Employe $employe): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($employe);
        $entityManager->flush();

        return $this->redirectToRoute('app_employe');
    }

    // Récupère automatiquement l'ID en passant l'objet employe en paramètre de la fonction
    
    /**
     * @Route("employe/{id}", name="show_employe")
     */
    public function show(Employe $employe) : Response
    {       
        return $this->render('employe/show.html.twig', [
            "employe" => $employe
        ]);
    }
}