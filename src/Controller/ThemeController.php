<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


use App\Form\ThemeType;
use App\Entity\Theme;

class ThemeController extends AbstractController
{
    #[Route('/creerTheme', name: 'creerTheme')]
    public function creerTheme(Request $request): Response{

        $theme = new Theme();
        $form = $this->createForm(ThemeType::class, $theme);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $this->addFlash('notice', 'Thème ajouté !');

                $em = $this->getDoctrine()->getManager();
                $em->persist($theme);
                $em->flush();
            }
        }

        return $this->render('static/creerTheme.html.twig', ['form'=>$form->createView()]);
    }
}
