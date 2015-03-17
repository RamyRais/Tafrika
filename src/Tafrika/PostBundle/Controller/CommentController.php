<?php
/**
 * Created by PhpStorm.
 * User: ramy
 * Date: 16/02/15
 * Time: 15:23
 */

namespace Tafrika\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Tafrika\PostBundle\Entity\Comment;
use Tafrika\PostBundle\Entity\Post;
use Tafrika\PostBundle\Form\CommentType;

class CommentController extends Controller{

    public function addCommentAction(){
        $request = $this->get('request');
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            $request->getSession()->set('_security.main.target_path', $request->getUri());
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $user = $this->getUser();
        if($request->isXmlHttpRequest()) {
            $post_id = $request->request->get('post_id');
            $entityManager = $this->getDoctrine()->getManager();
            $postRepository = $entityManager->getRepository('TafrikaPostBundle:Post');
            $post = $postRepository->find($post_id);
            $comment = new Comment();
            $comment->setUser($user);
            $comment->setPost($post);
            $form = $this->createForm(new CommentType, $comment);

            if ($request->getMethod() == 'POST') {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $entityManager->persist($comment);
                    $entityManager->flush();
                    $response = new JsonResponse();
                    $response->setData($this->renderView("TafrikaPostBundle:Comment:showComment.html.twig",array(
                        'comment'=>$comment)));
                    return $response;
                } else {
                    $message = json_encode(array('message' => 'form is not valid :('));
                    return new Response($message, 419);
                }
            }
        }
        return new Response("comment added");
    }

    public function removeCommentAction(){
        $request = $this->get('request');
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            $request->getSession()->set('_security.main.target_path', $request->getUri());
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $user = $this->getUser();
        if($request->isXmlHttpRequest()){
            $entityManager = $this->getDoctrine()->getManager();
            $comment_id  = $request->request->get('comment_id');
            $comment = $entityManager->getRepository('TafrikaPostBundle:Comment')->find($comment_id);
            if($comment->getUser() == $user){
                $entityManager->remove($comment);
                $entityManager->flush();
            }else{
                $message = json_encode(array('message' => 'you don\'t have the right to delete others user\' comment'));
                return new Response($message, 419);
            }

        }
        return new Response("comment deleted");
    }

    /**
     * @param $post
     * @return Response
     * @ParamConverter("post", options={"mapping": {"post_id": "id"}})
     */
    public function renderAddCommentAction(Post $post){
        $comment = new Comment();
        $form = $this->createForm(new CommentType, $comment);
        return $this->render("TafrikaPostBundle:Comment:create.html.twig",array(
            'form'=> $form->createView(),
            'post'=>$post
        ));
    }

    public function loadCommentsOfPostAction(){
        $entityManager = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $post_id = $request->request->get('post_id');
        $post = $entityManager->getRepository('TafrikaPostBundle:Post')->find($post_id);
        $page = $request->request->get('page');
        $commentPerPage = $this->container->getParameter('COMMENTS_PER_LOAD');
        $comments = $entityManager->getRepository('TafrikaPostBundle:Comment')
                                  ->findCommentByPost($page,$commentPerPage,$post);
        $response = new JsonResponse();
        $response->setData($this->renderView("TafrikaPostBundle:Comment:showCommentsOfPost.html.twig",array(
            "comments"=>$comments)));
        return $response;

    }

}