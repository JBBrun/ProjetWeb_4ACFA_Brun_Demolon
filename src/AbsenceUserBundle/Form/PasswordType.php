<?php
/**
 * Created by PhpStorm.
 * User: jbr14121
 * Date: 18/12/15
 * Time: 12:57
 */

namespace AbsenceUserBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

class PasswordType extends AbstractType
{
    private $password;

    public function __construct(string $password)
    {
    $this->$password = $password;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault($this->password);
    }

// ...
}