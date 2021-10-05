<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

use App\Entity\Theme;
use App\Entity\Fichier;
use App\Form\AjoutFichierType;

class FichierController extends AbstractController
{
    #[Route('/ajoutFichier', name: 'ajoutFichier')]
    public function ajoutFichier(Request $request): Response{

        $fichier = new Fichier();
        $form = $this->createForm(AjoutFichierType::class, $fichier);
        $doctrine = $this->getDoctrine();
        $em = $this->getDoctrine()->getManager();

        //suppression
        if($request->get('id') != null){
            $f = $doctrine->getRepository(Fichier::class)->find($request->get('id'));
            try{
                $filesystem = new Filesystem();
                if ($filesystem->exists($this->getParameter('file_directory').'/'.$f->getNom())){
                    $filesystem->remove($this->getParameter('file_directory').'/'.$f->getNom());
                }
            } catch(IOExceptionInterface $exception){

            }
            $em->remove($f);
            $em->flush();
            return $this->redirectToRoute('ajoutFichier');
        }
        //fin suppression
        $fichiers = $doctrine->getRepository(Fichier::class)->findBy(array(), array('date'=>'DESC'));

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){

                //$idTheme = $form->get('theme')->getData();
                //$theme = $this->getDoctrine()->getRepository(Theme::class)->find($idTheme);

                $fichierPhysique = $fichier->getNom();
                $fichier->setDate(new \DateTime());
                $ext = '';
                //je protege un guess extension qui renvoie null
                if($fichierPhysique->guessExtension()!= null){
                    $ext = $fichierPhysique->guessExtension();
                }
                $fichier->setExtension($ext);
                $fichier->setOriginal($fichierPhysique->getClientOriginalName());

                $fichier->setTaille($fichierPhysique->getSize());
                $fichier->setNom(md5(uniqid()));
                //$fichier->addTheme($theme);

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
                return $this->redirectToRoute('ajoutFichier');    

            }
        }
        return $this->render('static/ajoutFichier.html.twig', ['form'=>$form->createView(), 'fichiers' => $fichiers]);
    }

    //fonction telechargement
    #[Route('/telechargement-fichier/{id}',name: 'telechargement-fichier',requirements:["id"=>"\d+"])]
    public function telechargementFichier(int $id) {
        $doctrine = $this->getDoctrine();
        $repoFichier = $doctrine->getRepository(Fichier::class);
        $fichier = $repoFichier->find($id);
        if ($fichier == null){
            $this->redirectToRoute('ajoutFichier');
       }
       else {
        $filesystem = new Filesystem();
        if ($filesystem->exists($this->getParameter('file_directory').'/'.$fichier->getNom())){
           return $this->file($this->getParameter('file_directory').'/'.$fichier->getNom(), $fichier->getOriginal());
        }
        else {
            $this->addFlash('notice', 'Fichier inexistant !');
            return $this->redirectToRoute('ajoutFichier');

        }
       }
    }


}
