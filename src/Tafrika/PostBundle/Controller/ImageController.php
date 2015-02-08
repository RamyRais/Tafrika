<?php
/**
 * Created by PhpStorm.
 * User: ramy
 * Date: 13/01/15
 * Time: 23:29
 */

namespace Tafrika\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tafrika\PostBundle\Entity\Comment;
use Tafrika\PostBundle\Form\CommentType;
use Tafrika\PostBundle\Entity\Image;
use Tafrika\PostBundle\Form\ImageType;
use Tafrika\PostBundle\Form\ImageEditType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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

    /**
     * @ParamConverter("image", options={"mapping": {"image_id": "id"}})
     */
    public function showImageAction(Image $image){
        $list=$this->getDoctrine()
            ->getRepository('TafrikaPostBundle:Comment')->findBy(array('post' => $image));

        $user = $this->get('security.context')->getToken()->getUser();
        $comment = new Comment();
        $comment->setUser($user);
        $comment->setPost($image);
        $form = $this->createForm(new CommentType, $comment);
        $request = $this->get('request');
        if( $request->getMethod() == 'POST'){
            $form->bind($request);
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();
                return $this->redirect($this->generateUrl('image_show', array('image_id' => $image->getId())));

            }
        }



        return $this->render('TafrikaPostBundle:Image:show.html.twig',array(
            'image'=>$image,'form'=>$form->createView(),'list'=>$list
        ));
    }

    /**
     * @ParamConverter("image", options={"mapping": {"image_id": "id"}})
     */
    public function editImageAction(Image $image){
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirect($this->generateUrl('status_show', array('status_id'=>$image->getId())));
        } else{
            $user = $this->get('security.context')->getToken()->getUser();
            if($user != $image->getUser()){
                return $this->redirect($this->generateUrl('status_show', array('status_id'=>$image->getId())));

            }
        }
        $form = $this->createForm(new ImageEditType, $image);
        $request = $this->get('request');
        if( $request->getMethod() == 'POST'){
            $form->bind($request);
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($image);
                $em->flush();
                return $this->redirect($this->generateUrl('image_show', array('image_id'=>$image->getId())));

            }
        }
        return $this->render('TafrikaPostBundle:Image:edit.html.twig',array(
            'form'=>$form->createView(),
            'image'=>$image
        ));

    }
}