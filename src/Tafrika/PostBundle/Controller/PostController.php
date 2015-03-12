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
        $comments=$this->getDoctrine()
            ->getRepository('TafrikaPostBundle:Comment')->findBy(array('post' => $post));
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


}