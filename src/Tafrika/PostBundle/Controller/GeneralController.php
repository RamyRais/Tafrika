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

    public function indexAction($page = 1){
        $session = $this->get('request')->getSession();
        if($session->get('nsfw')===null){
            $nsfw = 1;
            $session->set('nsfw',$nsfw);
        }else{
            $nsfw = $session->get('nsfw');
        }
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('TafrikaPostBundle:Post');
        $postPerPage = $this->container->getParameter('postPerPage');
        $posts = $rep->findFresh($postPerPage,$page,$nsfw);
        $user = $this->get('security.context')->getToken()->getUser();


        $array=array();
        $i=0;

        foreach($posts as $x){
            $array[$i]=$x;
            $i++;

        }

        $vote = $em->getRepository('TafrikaPostBundle:Vote');
        $votes=$vote->findFresh($postPerPage,$page,$user,$array);
        //die($votes->getIterator()->current()->getTitle());

        $response = new Response();
        $response->headers->set('X-Frame-Options','Allow-From http://www.youtube.com');
        $response->headers->set('X-Frame-Options','GOFORIT');
        $engine = $this->container->get('templating');
        $content = $engine->render('TafrikaPostBundle::index.html.twig',array(
            'posts'=>$posts,
            'votes'=>$votes,
            'page' => $page,
            'pageNumber'=>ceil(count($posts)/$postPerPage)));
        $response->setContent($content);
        return $response;
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
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('TafrikaPostBundle:Post');
        $postPerPage = $this->container->getParameter('postPerPage');
        $posts = $rep->findFollowedUserPosts($user,$postPerPage,$page,$nsfw);

        $array=array();
        $i=0;
        foreach($posts as $x){
            $array[$i]=$x;
            $i++;

        }

        $vote = $em->getRepository('TafrikaPostBundle:Vote');
        $votes=$vote->findFresh($postPerPage,$page,$user,$array);
        //die($votes->getIterator()->current()->getTitle());

        $response = new Response();
        $response->headers->set('X-Frame-Options','Allow-From http://www.youtube.com');
        $response->headers->set('X-Frame-Options','GOFORIT');
        $engine = $this->container->get('templating');
        $content = $engine->render('TafrikaPostBundle::index.html.twig',array(
            'posts'=>$posts,
            'votes'=>$votes,
            'page' => $page,
            'pageNumber'=>ceil(count($posts)/$postPerPage)));
        $response->setContent($content);
        return $response;
    }

    public function changeNSFWStateAction(){
        $session = $this->get('request')->getSession();
        $nsfw = $session->get('nsfw');
        $nsfw = $nsfw == 1 ? 0 : 1;
        $session->set('nsfw',$nsfw);
        return $this->redirect($this->generateUrl('tafrika_index'));
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
}