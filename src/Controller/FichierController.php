<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Fichier;
use App\Form\AjoutFichierType;

class FichierController extends AbstractController
{
    #[Route('/ajoutFichier', name: 'ajoutFichier')]
    public function ajoutFichier(): Response{

        $fichier = new Fichier();
        $form = $this->createForm(AjoutFichierType::class, $fichier);
        return $this->render('static/ajoutFichier.html.twig', ['form'=>$form->createView()]);
    }
}
