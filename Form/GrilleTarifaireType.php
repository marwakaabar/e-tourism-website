<?php

namespace App\Form;

use App\Entity\GrilleTarifaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class GrilleTarifaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       
        $builder
            ->add('dateDebut')
            ->add('dateFin')
        
            ->add('hotel')
          
            ->add('PrixEnfant')  
            ->add('prix')
            ->add('description',TextareaType::class, [
                'attr' => ['class' => 'tinymce'],
            ])
          
            
       
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GrilleTarifaire::class,
        ]);
    }
}