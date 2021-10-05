<?php

namespace App\Form;

use App\Entity\Fichier;
use App\Entity\Theme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\Request;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class AjoutFichierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', FileType::class, array('label' => 'Fichier à upload'))
            ->add('utilisateur', EntityType::class, array('class'=>'App\Entity\Utilisateur', 'choice_label'=>function ($utilisateur){
                return $utilisateur->getNom().' '.$utilisateur->getPrenom();
            }))
            ->add('themes', EntityType::class, array('class'=>'App\Entity\Theme', 'choice_label'=>'nom', 'expanded'=>true, 'multiple'=>true))
            ->add('bouton', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Fichier::class,
        ]);
    }
}
