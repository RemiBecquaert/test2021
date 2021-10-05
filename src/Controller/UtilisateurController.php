<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

use App\Entity\Utilisateur;

class UtilisateurController extends AbstractController
{
    #[Route('/listeUtilisateur', name: 'listeUtilisateur')]
    public function listeUtilisateur(Request $request): Response
    {
        $doctrine = $this->getDoctrine();
        $em = $this->getDoctrine()->getManager();

        //suppression
        if($request->get('id') != null){
            $utilisateur = $doctrine->getRepository(Utilisateur::class)->find($request->get('id'));
            try{
                $filesystem = new Filesystem();
                foreach ($utilisateur->getFichiers() as $fichier) {
                    if ($filesystem->exists($this->getParameter('file_directory').'/'.$fichier->getNom())){
                        $filesystem->remove($this->getParameter('file_directory').'/'.$fichier->getNom());
                    }
                    $em->remove($fichier);
                }

            } catch(IOExceptionInterface $exception){

            }
            $em->remove($utilisateur);
            $em->flush();
            return $this->redirectToRoute('listeUtilisateur');
        }

        $repoUtilisateur = $this->getDoctrine()->getRepository(Utilisateur::class);
        $utilisateurs = $repoUtilisateur->findBy(array(), array('nom'=>'ASC'));

        return $this->render('static/listeUtilisateur.html.twig', ['utilisateurs'=>$utilisateurs]);

    }
}
