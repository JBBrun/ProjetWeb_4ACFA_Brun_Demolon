<?php

namespace AbsenceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormError;
use  AbsenceUserBundle\Entity\User;
use AbsenceBundle\Entity\Absence;
use Symfony\Component\Validator\Constraints\DateTime;

class AbsenceType extends AbstractType
{

    public $etudiants;
    public $data;
    public $user;

    function __construct(array $etudiants = null, Absence $data = null, User $user = null)
    {
        $this->etudiants = $etudiants;
        $this->data = $data;
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('etudiant', 'choice', array(
                'label' => 'Etudiant',
                'choices' => $this->etudiants,
                'data' => isset($this->user) ? $this->user->getId() : null
            ))
            ->add('date', 'date', array(
                'label' => 'Date Absence',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'data' => isset($this->data) ? $this->data->getDate() : null,
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy']))

            ->add('Heure', 'time', array(
                'label' => 'Heure:',
                'input' => "string",
                'required' => true,
               'data' => isset($this->data) ? $this->data->getDate()->format("h:i:s") : null,
                'attr' => array(
                    'placeholder' => 'Heure',
                    'class' => 'heure-select'
                )))

            ->add('reason', 'text', array(
                'label' => 'Motif',
                'data' => isset($this->data) ? $this->data->getReason(): "Non communiqué",
                'attr' => array(
                    'placeholder' => 'Motif')))

            ->add('justify', 'checkbox', array(
                    'required' => false,
                    'data' => isset($this->data) ? $this->data->getJustify() : null
                    )
            )

            ->add('Envoyer', 'submit', array(
                'attr' => array('class' => 'btn-primary btn')
            ));


    }

    public function getName()
    {
        return 'addAbsence_type';
    }
}

