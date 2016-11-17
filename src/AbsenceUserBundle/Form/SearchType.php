<?php

namespace AbsenceUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormError;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('recherche', 'text', array(
                'label' => '  ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Rechercher un Username ou un Mail'
                )
            ));


    }

    public function getName()
    {
        return 'search_type';
    }
}

