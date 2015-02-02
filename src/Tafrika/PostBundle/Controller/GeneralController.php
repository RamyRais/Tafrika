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
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('TafrikaPostBundle:Post');
        $postPerPage = $this->container->getParameter('postPerPage');
        $posts = $rep->findFresh($postPerPage,$page);
        $response = new Response();
        $response->headers->set('X-Frame-Options','Allow-From http://www.youtube.com');
        $response->headers->set('X-Frame-Options','GOFORIT');
        $engine = $this->container->get('templating');
        $content = $engine->render('TafrikaPostBundle::index.html.twig',array(
            'posts'=>$posts,
            'page' => $page,
            'pageNumber'=>ceil(count($posts)/$postPerPage)));
        $response->setContent($content);
        return $response;
    }
}