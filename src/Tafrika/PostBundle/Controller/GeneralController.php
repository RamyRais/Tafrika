<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 31/12/14
 * Time: 20:25
 */

namespace Tafrika\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
class GeneralController extends  Controller{

    public function indexAction(){
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('TafrikaPostBundle:Post');
        $posts = $rep->findAll();
        return $this->render('TafrikaPostBundle::index.html.twig',array(
            'posts'=>$posts));
    }
}