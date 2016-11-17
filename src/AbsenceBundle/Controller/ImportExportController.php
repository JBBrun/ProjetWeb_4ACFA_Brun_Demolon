<?php

namespace AbsenceBundle\Controller;

use AbsenceBundle\Entity\Files;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ImportExportController extends Controller
{


    /**
     * export
     *
     * @Route("/download/{fileId}")
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
      //  $filePath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/jb/' . $filename;
        $filePath = $file->getUrl();

        $response = new Response();
        //  $response->headers->set('Content-Description: File Transfer');
        $response->headers->set('Content-Type', 'text/csv;');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        $response->headers->set('Content-Length', filesize($filePath));
        $response->sendHeaders();
        $response->setContent(file_get_contents($filePath));

        return $response;
    }

    /**
     * Import action
     * @Route("/importAbsence", name="")
     * ("/importAbsence/{{user.id}}/{{absence.id}}", name="")
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

                 $file->setUrl('/web/uploads/' . $user->getUsername().'/'.$absenceId .'/'.$filename);
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


}
