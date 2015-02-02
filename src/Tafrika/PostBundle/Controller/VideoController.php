<?php
/**
 * Created by PhpStorm.
 * User: ramy
 * Date: 01/01/15
 * Time: 22:19
 */

namespace Tafrika\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tafrika\PostBundle\Entity\Video;
use Tafrika\PostBundle\Form\VideoType;
use Tafrika\PostBundle\Form\VideoEditType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class VideoController extends  Controller{

    public function addVideoAction(){
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            $request = $this->get('request');
            $request->getSession()->set('_security.main.target_path', $request->getUri());
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $user = $this->get('security.context')->getToken()->getUser();
        $video = new Video();
        $video->setUser($user);
        $form = $this->createForm(new VideoType, $video);
        $request = $this->get('request');
        if( $request->getMethod() == 'POST'){
            $form->bind($request);
            if($form->isValid()){
                $video->setUrl(preg_replace('/watch\?v=/', "embed/", $video->getUrl()));
                $em = $this->getDoctrine()->getManager();
                $em->persist($video);
                $em->flush();
                return $this->redirect($this->generateUrl('tafrika_index'));

            }
        }
        return $this->render('TafrikaPostBundle:Video:create.html.twig',array(
            'form'=>$form->createView()
        ));
    }

    /**
     * @ParamConverter("video", options={"mapping": {"video_id": "id"}})
     */
    public function showVideoAction(Video $video){
        return $this->render('TafrikaPostBundle:Video:show.html.twig',array(
            'video'=>$video
        ));
    }

    /**
     * @ParamConverter("video", options={"mapping": {"video_id": "id"}})
     */
    public function editVideoAction(Video $video){
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirect($this->generateUrl('video_show', array('video_id'=>$video->getId())));
        } else{
            $user = $this->get('security.context')->getToken()->getUser();
            if($user != $video->getUser()){
                return $this->redirect($this->generateUrl('video_show', array('video_id'=>$video->getId())));

            }
        }
        $form = $this->createForm(new VideoEditType, $video);
        $request = $this->get('request');
        if( $request->getMethod() == 'POST'){
            $form->bind($request);
            if($form->isValid()){
                $video->setUrl(preg_replace('/watch\?v=/', "embed/", $video->getUrl()));
                $em = $this->getDoctrine()->getManager();
                $em->persist($video);
                $em->flush();
                return $this->redirect($this->generateUrl('video_show', array('video_id'=>$video->getId())));

            }
        }
        return $this->render('TafrikaPostBundle:Video:edit.html.twig',array(
            'form'=>$form->createView(),
            'video'=>$video
        ));

    }
}