<?php
/**
 * Created by PhpStorm.
 * User: ramy
 * Date: 01/01/15
 * Time: 14:02
 */

namespace Tafrika\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tafrika\PostBundle\Entity\Status;
use Tafrika\PostBundle\Form\StatusType;

class StatusController extends Controller{

    public function addStatusAction(){
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            $request = $this->get('request');
            $request->getSession()->set('_security.main.target_path', $request->getUri());
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $user = $this->get('security.context')->getToken()->getUser();
        $status = new Status();
        $status->setUser($user);
        $form = $this->createForm(new StatusType, $status);
        $request = $this->get('request');
        if( $request->getMethod() == 'POST'){
            $form->bind($request);
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($status);
                $em->flush();
                return $this->redirect($this->generateUrl('tafrika_index'));

            }
        }
        return $this->render('TafrikaPostBundle:Status:create.html.twig',array(
            'form'=>$form->createView()
        ));
    }
}