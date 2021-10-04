<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\ContactType;
use App\Form\CommentaireType;
use App\Form\InscriptionType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contact;
use App\Entity\Commentaire;
use App\Entity\Utilisateur;
use App\Entity\Theme;

class StaticController extends AbstractController
{
    #[Route('/accueil', name: 'accueil')]
    public function accueil(): Response
    {
        return $this->render('static/accueil.html.twig', []);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, \Swift_Mailer $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);


        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $this->addFlash('notice', 'Bouton appuyé par '.$contact->getNom());

                $message = (new \Swift_Message($contact->getSujet()))
                ->setFrom($contact->getEmail())
                ->setTo('remi.becquaert35@gmail.com')
                ->setBody($this->renderView('emails/contact-email.html.twig', array('nom'=>$contact->getNom(), 'sujet'=>$contact->getSujet(), 'message'=>$contact->getMessage())), 'text/html');
                $mailer->send($message);

                $em = $this->getDoctrine()->getManager();
                $em->persist($contact);
                $em->flush();
                return $this->redirectToRoute('contact');    

            }
        }                //->setBody($form->get('message')->getData());

        return $this->render('static/contact.html.twig', ['form'=>$form->createView()]);
    } 

    #[Route('/mentions', name: 'mentions')]
    public function mentions(): Response
    {
        return $this->render('static/mentions.html.twig', []);
    }

    #[Route('/apropos', name: 'apropos')]
    public function apropos(): Response
    {
        return $this->render('static/apropos.html.twig', []);
    }

    #[Route('/inscription', name: 'inscription')]
    public function inscription(Request $request): Response{

        $utilisateur = new Utilisateur();
        $form = $this->createForm(InscriptionType::class, $utilisateur);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $prenom = $utilisateur->getPrenom();
                $email = $utilisateur->getEmail();
                $this->addFlash('notice', 'Bienvenue sur le site, '.$utilisateur->getPrenom());

                $em = $this->getDoctrine()->getManager();
                $em->persist($utilisateur);
                $em->flush();
                return $this->redirectToRoute('inscription');    

            }
        }
        return $this->render('static/inscription.html.twig', ['form'=>$form->createView()]);
    }         
            
    #[Route('/commentaire', name: 'commentaire')]
    public function commentaire(Request $request): Response{

        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $nom = $commentaire->getNom();
                $this->addFlash('notice', 'Bouton appuyé par '.$commentaire->getNom());

                $em = $this->getDoctrine()->getManager();
                $em->persist($commentaire);
                $em->flush();
            }
        }
        return $this->render('static/commentaire.html.twig', ['form'=>$form->createView()]);
    }

    #[Route('/voirContact', name: 'voirContact')]
    public function voirContact(): Response{

        $repoContact = $this->getDoctrine()->getRepository(Contact::class);
        $contacts = $repoContact->findBy(array(), array('nom'=>'ASC'));


        return $this->render('static/voirContact.html.twig', ['contacts'=>$contacts]);
    }

    #[Route('/voirCommentaire', name: 'voirCommentaire')]
    public function voirCommentaire(): Response{

        $repoCommentaire = $this->getDoctrine()->getRepository(Commentaire::class);
        $commentaires = $repoCommentaire->findBy(array(), array('nom'=>'ASC'));


        return $this->render('static/voirCommentaire.html.twig', ['commentaires'=>$commentaires]);
    }
}
