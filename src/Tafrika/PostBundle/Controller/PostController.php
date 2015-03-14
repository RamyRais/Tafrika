<?php
/**
 * Created by PhpStorm.
 * User: ramy
 * Date: 10/03/15
 * Time: 14:23
 */

namespace Tafrika\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tafrika\PostBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PostController extends Controller{

    /**
     * @ParamConverter("post", options={"mapping": {"post_id": "id"}})
     */
    public function showPostAction(Post $post){
        $user = $this->get('security.context')->getToken()->getUser();
        $vote = $this->getDoctrine()->getRepository('TafrikaPostBundle:Vote')->findOneBy(
            array('post' => $post,
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
        $commentPerLoad = $this->container->getParameter('COMMENTS_PER_LOAD');
        $comments=$this->getDoctrine()->getRepository('TafrikaPostBundle:Comment')
                       ->findCommentByPost(1,$commentPerLoad,$post);
        if($post->get_type()=="IMAGE") {
            return $this->render('TafrikaPostBundle:Image:show.html.twig', array(
                'image' => $post, 'comments' => $comments, 'user_vote' => $user_vote
            ));
        }else if($post->get_type()=="STATUS"){
            return $this->render('TafrikaPostBundle:Status:show.html.twig',array(
                'status'=>$post,'comments'=>$comments,'user_vote'=>$user_vote
            ));
        }else if($post->get_type()=="VIDEO"){
            return $this->render('TafrikaPostBundle:Video:show.html.twig',array(
                'video'=>$post,'comments'=>$comments,'user_vote'=>$user_vote
            ));
        }

    }

    /**
     * @ParamConverter("post", options={"mapping": {"post_id": "id"}})
     */
    public function deletePostAction(Post $post){
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirect($this->generateUrl('post_show', array('post_id'=>$post->getId())));
        } else{
            $user = $this->get('security.context')->getToken()->getUser();
            if($user != $post->getUser()){
                return $this->redirect($this->generateUrl('post_show', array('post_id'=>$post->getId())));
            }
        }
        $entityManager = $this->getDoctrine()->getManager();
        $votes = $entityManager->getRepository('TafrikaPostBundle:Vote')
                               ->findBy(array('post' => $post));
        foreach( $votes as $vote){
            $entityManager->remove($vote);
        }
        $entityManager->remove($post);
        $entityManager->flush();
        return $this->redirect($this->generateUrl('fos_user_profile_show'));

    }
}