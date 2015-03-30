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
use Symfony\Component\HttpFoundation\Response;
use Tafrika\PostBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Tafrika\PostBundle\Entity\Vote;

class PostController extends Controller{

    /**
     * @ParamConverter("post", options={"mapping": {"post_slug": "slug"}})
     */
    public function showPostAction(Post $post){
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $vote = $entityManager->getRepository('TafrikaPostBundle:Vote')->findOneBy(
            array('post' => $post,
                'user' => $user)
        );

        $matchingVotes = array();
        if($vote != null) {
            $matchingVotes[$vote->getPost()->getId()] = $vote->getVote();
        }
        $commentPerLoad = $this->container->getParameter('COMMENTS_PER_LOAD');
        $comments=$entityManager->getRepository('TafrikaPostBundle:Comment')
                       ->findCommentByPost(1,$commentPerLoad,$post);
        if($post->get_type()=="IMAGE") {
            return $this->render('TafrikaPostBundle:Image:show.html.twig', array(
                'image' => $post, 'comments' => $comments, 'matchingVotes'=>$matchingVotes
            ));
        }else if($post->get_type()=="STATUS"){
            return $this->render('TafrikaPostBundle:Status:show.html.twig',array(
                'status'=>$post,'comments'=>$comments,'matchingVotes'=>$matchingVotes
            ));
        }else if($post->get_type()=="VIDEO"){
            return $this->render('TafrikaPostBundle:Video:show.html.twig',array(
                'video'=>$post,'comments'=>$comments,'matchingVotes'=>$matchingVotes
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
            case 'hot':
                $posts = $entityManager->getRepository('TafrikaPostBundle:Post')
                    ->findHot($postPerPage, $page, $nsfw);
            break;
            default: return new JsonResponse();
        }


        $votes = null;
        $matchingVotes = array();
        if($user != null) {
            $votes = $entityManager->getRepository('TafrikaPostBundle:Vote')
                ->findVoteByUserAndPosts($user, $posts);
            foreach($votes as $vote){
                $matchingVotes[$vote->getPost()->getId()] = $vote->getVote();
            }
        }

        $response = new JsonResponse();
        $response->setData($this->renderView("TafrikaPostBundle:Post:showLoadedPost.html.twig",array(
            'posts'=>$posts,
            'matchingVotes'=>$matchingVotes)));
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

    public function voteUpAction(){
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            return new Response("you must sing in to vote up");
        }
        $request = $this->get('request');
        if($request->isXmlHttpRequest()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $postId = $request->request->get('post_id');
            $post = $entityManager->getRepository('TafrikaPostBundle:Post')->find($postId);
            $vote = $entityManager->getRepository('TafrikaPostBundle:Vote')
                                ->findOneBy(array('post' => $post, 'user' => $user));
            if($vote == null){ //vote doesn't exist in the database
                $newVote = new Vote(); //so we create one
                $newVote->setPost($post);
                $newVote->setUser($user);
                $newVote->setVote(1);
                $entityManager->persist($newVote); // and persist it
                $post->setLikes($post->getLikes() + 1); //update the like since we don't calculate it
            }else {
                $voteValue = $vote->getVote();
                switch ($voteValue) {
                    case 1 :
                        $post->setLikes($post->getLikes() - 1);
                        $entityManager->remove($vote);
                    break;
                    case -1 :
                        $post->setLikes($post->getLikes() + 2);
                        $vote->setVote(1);
                        break;
                }
            }
            $entityManager->flush();
            return new Response($post->getLikes());
        }
        return new Response("request is not ajax");
    }

    public function voteDownAction(){
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            return new Response("you must sing in to vote up");
        }
        $request = $this->get('request');
        if($request->isXmlHttpRequest()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $postId = $request->request->get('post_id');
            $post = $entityManager->getRepository('TafrikaPostBundle:Post')->find($postId);
            $vote = $entityManager->getRepository('TafrikaPostBundle:Vote')
                ->findOneBy(array('post' => $post, 'user' => $user));
            if($vote == null){ //vote doesn't exist in the database
                $newVote = new Vote(); //so we create one
                $newVote->setPost($post);
                $newVote->setUser($user);
                $newVote->setVote(-1);
                $entityManager->persist($newVote); // and persist it
                $post->setLikes($post->getLikes() - 1); //update the like since we don't calculate it
            }else {
                $voteValue = $vote->getVote();
                switch ($voteValue) {
                    case -1 :
                        $post->setLikes($post->getLikes() + 1);
                        $entityManager->remove($vote);
                        break;
                    case 1 :
                        $post->setLikes($post->getLikes() - 2);
                        $vote->setVote(-1);
                        break;
                }
            }
            $entityManager->flush();
            return new Response($post->getLikes());
        }
        return new Response("request is not ajax");
    }
}