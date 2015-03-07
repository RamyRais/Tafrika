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

    public function changeNSFWStateAction(){
        $session = $this->get('request')->getSession();
        $nsfw = $session->get('nsfw');
        $nsfw = $nsfw == 1 ? 0 : 1;
        $session->set('nsfw',$nsfw);
        return $this->redirect($this->generateUrl('tafrika_index'));
    }
}