<?php
/**
 * Created by PhpStorm.
 * User: ramy
 * Date: 01/01/15
 * Time: 14:02
 */

namespace Tafrika\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tafrika\PostBundle\Entity\Comment;
use Tafrika\PostBundle\Form\CommentType;
use Tafrika\PostBundle\Entity\Status;
use Tafrika\PostBundle\Form\StatusType;
use Tafrika\PostBundle\Form\StatusEditType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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

    /**
     * @ParamConverter("status", options={"mapping": {"status_id": "id"}})
     */
    public function showStatusAction(Status $status){
        /*if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            $request = $this->get('request');
            $request->getSession()->set('_security.main.target_path', $request->getUri());
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }*/
        $list=$this->getDoctrine()
            ->getRepository('TafrikaPostBundle:Comment')->findBy(array('post' => $status));

        $user = $this->get('security.context')->getToken()->getUser();
        $comment = new Comment();
        $comment->setUser($user);
        $comment->setPost($status);
        $form = $this->createForm(new CommentType, $comment);
        $request = $this->get('request');
        if( $request->getMethod() == 'POST'){
            $form->bind($request);
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();
                return $this->redirect($this->generateUrl('status_show', array('status_id' => $status->getId())));

            }
        }



        return $this->render('TafrikaPostBundle:Status:show.html.twig',array(
            'status'=>$status,'form'=>$form->createView(),'list'=>$list
        ));
    }

    /**
     * @ParamConverter("status", options={"mapping": {"status_id": "id"}})
     */
    public function editStatusAction(Status $status){
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirect($this->generateUrl('status_show', array('status_id'=>$status->getId())));
        } else{
            $user = $this->get('security.context')->getToken()->getUser();
            if($user != $status->getUser()){
                return $this->redirect($this->generateUrl('status_show', array('status_id'=>$status->getId())));

            }
        }
        $form = $this->createForm(new StatusEditType, $status);
        $request = $this->get('request');
        if( $request->getMethod() == 'POST'){
            $form->bind($request);
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($status);
                $em->flush();
                return $this->redirect($this->generateUrl('status_show', array('status_id'=>$status->getId())));

            }
        }
        return $this->render('TafrikaPostBundle:Status:edit.html.twig',array(
            'form'=>$form->createView(),
            'status'=>$status
        ));

    }
}