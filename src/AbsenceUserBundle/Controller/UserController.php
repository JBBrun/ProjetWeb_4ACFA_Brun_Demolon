<?php

namespace AbsenceUserBundle\Controller;

use AbsenceUserBundle\Form\EditProfileType;
use AbsenceUserBundle\Form\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
Use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Role\Role;
use AbsenceUserBundle\Entity\User;
use AbsenceBundle\Entity\Absence;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\UserEvent;

class UserController extends Controller
{
    /**
     * @Route("/index")
     */
    public function indexAction()
    {
        return $this->render('AbsenceBundle::index.html.twig');
    }

    /**
     * @Route("/login")
     */
    public function loginAction(Request $request)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->render('AbsenceBundle::index.html.twig');
        }
        $authenticationUtils = $this->get('security.authentication_utils');
        return $this->render('AbsenceUserBundle::login.html.twig', array(
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ));
    }

    /**
     * @Route("/list", name="absence_user_list")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $listUser = $em
            ->getRepository('AbsenceUserBundle:User')
            ->findBy(array(), array('username' => 'ASC'));

        $listAbsence = array();
        foreach ($listUser as $user) {
            $Absence = $em
                ->getRepository('AbsenceBundle:Absence')
                ->findBy(array('user' => $user));

            $listAbsence[$user->getId()] = count($Absence);
        }


        return $this->render('AbsenceUserBundle::list.html.twig', array('listUser' => $listUser, 'listAbsence' => $listAbsence));
    }

    /**
     * @Route("/list/order/{type}/{id}")
     */
    public function listOrderAction($type, $id)
    {
        $listUser = null;
        $session = new Session();
       // $session->start();


        switch ($type) {
            case'user':
                $listUser=$this->listOrderUserAction($id,$session);
                break;
            case'year':
                $listUser=$this->listOrderYearAction($id,$session);
                break;
         }
        $listAbsence = array();
        $em = $this->getDoctrine()->getManager();
        foreach ($listUser as $user) {
            $Absence = $em
                ->getRepository('AbsenceBundle:Absence')
                ->findBy(array('user' => $user));

            $listAbsence[$user->getId()] = count($Absence);
        }

        return $this->render('AbsenceUserBundle::list.html.twig', array('listUser' => $listUser, 'listAbsence' => $listAbsence,'year'=> $session->get('year')));
    }


    private function listOrderUserAction($id,$session)
    {

        /**
         * Route("/list/user/UD")
         */
        $year = array();
        if($session->get('year') == null)
        {
            $session->set('year', 'ALL');
        }
        if($session->get('year') != "ALL")
        {
            $year = array('year' => $session->get('year'));
        }
        switch ($id) {
            case'UD':
                $em = $this->getDoctrine()->getManager();
                $listUser = $em
                    ->getRepository('AbsenceUserBundle:User')
                    ->findBy($year, array('username' => 'DESC'));
                break;

            /**
             * Route("/list/user/MA")
             */
            case'MA':
                $em = $this->getDoctrine()->getManager();
                $listUser = $em
                    ->getRepository('AbsenceUserBundle:User')
                    ->findBy($year, array('email' => 'ASC'));
                break;
            /**
             * Route("/list/user/MD")
             */
            case'MD':
                $em = $this->getDoctrine()->getManager();
                $listUser = $em
                    ->getRepository('AbsenceUserBundle:User')
                    ->findBy($year, array('email' => 'DESC'));
                break;
            case'UA':
                $em = $this->getDoctrine()->getManager();
                $listUser = $em
                    ->getRepository('AbsenceUserBundle:User')
                    ->findBy($year, array('username' => 'ASC'));
                break;
        }


      return $listUser;
    }


    private function listOrderYearAction($id,$session)
    {

        $session->set('year',$id);
        $year = array();
        if($session->get('year') != "ALL")
        {
            $year = array('year' => $session->get('year'));
        }
        $em = $this->getDoctrine()->getManager();
        $listUser = $em
            ->getRepository('AbsenceUserBundle:User')
            ->findBy($year);

        return $listUser;
    }



    /**
     * @Route("/list/search")
     */
    public function listSearchction()
    {

        $form = $this->createForm(new SearchType());
        $request = $this->get('request');

        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);

        $data = $form->getData();


        $recherche = $data["recherche"];

        $listUser1 = $em
            ->getRepository('AbsenceUserBundle:User')
            ->findBy(array('email' => $recherche));
        $listUser2 = $em
            ->getRepository('AbsenceUserBundle:User')
            ->findBy(array('username' => $recherche));

        $listUser = $listUser1 + $listUser2;

        $listAbsence = array();
        $em = $this->getDoctrine()->getManager();
        foreach ($listUser as $user) {
            $Absence = $em
                ->getRepository('AbsenceBundle:Absence')
                ->findBy(array('user' => $user));

            $listAbsence[$user->getId()] = count($Absence);
        }

                if ($request->getMethod() == 'POST') {

                    $keyword = $request->request->get('recherche');


                    if ($form->isValid()) {

                return $this->render('AbsenceUserBundle::list.html.twig', array('listUser' => $listUser, 'data' => $recherche,'listAbsence' => $listAbsence));
            }
        }

        $em = $this->getDoctrine()->getManager();
        $listUser = $em
            ->getRepository('AbsenceUserBundle:User')
            ->findBy(array(), array('username' => 'ASC'));

        $listAbsence = array();
        foreach ($listUser as $user) {
            $Absence = $em
                ->getRepository('AbsenceBundle:Absence')
                ->findBy(array('user' => $user));

            $listAbsence[$user->getId()] = count($Absence);
        }


        return $this->render('AbsenceUserBundle::list.html.twig', array('listUser' => $listUser, 'form' => $form->createView(),'listAbsence' => $listAbsence));
    }




    /**
     * @Route("/list/deleteUser/{id}", name="user_list_delete")
     */
    public function deleteUserAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em
            ->getRepository('AbsenceUserBundle:User')
            ->find($id);

        if($user != null) {
            $absences = $em
                ->getRepository('AbsenceBundle:Absence')
                ->findBy(array('user' => $user));
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
            }

            $em->remove($user);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Utilisateur supprimé')
            ;

        }
        return $this->redirectToRoute('absence_user_list');

    }


    /*
        /**
         * @Route("/list/updateUser", name="absence_List_update")
         */
     public function editUserAction($id, Request $request)
     {

         $form = $this->createForm(new EditProfileType());
         $request = $this->get('request');

         if ($request->getMethod() == 'POST') {
             $form->handleRequest($request);
             $data = $form->getData();

             if ($form->isValid()) {
                 $em = $this->getDoctrine()->getManager();
                 $user = $em
                     ->getRepository('AbsenceUserBundle:User')
                     ->find($id);

                 $user->setUsername($data['username']);
                 $user->setEmail($data['email']);
                 $user->setYear($data['year']);
                 $em->flush();
             }
         }
         $request->getSession()
             ->getFlashBag()
             ->add('success', 'Utilisateur modifié')
         ;
         return $this->redirectToRoute('absence_user_list');
     }


    /**
     * @Route("/list/updateUser/{id}", name="user_list_update")
     */

    public function updateUserAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $em
            ->getRepository('AbsenceUserBundle:User')
            ->find($id);

        $form = $this->createForm(new EditProfileType($user->getYear()));
        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            $data = $form->getData();

            if ($form->isValid()) {


                $listUser = $em
                    ->getRepository('AbsenceUserBundle:User')
                    ->findAll();


                $checkData = false;
                foreach ($listUser as $value) {

                    if (($user->getEmail($id) == $data['email']) && ($user == $data['username'])) {
                        $checkData = false;
                    } else if (($user->getEmail($id) != $data['email']) && ($user != $data['username'])) {
                        if (($value->getEmail($id) == $data['email']) || ($value == $data['username'])) {

                            $checkData = true;

                        }

                    } else if (($user->getEmail($id) == $data['email']) && ($user != $data['username'])) {
                        if (($value == $data['username'])) {

                            $checkData = true;

                        }
                    } else if (($user->getEmail($id) != $data['email']) && ($user == $data['username'])) {
                        if (($value->getEmail($id) == $data['email'])) {

                            $checkData = true;

                        }
                    } else {
                        $checkData = false;
                    }

                }

                if ($checkData == false) {

                    $user->setUsername($data['username']);
                    $user->setEmail($data['email']);
                    $user->setYear($data['year']);
                    $url = '';
                    $em->flush();
                    $request->getSession()
                        ->getFlashBag()
                        ->add('success', 'Utilisateur modifié')
                    ;
                    //   $em = $this->getDoctrine()->getManager();
                      // $admin = $em
                        //   ->getRepository('AbsenceBundle:Parameters')
                          // ->findBy(array(), array('id' => 'ASC'));


                                  /*      $message = \Swift_Message::newInstance()
                                            ->setSubject("Modification du compte")
                                            ->setFrom(array($this->getParameter('mailer_user') => $this->getParameter('mailer_sender')))
                                            ->setTo($admin[0]->getMailerDestination())
                                            ->setTo($data['email'])
                                            ->setBody(
                                                $this->renderView(
                                                    'AbsenceUserBundle:message:editProfileMessage.html.twig',
                                                    array('profile' => $data),
                                                    'text/html'
                                                ));

                                        $this->get('mailer')->send($message);*/
                    $listAbsence = array();
                    foreach ($listUser as $user) {
                        $Absence = $em
                            ->getRepository('AbsenceBundle:Absence')
                            ->findBy(array('user' => $user));

                        $listAbsence[$user->getId()] = count($Absence);
                    }
                    return $this->render('AbsenceUserBundle::list.html.twig', array('listUser' => $listUser, 'modify' => $data['username'], 'listAbsence' => $listAbsence));
                } else {
                    $this->get('session')->getFlashBag()->add('error', 'Email or username already used');
                    return $this->render('AbsenceUserBundle:User:editProfile.html.twig',
                        array('form' => $form->createView(), 'dataUser' => $user));
                }
            }
        }
        return $this->render('AbsenceUserBundle:User:editProfile.html.twig',
            array('form' => $form->createView(), 'dataUser' => $user));
    }





//    /**
    //    * @Route("/resetting/request")
//     */
//    public function requestAction()
//    {
//        return $this->render('AbsenceUserBundle:Resetting:request.html.twig');
//    }

}
