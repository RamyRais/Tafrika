<?php
/**
 * Created by PhpStorm.
 * User: ramy
 * Date: 01/01/15
 * Time: 22:19
 */

namespace Tafrika\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tafrika\PostBundle\Entity\Video;
use Tafrika\PostBundle\Form\VideoType;

class VideoController extends  Controller{

    public function addVideoAction(){
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            $request = $this->get('request');
            $request->getSession()->set('_security.main.target_path', $request->getUri());
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $user = $this->get('security.context')->getToken()->getUser();
        $video = new Video();
        $video->setUser($user);
        $form = $this->createForm(new VideoType, $video);
        $request = $this->get('request');
        if( $request->getMethod() == 'POST'){
            $form->bind($request);
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($video);
                $em->flush();
                return $this->redirect($this->generateUrl('tafrika_index'));

            }
        }
        return $this->render('TafrikaPostBundle:Video:create.html.twig',array(
            'form'=>$form->createView()
        ));
    }
}