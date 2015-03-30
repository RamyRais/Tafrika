<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 31/12/14
 * Time: 20:25
 */

namespace Tafrika\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Tafrika\PostBundle\Entity\Post ;

class GeneralController extends  Controller{

    public function indexAction(){
        $page = 1;
        $session = $this->get('request')->getSession();
        if($session->get('nsfw')===null){
            $nsfw = 1;
            $session->set('nsfw',$nsfw);
        }else{
            $nsfw = $session->get('nsfw');
        }
        $entityManager = $this->getDoctrine()->getManager();
        $postRepository = $entityManager->getRepository('TafrikaPostBundle:Post');
        $postPerPage = $this->container->getParameter('POST_PER_PAGE');
        $posts = $postRepository->findFresh($postPerPage,$page,$nsfw);

        $user = $this->getUser();
        $votes = null;
        $matchingVotes = array();
        if($user != null) {
            $votes = $entityManager->getRepository('TafrikaPostBundle:Vote')
                                   ->findVoteByUserAndPosts($user, $posts);
            foreach($votes as $vote){
                $matchingVotes[$vote->getPost()->getId()] = $vote->getVote();
            }
        }
        $totalPage = $postRepository->countFreshPosts($nsfw);

        return $this->render('TafrikaPostBundle::index.html.twig',array(
            'posts'=>$posts,
            'matchingVotes'=>$matchingVotes,
            'page' => $page,
            'totalPage'=>ceil($totalPage/$postPerPage)));

    }

    public function loadFollowedUserPostAction($page = 1){
        $request = $this->get('request');
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            $request->getSession()->set('_security.main.target_path', $request->getUri());
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $user = $this->getUser();
        $session = $request->getSession();
        if($session->get('nsfw')===null){
            $nsfw = 1;
            $session->set('nsfw',$nsfw);
        }else{
            $nsfw = $session->get('nsfw');
        }
        $entityManager = $this->getDoctrine()->getManager();
        $rep = $entityManager->getRepository('TafrikaPostBundle:Post');
        $postPerPage = $this->container->getParameter('POST_PER_PAGE');
        $posts = $rep->findFollowedUserPosts($user,$postPerPage,$page,$nsfw);

        $votes = null;
        $matchingVotes = array();
        if($user != null) {
            $votes = $entityManager->getRepository('TafrikaPostBundle:Vote')
                ->findVoteByUserAndPosts($user, $posts);
            foreach($votes as $vote){
                $matchingVotes[$vote->getPost()->getId()] = $vote->getVote();
            }
        }

        $totalPage = $rep->countFollowedUserPosts($user, $nsfw);

        return $this->render('TafrikaPostBundle:Post:followedUsersPosts.html.twig',array(
            'posts'=>$posts,
            'matchingVotes'=>$matchingVotes,
            'page' => $page,
            'totalPage'=>ceil($totalPage/$postPerPage)));
    }

    public function changeNSFWStateAction(){
        $session = $this->get('request')->getSession();
        $nsfw = $session->get('nsfw');
        $nsfw = $nsfw == 1 ? 0 : 1;
        $session->set('nsfw',$nsfw);
        return $this->redirect($this->get('request')->headers->get('referer'));
    }

    public function signalNSFWAction(){
        $request = $this->get('request');
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            $request->getSession()->set('_security.main.target_path', $request->getUri());
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        if($request->isXmlHttpRequest()){
            $entityManager = $this->getDoctrine()->getManager();
            $post_id  = $request->request->get('post_id');
            $post = $entityManager->getRepository('TafrikaPostBundle:Post')->find($post_id);
            if($post != null){
                $post->setSignalNsfw($post->getSignalNsfw()+1);
                if($post->getSignalNsfw() >= Post::MAX_SIGNAL_NUMBER_NSFW){
                    $post->setNSFW(true);
                }
                $entityManager->persist($post);
                $entityManager->flush();
            }else{
                $message = json_encode(array('message' => 'post is null'));
                return new Response($message, 419);
            }
        }
        return new Response("signal added");
    }

    public function signalPornAction(){
        $request = $this->get('request');
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            $request->getSession()->set('_security.main.target_path', $request->getUri());
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        if($request->isXmlHttpRequest()){
            $entityManager = $this->getDoctrine()->getManager();
            $post_id  = $request->request->get('post_id');
            $post = $entityManager->getRepository('TafrikaPostBundle:Post')->find($post_id);
            if($post != null){
                $post->setSignalPorn($post->getSignalPorn()+1);
                $entityManager->persist($post);
                $entityManager->flush();
            }else{
                $message = json_encode(array('message' => 'post is null'));
                return new Response($message, 419);
            }
        }
        return new Response("signal added");
    }

    public function loadHotPostsAction(){
        $page = 1;// 1 is page one
        $session = $this->get('request')->getSession();
        if($session->get('nsfw')===null){
            $nsfw = 1;
            $session->set('nsfw',$nsfw);
        }else{
            $nsfw = $session->get('nsfw');
        }
        $entityManager = $this->getDoctrine()->getManager();
        $postRepository = $entityManager->getRepository('TafrikaPostBundle:Post');
        $postPerPage = $this->container->getParameter('POST_PER_PAGE');
        $posts = $postRepository->findHot($postPerPage, $page, $nsfw);

        $user = $this->getUser();
        $votes = null;
        $matchingVotes = array();
        if($user != null) {
            $votes = $entityManager->getRepository('TafrikaPostBundle:Vote')
                ->findVoteByUserAndPosts($user, $posts);
            foreach($votes as $vote){
                $matchingVotes[$vote->getPost()->getId()] = $vote->getVote();
            }
        }
        $totalPage = $postRepository->countFreshPosts($nsfw);

        return $this->render('TafrikaPostBundle::index.html.twig',array(
            'posts'=>$posts,
            'matchingVotes'=>$matchingVotes,
            'page' => $page,
            'totalPage'=>ceil($totalPage/$postPerPage)));
    }
}