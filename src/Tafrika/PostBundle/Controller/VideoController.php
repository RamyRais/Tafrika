<?php
/**
 * Created by PhpStorm.
 * User: ramy
 * Date: 01/01/15
 * Time: 22:19
 */

namespace Tafrika\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tafrika\PostBundle\Entity\Comment;
use Tafrika\PostBundle\Form\CommentType;
use Tafrika\PostBundle\Entity\Vote;use Tafrika\PostBundle\Entity\Video;
use Tafrika\PostBundle\Form\VideoType;
use Tafrika\PostBundle\Form\VideoEditType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
                $video->setUrl(preg_replace('/watch\?v=/', "embed/", $video->getUrl()));
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

    /**
     * @ParamConverter("video", options={"mapping": {"video_id": "id"}})
     */
    public function showVideoAction(Video $video){


        $user = $this->get('security.context')->getToken()->getUser();

        $vote = $this->getDoctrine()->getRepository('TafrikaPostBundle:Vote')->findOneBy(
            array('post' => $video,
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
        $comments=$this->getDoctrine()
            ->getRepository('TafrikaPostBundle:Comment')->findBy(array('post' => $video));
        return $this->render('TafrikaPostBundle:Video:show.html.twig',array(
            'video'=>$video,'comments'=>$comments,'user_vote'=>$user_vote
        ));
    }

    /**
     * @ParamConverter("video", options={"mapping": {"video_id": "id"}})
     */
    public function voteUpVideoAction(Video $video){

        //$request = $this->container->get('request');


        $user = $this->get('security.context')->getToken()->getUser();
        $vote = $this->getDoctrine()->getRepository('TafrikaPostBundle:Vote')->findOneBy(
            array('post' => $video,
                'user' => $user)
        );
        $em = $this->getDoctrine()->getManager();
        $likes=$video->getLikes();

        if (!$vote) {
            $vote = new Vote();
            $vote->setPost($video);
            $vote->setUser($user);
            $vote->setVote(1);
            $em->persist($vote);

            $likes=$likes+1;
            $video->setLikes($likes);
            $em->persist($video);
            $em->flush();

            $button="up_button";
            $response=new JsonResponse();
            return $response->setData(array('button'=>$button));
        } else if($vote->getVote()==1){
            $em->remove($vote);
            $likes=$likes-1;
            $video->setLikes($likes);
            $em->persist($video);
            $em->flush();

            $button="no_button";
            $response=new JsonResponse();
            return $response->setData(array('button'=>$button));

        }else{
            $vote->setVote(1);
            $em->persist($vote);
            $likes=$likes+2;
            $video->setLikes($likes);
            $em->persist($video);
            $em->flush();

            $button="up_button";
            $response=new JsonResponse();
            return $response->setData(array('button'=>$button));
        }
        //die($status->getId());



    }

    /**
     * @ParamConverter("video", options={"mapping": {"video_id": "id"}})
     */
    public function voteDownVideoAction(Video $video){

        //$request = $this->container->get('request');


        $user = $this->get('security.context')->getToken()->getUser();
        $vote = $this->getDoctrine()->getRepository('TafrikaPostBundle:Vote')->findOneBy(
            array('post' => $video,
                'user' => $user)
        );
        $em = $this->getDoctrine()->getManager();
        $likes=$video->getLikes();

        if (!$vote) {
            $vote = new Vote();
            $vote->setPost($video);
            $vote->setUser($user);
            $vote->setVote(-1);
            $em->persist($vote);

            $likes=$likes-1;
            $video->setLikes($likes);
            $em->persist($video);
            $em->flush();

            $button="down_button";
            $response=new JsonResponse();
            return $response->setData(array('button'=>$button));
        }
        else if($vote->getVote()==-1){
            $em->remove($vote);
            $likes=$likes+1;
            $video->setLikes($likes);
            $em->persist($video);
            $em->flush();

            $button="no_button";
            $response=new JsonResponse();
            return $response->setData(array('button'=>$button));

        }else{
            $vote->setVote(-1);
            $em->persist($vote);
            $likes=$likes-2;
            $video->setLikes($likes);
            $em->persist($video);
            $em->flush();

            $button="down_button";
            $response=new JsonResponse();
            return $response->setData(array('button'=>$button));
        }
        //die($status->getId());



    }
    /**
     * @ParamConverter("video", options={"mapping": {"video_id": "id"}})
     */
    public function editVideoAction(Video $video){
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirect($this->generateUrl('video_show', array('video_id'=>$video->getId())));
        } else{
            $user = $this->get('security.context')->getToken()->getUser();
            if($user != $video->getUser()){
                return $this->redirect($this->generateUrl('video_show', array('video_id'=>$video->getId())));

            }
        }
        $form = $this->createForm(new VideoEditType, $video);
        $request = $this->get('request');
        if( $request->getMethod() == 'POST'){
            $form->bind($request);
            if($form->isValid()){
                $video->setUrl(preg_replace('/watch\?v=/', "embed/", $video->getUrl()));
                $em = $this->getDoctrine()->getManager();
                $em->persist($video);
                $em->flush();
                return $this->redirect($this->generateUrl('video_show', array('video_id'=>$video->getId())));

            }
        }
        return $this->render('TafrikaPostBundle:Video:edit.html.twig',array(
            'form'=>$form->createView(),
            'video'=>$video
        ));

    }
}