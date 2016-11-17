<?php

namespace AbsenceUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AbsenceUserBundle:Default:index.html.twig');
    }
}
