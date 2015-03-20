<?php
/**
 * Created by PhpStorm.
 * User: ramy
 * Date: 10/03/15
 * Time: 14:23
 */

namespace Tafrika\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    public function loadPostsAction(){
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $nsfw = $request->getSession()->get('nsfw');
        $page = $request->request->get('page');
        $postPerPage = $this->container->getParameter('POSTS_PER_LOAD');
        $type = $request->request->get('type');
        switch($type){
            case 'index': $posts = $entityManager->getRepository('TafrikaPostBundle:Post')
                ->findFresh($postPerPage, $page, $nsfw);
            break;
            case 'followed': $posts = $entityManager->getRepository('TafrikaPostBundle:Post')
                ->findFollowedUserPosts($user,$postPerPage,$page,$nsfw);
            break;
            case 'user':
                $userVisitedId = $request->request->get('userId');
                $userVisited = $entityManager->getRepository('TafrikaUserBundle:User')->find($userVisitedId);
                $posts = $entityManager->getRepository('TafrikaPostBundle:Post')
                    ->findUsersPosts($userVisited, $postPerPage, $page, $nsfw);
            break;
            case 'me': $posts = $entityManager->getRepository('TafrikaPostBundle:Post')
                ->findUsersPosts($user, $postPerPage, $page, $nsfw);
            break;
            default: return new JsonResponse();
        }

        $array=array();
        $i=0;

        foreach($posts as $x){
            $array[$i]=$x;
            $i++;

        }

        $vote = $entityManager->getRepository('TafrikaPostBundle:Vote');
        $votes = $vote->findFresh($postPerPage,$page,$user,$array);
        $response = new JsonResponse();
        $response->setData($this->renderView("TafrikaPostBundle:Post:showLoadedPost.html.twig",array(
            'posts'=>$posts,
            'votes'=>$votes)));
        return $response;

    }

    public function loadFriendsPostsAction(){
        $entityManager = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $nsfw = $request->getSession()->get('nsfw');
        $page = $request->request->get('page');
        $postPerPage = $this->container->getParameter('POSTS_PER_LOAD');
        $posts = $entityManager->getRepository('TafrikaPostBundle:Post')
                               ->findFollowedUserPosts($postPerPage, $page, $nsfw);
        $user = $this->getUser();

        $array=array();
        $i=0;

        foreach($posts as $x){
            $array[$i]=$x;
            $i++;

        }

        $vote = $entityManager->getRepository('TafrikaPostBundle:Vote');
        $votes = $vote->findFresh($postPerPage,$page,$user,$array);
        $response = new JsonResponse();
        $response->setData($this->renderView("TafrikaPostBundle:Post:showLoadedPost.html.twig",array(
            'posts'=>$posts,
            'votes'=>$votes)));
        return $response;
    }
}