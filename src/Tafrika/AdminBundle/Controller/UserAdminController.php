<?php
/**
 * Created by PhpStorm.
 * User: ramy
 * Date: 02/06/15
 * Time: 23:41
 */
namespace Tafrika\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserAdminController extends Controller{


    public function listUserAction($page){
        $userPerPage = 4;
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('TafrikaUserBundle:User');
        $users = $repository->getUserList($userPerPage,$page);
        return $this->render('TafrikaAdminBundle:user:list.html.twig',
            array( 'users' => $users,
                'page' => $page,
                'pageNumber'=>ceil(count($users)/$userPerPage)));
    }
}