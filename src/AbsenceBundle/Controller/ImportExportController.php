<?php

namespace AbsenceBundle\Controller;

use AbsenceBundle\Entity\Files;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImportExportController extends Controller
{


    /**
     * export
     *
     * @Route("/downloadFile/{fileId}")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function exportAction(Request $request, $fileId)
    {

           $em = $this->getDoctrine()->getManager();
            $file = $em
                ->getRepository('AbsenceBundle:Files')
                ->find($fileId);


        $filename = $file->getName();
        $filePath = $_SERVER['DOCUMENT_ROOT'] . $file->getUrl();;


        $response = new Response();
    //    $response->headers->set('Content-Description: File Transfer');
    //    $response->headers->set('Content-Type', 'application/html;');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        $response->headers->set('Content-Length', filesize($filePath));
        $response->sendHeaders();
        $response->setContent(file_get_contents($filePath));

        return $response;
    }

    /**
     * Import action
     * @Route("/importFile", name="")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function importAction(Request $request)
  //  public function importAction(Request $request, $userId, $absenceId)
    {

        if ($request->getMethod() == 'POST') {

            $userId = $request->request->get('userId');
            $absenceId = $request->request->get('absenceId');

                $em = $this->getDoctrine()->getManager();
                $user = $em
                    ->getRepository('AbsenceUserBundle:User')
                    ->find($userId);

                $absence = $em
                    ->getRepository('AbsenceBundle:Absence')
                    ->find($absenceId);

                /*upload file*/
                $result = "";
                $directory = $this->get('kernel')->getRootDir() . '/../web/uploads/' . $user->getUsername().'/'.$absenceId;
                $request = Request::createFromGlobals();

                if ($request->files->get('importFile') != null) {
                    if (!$request->files->get('importFile')->getSize()) {
                        $this->get('session')->getFlashBag()->add('error', "La demande n'a pas pu aboutir, Le fichier est trop imposant");
                    }
                    $name ="";
                    foreach ($request->files as $uploadedFile) {
                        $name = $uploadedFile->getClientOriginalName();
                        $filename = str_replace("." . $uploadedFile->getClientOriginalExtension(), "_" . uniqid() . "." . $uploadedFile->getClientOriginalExtension(), $uploadedFile->getClientOriginalName());
                        $filePath = $uploadedFile->move($directory, $filename);
                    }

                } else {

                    $this->get('session')->getFlashBag()->add('error', "La demande n'a pas pu aboutir, aucun fichier sélectionné");

                }


                $file = new Files();

                 $file->setUrl('/uploads/' . $user->getUsername().'/'.$absenceId .'/'.$filename);
                 $file->setName($name);
                 $file->setAbsence($absence);
                $em = $this->getDoctrine()->getManager();
                $em->persist($file);
                $em->flush();

                return $this->redirect("/absence/user/".$userId);



            $request->getSession()
                ->getFlashBag()
                ->add('error', 'Une erreur est apparue');
        }

        return $this->redirect("/login");

    }



    /**
     * Import action
     * @Route("/removeFile/{userId}/{fileId}" , name="deleteFile")

     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function removeFileAction(Request $request, $userId, $fileId)
    {

        $em = $this->getDoctrine()->getManager();
        $file = $em
            ->getRepository('AbsenceBundle:Files')
            ->find($fileId);
        $em->remove($file);
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('success', 'Fichier supprimé');

        return $this->redirect("/absence/user/".$userId);
    }


}
