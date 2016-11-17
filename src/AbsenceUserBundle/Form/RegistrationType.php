<?php
/**
 * Created by PhpStorm.
 * User: jbr14121
 * Date: 18/12/15
 * Time: 12:57
 */

namespace AbsenceUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'label' => 'Adresse e-mail'))

            ->add('plainPassword', 'hidden', array(
                'label' => 'Field'
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
                  'MASTERE' => 'MASTERE'


              ))
    );
    }


    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }


    public function getBlockPrefix()
    {
        return 'absence_user_registration';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
}

}
