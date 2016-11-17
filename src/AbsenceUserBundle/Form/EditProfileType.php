<?php

namespace AbsenceUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormError;

class EditProfileType extends AbstractType
{

    public $year;

    function __construct($year = null) {
        $this->year = $year;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'label' => 'Username',
                )
            )
            ->add('email', 'text', array(
                'label' => 'Email',
            ))
            ->add('year', 'choice', array(
                    'label' => 'Année',
                    'choices' => array(
                        '1A' => '1A',
                        '2A' => '2A',
                        '3A' => '3A',
                        '3ACFA' => '3ACFA',
                        '4A' => '4A',
                        '4ACFA' => '4ACFA',
                        '5A' => '5A',
                        '5ACFA' => '5ACFA',
                        'MASTERE' => 'MASTERE'),
                    'data' => $this->year
                )
            )
            ->add('Modifier', 'submit', array(
                'attr' => array('class' => 'btn btn-primary btn')
            ));

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
            if(strlen($data['username'])> '40' || strlen($data['username'])< '2'){
                $form->get('username')->addError(new FormError('Ce champs dépasse la limite de caractères autorisés'));
            }
            if(strlen($data['email'])> '40' || strlen($data['email'])< '2'){
                $form->get('email')->addError(new FormError('Ce champs dépasse la limite de caractères autorisés'));
            }
        });


        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
            $event->setData($data);
        });
    }

    public function getName()
    {
        return 'EditProfile_Type';
    }
}
