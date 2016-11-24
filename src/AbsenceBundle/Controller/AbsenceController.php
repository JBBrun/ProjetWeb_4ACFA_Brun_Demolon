<?php

namespace AbsenceBundle\Controller;


use AbsenceBundle\Form\AbsenceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AbsenceBundle\Entity\Absence;
use AbsenceUserBundle\Entity\User;

/**
 * The default controller is used to render the main screen the users see when they log in to the admin
 */
class AbsenceController extends Controller
{


    /**
     * @Route("/add_absence")
     */
    public function addAbsenceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $listUser = $em
            ->getRepository('AbsenceUserBundle:User')
            ->findBy(array(), array('username' => 'ASC'));


        $listEtudiant = array();

        foreach ($listUser as $user) {
            $listEtudiant[$user->getID()] = $user->getUsername();
        }
        $form = $this->createForm(new AbsenceType($listEtudiant, null, null));
        $form->get('reason')->setData('fvefezf');

        $request = $this->get('request');


        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            $data = $form->getData();

            if ($form->isValid()) {

                $etudiant = $form["etudiant"]->getData();
                $date = $form["date"]->getData();
                $heure = $form["Heure"]->getData();
                $em = $this->getDoctrine()->getManager();
                $user = $em
                    ->getRepository('AbsenceUserBundle:User')
                    ->find($etudiant);
                $hours = substr($heure, 0, 2);
                $minutes = substr($heure, 3, 2);

                date_time_set($date, intval($hours), intval($minutes));
                $absence = new Absence();

                $absence->setDate($date);
                $absence->setIdLesson(1); // 1 pour l'instant
                $absence->setReason($form["reason"]->getData());
                $absence->setJustify($form["justify"]->getData());
                $absence->setUser($user);
                $em = $this->getDoctrine()->getManager();
                $em->persist($absence);
                $em->flush();

               $message = \Swift_Message::newInstance()
                    ->setSubject('Absence ')
                    ->setFrom($this->getParameter('mailer_user'))
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'AbsenceBundle:absence:mailAbsence.html.twig',
                            array('absence' => $absence, 'user' => $user),
                            'text/html'

                        ));

                $this->get('mailer')->send($message);

                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Absence a bien été créée');

                return $this->redirectToRoute('list_absence');

            }

        }

        return $this->render('AbsenceBundle:absence:addAbsence.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/absence/user/{id}", name="Absence_user")
     */
    public function userAbsenceAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $em
            ->getRepository('AbsenceUserBundle:User')
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $listAbsence = $em
            ->getRepository('AbsenceBundle:Absence')
            ->findBy(array('user' => $user));

        $listFiles = $em
            ->getRepository('AbsenceBundle:Files')
            ->findBy(array("absence" => $listAbsence));

        return $this->render('AbsenceBundle:absence:userAbsence.html.twig', array('user' => $user, 'listAbsence' => $listAbsence, 'listFiles'=>$listFiles));
    }


    /**
     * @Route("/list_absence", name="list_absence")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $listAbsence = $em
            ->getRepository('AbsenceBundle:Absence')
            ->findBy(array(), array('id' => 'ASC'));

        $listUser = array();
        $em = $this->getDoctrine()->getManager();
        foreach ($listAbsence as $absence) {
            $user = $em
                ->getRepository('AbsenceUserBundle:User')
                ->find($absence->getUser()->getId());

            $listUser[$absence->getId()] = $user;
        }

        return $this->render('AbsenceBundle:absence:listAbsence.html.twig', array('listAbsence' => $listAbsence, 'listUser' => $listUser));
    }


    /**
     * @Route("/list/orderAbsence/{id}")
     */
    public function listOrderAction($id)
    {

        switch ($id) {
            case'UA':
                $em = $this->getDoctrine()->getManager();
                $listAbsence = $em
                    ->getRepository('AbsenceBundle:Absence')
                    ->findBy(array(), array('user' => 'ASC'));
                break;

            case'UD':
                $em = $this->getDoctrine()->getManager();
                $listAbsence = $em
                    ->getRepository('AbsenceBundle:Absence')
                    ->findBy(array(), array('user' => 'DESC'));
                break;

            case'JU':
                $em = $this->getDoctrine()->getManager();
                $listAbsence = $em
                    ->getRepository('AbsenceBundle:Absence')
                    ->findBy(array('justify' => 1));
                break;
            case'IN':
                $em = $this->getDoctrine()->getManager();
                $listAbsence = $em
                    ->getRepository('AbsenceBundle:Absence')
                    ->findBy(array('justify' => 0));
                break;
        }

        $listUser = array();
        $em = $this->getDoctrine()->getManager();
        foreach ($listAbsence as $absence) {
            $user = $em
                ->getRepository('AbsenceUserBundle:User')
                ->find($absence->getUser()->getId());

            $listUser[$absence->getId()] = $user;
        }

        return $this->render('AbsenceBundle:absence:listAbsence.html.twig', array('listAbsence' => $listAbsence, 'listUser' => $listUser));
    }


    /**
     * @Route("/list/deleteAbsence/{id}", name="absence_list_delete")
     */
    public function deleteAbsenceAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $absence = $em
            ->getRepository('AbsenceBundle:Absence')
            ->find($id);

        $listfiles = $em
            ->getRepository('AbsenceBundle:Files')
            ->findby(array('absence'=>$absence));

        foreach($listfiles as $file)
        {
            $em->remove($file);
            $em->flush();
        }

        $em->remove($absence);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'Absence supprimée');

        return $this->redirectToRoute('list_absence');

    }


    /**
     * @Route("/list/deleteAllAbsence", name="absence_list_delete_all_justify")
     */
    public function deleteAllAbsenceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $absences = $em
            ->getRepository('AbsenceBundle:Absence')
            ->findby(array());


        foreach ($absences as $absence) {
            $listfiles = $em
                ->getRepository('AbsenceBundle:Files')
                ->findby(array('absence'=>$absence));

            foreach($listfiles as $file)
            {
                $em->remove($file);
                $em->flush();
            }

            $em->remove($absence);
            $em->flush();
        }

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'Toutes les Absences ont été supprimées');

        return $this->redirectToRoute('list_absence');

    }

    /**
     * @Route("/list/updateAbsence/{id}", name="absence_list_update")
     *//*
    public function editAbsenceAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $absence = $em
            ->getRepository('AbsenceBundle:Absence')
            ->find($id);

        $form = $this->createForm(new AbsenceType());
        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            $data = $form->getData();

            }
        $request->getSession()
            ->getFlashBag()
            ->add('success', 'Utilisateur modifié');

        return $this->redirectToRoute('absence_user_list');
    }*/


    /**
     * @Route("/list/updateAbsence/{idUser}/{idAbsence}", name="absence_list_update")
     */

    public function updateAbsenceAction($idUser, $idAbsence, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $em
            ->getRepository('AbsenceUserBundle:User')
            ->find($idUser);

        $absence = $em
            ->getRepository('AbsenceBundle:Absence')
            ->find($idAbsence);

        $listUser = $em
            ->getRepository('AbsenceUserBundle:User')
            ->findBy(array(), array('username' => 'ASC'));

        $listEtudiant = array();

        foreach ($listUser as $user) {
            $listEtudiant[$user->getID()] = $user->getUsername();
        }

        $form = $this->createForm(new AbsenceType($listEtudiant, $absence, $user));
        $request = $this->get('request');


        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            $data = $form->getData();

            if ($form->isValid()) {

                $etudiant = $form["etudiant"]->getData();
                $date = $form["date"]->getData();
                $hours = substr($form["Heure"]->getData(), 0, 2);
                $minutes = substr($form["Heure"]->getData(), 3, 2);
                date_time_set($date, intval($hours), intval($minutes));
                $em = $this->getDoctrine()->getManager();

                $user = $em
                    ->getRepository('AbsenceUserBundle:User')
                    ->find($etudiant);

                $absence->setDate($date);
                $absence->setIdLesson(1);
                $absence->setReason($form["reason"]->getData());
                $absence->setJustify($form["justify"]->getData());
                $absence->setUser($user);

                $em->flush();


                $listAbsence = $em
                    ->getRepository('AbsenceBundle:Absence')
                    ->findBy(array(), array('id' => 'ASC'));

                $listUser = array();
                $em = $this->getDoctrine()->getManager();
                foreach ($listAbsence as $absence) {
                    $user = $em
                        ->getRepository('AbsenceUserBundle:User')
                        ->find($absence->getUser()->getId());

                    $listUser[$absence->getId()] = $user;
                }

                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Absence modifiée');

                return $this->render('AbsenceBundle:absence:listAbsence.html.twig', array('listAbsence' => $listAbsence, 'listUser' => $listUser, 'modify' => $idAbsence));

            }

        }
        return $this->render('AbsenceBundle:absence:editAbsence.html.twig',
            array('form' => $form->createView(),"user" => $user));
    }
}