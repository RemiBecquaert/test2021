<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Theme;

use App\Entity\Fichier;
use App\Form\AjoutFichierType;

class FichierController extends AbstractController
{
    #[Route('/ajoutFichier', name: 'ajoutFichier')]
    public function ajoutFichier(Request $request): Response{

        $fichier = new Fichier();
        $form = $this->createForm(AjoutFichierType::class, $fichier);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $idTheme = $form->get('theme')->getData();
                $theme = $this->getDoctrine()->getRepository(Theme::class)->find($idTheme);

                $fichierPhysique = $fichier->getNom();
                $fichier->setDate(new \DateTime());
                $ext = '';
                //je protege un guess extension qui renvoie null
                if($fichierPhysique->guessExtension()!= null){
                    $ext = $fichierPhysique->guessExtension();
                }
                dump($ext);
                $fichier->setExtension($ext);

                $fichier->setTaille($fichierPhysique->getSize());
                $fichier->setNom(md5(uniqid()));
                $fichier->addTheme($theme);

                try{
                    $fichierPhysique->move($this->getParameter('file_directory'), $fichier->getNom());
                    $this->addFlash('notice', 'Fichier envoyé !');
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($fichier);
                    $em->flush();
                }
                catch(FileException $e){
                    $this->addFlash('notice', 'Problème d\'envoi !');

                }
            }
        }
        return $this->render('static/ajoutFichier.html.twig', ['form'=>$form->createView()]);
    }
}
