<?php

namespace AbsenceCalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AbsenceCalendarBundle:Default:index.html.twig');
    }
}
