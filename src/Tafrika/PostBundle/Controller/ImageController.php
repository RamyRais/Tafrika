<?php
/**
 * Created by PhpStorm.
 * User: ramy
 * Date: 13/01/15
 * Time: 23:29
 */

namespace Tafrika\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tafrika\PostBundle\Entity\Image;
use Tafrika\PostBundle\Form\ImageType;

class ImageController extends Controller{

    public function addImageAction(){
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            $request = $this->get('request');
            $request->getSession()->set('_security.main.target_path', $request->getUri());
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $user = $this->get('security.context')->getToken()->getUser();
        $image = new Image();
        $image->setUser($user);
        $form = $this->createForm(new ImageType(), $image);
        $request = $this->get('request');
        if( $request->getMethod() == 'POST'){
            $form->bind($request);
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($image);
                $em->flush();
                return $this->redirect($this->generateUrl('tafrika_index'));

            }
        }
        return $this->render('TafrikaPostBundle:Image:create.html.twig',array(
            'form'=>$form->createView()
        ));
    }
}