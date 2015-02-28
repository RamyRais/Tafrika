<?php
/**
 * Created by PhpStorm.
 * User: ramy
 * Date: 01/01/15
 * Time: 14:02
 */

namespace Tafrika\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\BrowserKit\Response;
use Tafrika\PostBundle\Entity\Comment;
use Tafrika\PostBundle\Entity\Vote;
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

        $user = $this->get('security.context')->getToken()->getUser();

        $vote = $this->getDoctrine()->getRepository('TafrikaPostBundle:Vote')->findOneBy(
            array('post' => $status,
                'user' => $user)
        );
        $em = $this->getDoctrine()->getManager();

        if (!$vote) {

            $user_vote="no_vote";

        } else if($vote->getVote()==1){

            $user_vote="vote_up";

        }else{

            $user_vote="vote_down";

        }
        $list=$this->getDoctrine()
            ->getRepository('TafrikaPostBundle:Comment')->findBy(array('post' => $status));
        return $this->render('TafrikaPostBundle:Status:show.html.twig',array(
            'status'=>$status,'list'=>$list,'user_vote'=>$user_vote
        ));
    }

    /**
     * @ParamConverter("status", options={"mapping": {"status_id": "id"}})
     */
    public function voteUpStatusAction(Status $status){

        //$request = $this->container->get('request');


            $user = $this->get('security.context')->getToken()->getUser();
            $vote = $this->getDoctrine()->getRepository('TafrikaPostBundle:Vote')->findOneBy(
                array('post' => $status,
               'user' => $user)
            );
            $em = $this->getDoctrine()->getManager();
            $likes=$status->getLikes();

            if (!$vote) {
                $vote = new Vote();
                $vote->setPost($status);
                $vote->setUser($user);
                $vote->setVote(1);
                $em->persist($vote);

                $likes=$likes+1;
                $status->setLikes($likes);
                $em->persist($status);
                $em->flush();

                $button="up_button";
                $response=new JsonResponse();
                return $response->setData(array('button'=>$button));
            } else if($vote->getVote()==1){
                $em->remove($vote);
                $likes=$likes-1;
                $status->setLikes($likes);
                $em->persist($status);
                $em->flush();

                $button="no_button";
                $response=new JsonResponse();
                return $response->setData(array('button'=>$button));

            }else{
                $vote->setVote(1);
                $em->persist($vote);
                $likes=$likes+2;
                $status->setLikes($likes);
                $em->persist($status);
                $em->flush();

                $button="up_button";
                $response=new JsonResponse();
                return $response->setData(array('button'=>$button));
            }
        //die($status->getId());



    }

    /**
     * @ParamConverter("status", options={"mapping": {"status_id": "id"}})
     */
    public function voteDownStatusAction(Status $status){

        //$request = $this->container->get('request');


        $user = $this->get('security.context')->getToken()->getUser();
        $vote = $this->getDoctrine()->getRepository('TafrikaPostBundle:Vote')->findOneBy(
            array('post' => $status,
                'user' => $user)
        );
        $em = $this->getDoctrine()->getManager();
        $likes=$status->getLikes();

        if (!$vote) {
            $vote = new Vote();
            $vote->setPost($status);
            $vote->setUser($user);
            $vote->setVote(-1);
            $em->persist($vote);

            $likes=$likes-1;
            $status->setLikes($likes);
            $em->persist($status);
            $em->flush();

            $button="down_button";
            $response=new JsonResponse();
            return $response->setData(array('button'=>$button));
        }
        else if($vote->getVote()==-1){
            $em->remove($vote);
            $likes=$likes+1;
            $status->setLikes($likes);
            $em->persist($status);
            $em->flush();

            $button="no_button";
            $response=new JsonResponse();
            return $response->setData(array('button'=>$button));

        }else{
            $vote->setVote(-1);
            $em->persist($vote);
            $likes=$likes-2;
            $status->setLikes($likes);
            $em->persist($status);
            $em->flush();

            $button="down_button";
            $response=new JsonResponse();
            return $response->setData(array('button'=>$button));
        }
        //die($status->getId());



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